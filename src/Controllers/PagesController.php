<?php

namespace Varbox\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Varbox\Contracts\PageFilterContract;
use Varbox\Contracts\PageSortContract;
use Varbox\Traits\CanCrud;
use Varbox\Traits\CanDraft;
use Varbox\Traits\CanDuplicate;
use Varbox\Traits\CanPreview;
use Varbox\Traits\CanRevision;
use Varbox\Contracts\PageModelContract;
use Varbox\Models\Page;
use Varbox\Requests\PageRequest;

class PagesController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud, CanDraft, CanRevision, CanDuplicate, CanPreview;

    /**
     * @var PageModelContract
     */
    protected $model;

    /**
     * PagesController constructor.
     *
     * @param PageModelContract $model
     */
    public function __construct(PageModelContract $model)
    {
        $this->model = $model;

        view()->share('_model', $this->model);
    }

    /**
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        return $this->_index(function () {
            $this->items = new Collection;
            $this->title = 'Pages';
            $this->view = view('varbox::admin.pages.index');
            $this->vars = [
                'types' => $this->typesToArray(),
            ];
        });
    }

    /**
     * @param PageModelContract|null $pageParent
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create(PageModelContract $pageParent = null)
    {
        return $this->_create(function () use ($pageParent) {
            $this->title = 'Add Page';
            $this->view = view('varbox::admin.pages.add');
            $this->vars = [
                'parent' => $pageParent,
                'types' => $this->typesToArray(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param PageModelContract $pageParent
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request, PageModelContract $pageParent = null)
    {
        app(config('varbox.bindings.form_requests.page_form_request', PageRequest::class));

        return $this->_store(function () use ($request, $pageParent) {
            $this->item = $this->model->create($request->all(), $pageParent ?: null);
            $this->redirect = redirect()->route('admin.pages.index');
        }, $request);
    }

    /**
     * @param PageModelContract $page
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(PageModelContract $page)
    {
        return $this->_edit(function () use ($page) {
            $this->item = $page;
            $this->title = 'Edit Page';
            $this->view = view('varbox::admin.pages.edit');
            $this->vars = [
                'types' => $this->typesToArray(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param PageModelContract $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, PageModelContract $page)
    {
        app(config('varbox.bindings.form_requests.page_form_request', PageRequest::class));

        return $this->_update(function () use ($page, $request) {
            $this->item = $page;
            $this->redirect = redirect()->route('admin.pages.index');

            $this->item->update($request->all());
            $this->item->saveBlocks($request->input('blocks') ?: []);
        }, $request);
    }

    /**
     * @param PageModelContract $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(PageModelContract $page)
    {
        return $this->_destroy(function () use ($page) {
            $this->item = $page;
            $this->redirect = redirect()->route('admin.pages.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param PageFilterContract $filter
     * @param PageSortContract $sort
     * @return mixed
     */
    public function csv(Request $request, PageFilterContract $filter, PageSortContract $sort)
    {
        $items = $this->model->withDrafts()
            ->filtered($request->all(), $filter)
            ->sorted($request->all(), $sort)
            ->orderBy($this->model->getLftName())
            ->get();

        return $this->model->exportToCsv($items);
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
        return 'Page Revision';
    }

    /**
     * Get the blade view to be rendered as the revision view.
     *
     * @return string
     */
    protected function revisionView(): string
    {
        return 'varbox::admin.pages.edit';
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
            'types' => $this->typesToArray(),
        ];
    }

    /**
     * Get the model to be drafted.
     *
     * @return string
     */
    protected function draftModel(): string
    {
        return config('varbox.bindings.models.page_model', Page::class);
    }

    /**
     * Get the form request to validate the draft upon.
     *
     * @return string|null
     */
    protected function draftRequest(): ?string
    {
        return config('varbox.bindings.form_requests.page_form_request', PageRequest::class);
    }

    /**
     * Get the url to redirect after drafting/publishing.
     *
     * @param Model $model
     * @return string
     */
    protected function draftRedirectTo(Model $model): string
    {
        return route('admin.pages.edit', $model->getKey());
    }

    /**
     * Get the model to be duplicated.
     *
     * @return Model
     */
    protected function duplicateModel(): string
    {
        return config('varbox.bindings.models.page_model', Page::class);
    }

    /**
     * Get the url to redirect to after the duplication.
     *
     * @param Model $duplicate
     * @return string
     */
    protected function duplicateRedirectTo(Model $duplicate): string
    {
        return route('admin.pages.edit', $duplicate->getKey());
    }

    /**
     * Get the model to be previewed.
     *
     * @return string
     */
    protected function previewModel(): string
    {
        return config('varbox.bindings.models.page_model', Page::class);
    }

    /**
     * Get the controller where to dispatch the preview.
     *
     * @param Model $model
     * @return Model
     */
    protected function previewController(Model $model): string
    {
        return config('varbox.pages.types.' . $model->type . '.controller');
    }

    /**
     * Get the action where to dispatch the preview.
     *
     * @param Model $model
     * @return Model
     */
    protected function previewAction(Model $model): string
    {
        return config('varbox.pages.types.' . $model->type . '.action');
    }

    /**
     * Get the form request to validate the preview upon.
     *
     * @return string|null
     */
    protected function previewRequest(): ?string
    {
        return config('varbox.bindings.form_requests.page_form_request', PageRequest::class);
    }

    /**
     * Get the formatted types for a select.
     * Final format will be: [type => title-cased type].
     *
     * @return array
     */
    protected function typesToArray()
    {
        $types = [];

        foreach (array_keys((array)config('varbox.pages.types', [])) as $type) {
            $types[$type] = Str::title(str_replace(['_', '-', '.'], ' ', $type));
        }

        return $types;
    }
}
