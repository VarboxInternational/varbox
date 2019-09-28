<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\SchemaHelperContract;
use Varbox\Contracts\SchemaModelContract;
use Varbox\Filters\SchemaFilter;
use Varbox\Helpers\SchemaHelper;
use Varbox\Requests\SchemaRequest;
use Varbox\Sorts\SchemaSort;
use Varbox\Traits\CanCrud;

class SchemaController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var SchemaModelContract
     */
    protected $model;

    /**
     * @var SchemaHelperContract
     */
    protected $helper;

    /**
     * @param SchemaModelContract $model
     */
    public function __construct(SchemaModelContract $model, SchemaHelperContract $helper)
    {
        $this->model = $model;
        $this->helper = $helper;
    }

    /**
     * @param Request $request
     * @param SchemaFilter $filter
     * @param SchemaSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, SchemaFilter $filter, SchemaSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.crud.per_page', 30));

            $this->title = 'Schema';
            $this->view = view('varbox::admin.schema.index');
            $this->vars = [
                'types' => $this->model->getTypes(),
                'targets' => (array)config('varbox.schema.targets', []),
            ];
        });
    }

    /**
     * @param string|null $type
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create($type = null)
    {
        if (is_null($type) || !array_key_exists($type, $this->model->getTypes())) {
            meta()->set('title', 'Admin - Add Schema - Choose Type');

            return view('varbox::admin.schema.init')->with([
                'title' => 'Add Schema',
                'types' => $this->model->getTypes(),
            ]);
        }

        return $this->_create(function () use ($type) {
            $this->title = 'Add Schema';
            $this->view = view('varbox::admin.schema.add');
            $this->vars = [
                'type' => $type,
                'targets' => (array)config('varbox.schema.targets', []),
            ];
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        app(config('varbox.bindings.form_requests.schema_form_request', SchemaRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.schema.index');
        }, $request);
    }

    /**
     * @param SchemaModelContract $schema
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(SchemaModelContract $schema)
    {
        return $this->_edit(function () use ($schema) {
            try {
                $model = app($schema->target)->firstOrFail();
                $code = $this->helper->renderSingle($schema, $model);
            } catch (ModelNotFoundException $e) {
                $code = null;
            } catch (Exception $e) {
                $code = null;
            }

            $this->item = $schema;
            $this->title = 'Edit Schema';
            $this->view = view('varbox::admin.schema.edit');
            $this->vars = [
                'types' => $this->model->getTypes(),
                'targets' => (array)config('varbox.schema.targets', []),
                'schemaCode' => $code,
                'articleTypes' => $this->model->articleSchemaTypes(),
                'eventTypes' => $this->model->eventSchemaTypes(),
                'localBusinessTypes' => $this->model->localBusinessSchemaTypes(),
                'softwareApplicationTypes' => $this->model->softwareApplicationSchemaTypes(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param SchemaModelContract $schema
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, SchemaModelContract $schema)
    {
        app(config('varbox.bindings.form_requests.schema_form_request', SchemaRequest::class));

        return $this->_update(function () use ($request, $schema) {
            $this->item = $schema;
            $this->redirect = redirect()->route('admin.schema.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param SchemaModelContract $schema
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(SchemaModelContract $schema)
    {
        return $this->_destroy(function () use ($schema) {
            $this->item = $schema;
            $this->redirect = redirect()->route('admin.schema.index');

            $this->item->delete();
        });
    }
}
