<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Varbox\Contracts\RevisionModelContract;
use Varbox\Traits\CanCrud;
use Varbox\Traits\CanDraft;
use Varbox\Traits\CanDuplicate;
use Varbox\Traits\CanRevision;
use Varbox\Traits\CanSoftDelete;
use Varbox\Contracts\BlockModelContract;
use Varbox\Filters\BlockFilter;
use Varbox\Models\Block;
use Varbox\Requests\BlockRequest;
use Varbox\Sorts\BlockSort;

class BlocksController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud, CanDraft, CanRevision, CanDuplicate, CanSoftDelete;

    /**
     * @var BlockModelContract
     */
    protected $model;

    /**
     * BlocksController constructor.
     *
     * @param BlockModelContract $model
     */
    public function __construct(BlockModelContract $model)
    {
        $this->model = $model;

        view()->share('_model', $this->model);
    }

    /**
     * @param Request $request
     * @param BlockFilter $filter
     * @param BlockSort $sort
     * @return \Illuminate\View\View
     * @throws Exception
     */
    public function index(Request $request, BlockFilter $filter, BlockSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $query = $this->model->query();

            if ($request->filled('trashed')) {
                switch ($request->query('trashed')) {
                    case 1:
                        $query->onlyTrashed();
                        break;
                    case 2:
                        $query->withoutTrashed();
                        break;
                }
            } else {
                $query->withTrashed();
            }

            if ($request->filled('drafted')) {
                switch ($request->query('drafted')) {
                    case 1:
                        $query->withoutDrafts();
                        break;
                    case 2:
                        $query->onlyDrafts();
                        break;
                }
            } else {
                $query->withDrafts();
            }

            $this->items = $query
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.base.crud.per_page', 10));

            $this->title = 'Blocks';
            $this->view = view('varbox::admin.blocks.index');
            $this->vars = [
                'types' => $this->typesToArray(),
            ];
        });
    }

    /**
     * @param string|null $type
     * @return \Illuminate\View\View
     * @throws Exception
     */
    public function create($type = null)
    {
        if (!$type || !array_key_exists($type, (array)config('varbox.blocks.types', []))) {
            meta()->set('title', 'Admin - Add Block');

            return view('varbox::admin.blocks.init')->with([
                'title' => 'Add Block',
                'types' => $this->typesToArray(),
                'images' => $this->imagesToArray(),
            ]);
        }

        $this->guardAgainstUncreatedBlock($type);

        return $this->_create(function () use ($type) {
            $this->title = 'Add Block';
            $this->view = view('varbox::admin.blocks.add');
            $this->vars = [
                'type' => $type,
                'includeAdminView' => !$this->isEmptyAdminView($type)
            ];
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function store(Request $request)
    {
        app(config('varbox.bindings.form_requests.block_form_request', BlockRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.blocks.index');
        }, $request);
    }

    /**
     * @param BlockModelContract $block
     * @return \Illuminate\View\View
     * @throws Exception
     */
    public function edit(BlockModelContract $block)
    {
        return $this->_edit(function () use ($block) {
            $this->item = $block;
            $this->title = 'Edit Block';
            $this->view = view('varbox::admin.blocks.edit');
            $this->vars = [
                'includeAdminView' => !$this->isEmptyAdminView($this->item->type)
            ];
        });
    }

    /**
     * @param Request $request
     * @param BlockModelContract $block
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function update(Request $request, BlockModelContract $block)
    {
        app(config('varbox.bindings.form_requests.block_form_request', BlockRequest::class));

        return $this->_update(function () use ($request, $block) {
            $this->item = $block;
            $this->redirect = redirect()->route('admin.blocks.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param BlockModelContract $block
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function destroy(BlockModelContract $block)
    {
        return $this->_destroy(function () use ($block) {
            $this->item = $block;
            $this->redirect = redirect()->route('admin.blocks.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function get(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $request->validate([
            'blockable_id' => 'required|numeric',
            'blockable_type' => 'required',
        ]);

        $class = $request->input('blockable_type');
        $id = $request->input('blockable_id');
        $model = $class::withoutGlobalScopes()->findOrFail($id);

        if ($request->filled('revision') && ($revision = app(RevisionModelContract::class)->find((int)$request->input('revision')))) {
            DB::beginTransaction();

            $model = $revision->revisionable;
            $model->rollbackToRevision($revision);
        }

        return response()->json([
            'status' => true,
            'html' => view('varbox::helpers.block.partials.blocks')->with([
                'model' => $model,
                'blocks' => $model->blocks,
                'locations' => $model->getBlockLocations(),
                'revision' => $revision ?? null,
                'disabled' => json_decode($request->input('disabled')) ? true : false,
            ])->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function row(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $request->validate([
            'block_id' => 'required|numeric',
        ]);

        $block = $this->model->findOrFail($request->input('block_id'));

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $block->getKey(),
                'name' => $block->name ?: 'N/A',
                'type' => $block->type ?: 'N/A',
                'url' => route('admin.blocks.edit', $block->getKey()),
            ],
        ]);
    }

    /**
     * @param string $type
     * @throws Exception
     */
    protected function guardAgainstUncreatedBlock($type)
    {
        if (
            !file_exists(app_path("Blocks/{$type}/Composer.php")) ||
            !file_exists(app_path("Blocks/{$type}/Views/admin.blade.php")) ||
            !file_exists(app_path("Blocks/{$type}/Views/front.blade.php"))
        ) {
            throw new Exception(
                'The block type "' . $type . '" does not exist!' . PHP_EOL .
                'Please run "php artisan varbox:make-block ' . $type . '" and try again.'
            );
        }
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function isEmptyAdminView($type)
    {
        return empty(str_replace(
            ["\n", " "], '', file_get_contents(
                app_path("Blocks/{$type}/Views/admin.blade.php")
            )
        ));
    }

    /**
     * Get the formatted block types for a select.
     * Final format will be: [type => label].
     *
     * @return array
     */
    protected function typesToArray()
    {
        $types = [];

        foreach ((array)config('varbox.blocks.types', []) as $type => $options) {
            $types[$type] = $options['label'];
        }

        return $types;
    }

    /**
     * Get the formatted block types for a select.
     * Final format will be: [type => image].
     *
     * @return array
     */
    protected function imagesToArray()
    {
        $images = [];

        foreach ((array)config('varbox.blocks.types', []) as $type => $options) {
            $images[$type] = $options['preview_image'];
        }

        return $images;
    }

    /**
     * Get the title to be used on the revision view.
     *
     * The title will be used in:
     * - page title
     * - meta title
     *
     * @return string
     */
    protected function revisionPageTitle(): string
    {
        return 'Block Revision';
    }

    /**
     * Get the blade view to be rendered as the revision view.
     *
     * @return string
     */
    protected function revisionView(): string
    {
        return 'varbox::admin.blocks.edit';
    }

    /**
     * Get additional view variables to be assigned to the revision view.
     * If no additional variables are needed, return an empty array.
     *
     * @param Model $revisionable
     * @return array
     */
    protected function revisionViewVariables(Model $revisionable): array
    {
        return [
            'includeAdminView' => !$this->isEmptyAdminView($revisionable->type)
        ];
    }

    /**
     * Get the model to be drafted.
     *
     * @return string
     */
    protected function draftModel(): string
    {
        return config('varbox.bindings.models.block_model', Block::class);
    }

    /**
     * Get the form request to validate the draft upon.
     *
     * @return string|null
     */
    protected function draftRequest(): ?string
    {
        return config('varbox.bindings.form_requests.block_form_request', BlockRequest::class);
    }

    /**
     * Get the url to redirect after drafting/publishing.
     *
     * @param Model $model
     * @return string
     */
    protected function draftRedirectTo(Model $model): string
    {
        return route('admin.blocks.edit', $model->getKey());
    }

    /**
     * Get the model to be duplicated.
     *
     * @return Model
     */
    protected function duplicateModel(): string
    {
        return config('varbox.bindings.models.block_model', Block::class);
    }

    /**
     * Get the url to redirect to after the duplication.
     *
     * @param Model $duplicate
     * @return string
     */
    protected function duplicateRedirectTo(Model $duplicate): string
    {
        return route('admin.blocks.edit', $duplicate->getKey());
    }

    /**
     * Get the model to be soft deleted.
     *
     * @return Model
     */
    protected function softDeleteModel(): string
    {
        return config('varbox.bindings.models.block_model', Block::class);
    }

    /**
     * Get the url to redirect to after the soft deletion.
     *
     * @return string
     */
    protected function softDeleteRedirectTo(): string
    {
        return route('admin.blocks.index');
    }
}
