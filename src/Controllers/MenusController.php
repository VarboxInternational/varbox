<?php

namespace Varbox\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Varbox\Traits\CanCrud;
use Varbox\Contracts\MenuModelContract;
use Varbox\Filters\MenuFilter;
use Varbox\Requests\MenuRequest;
use Varbox\Sorts\MenuSort;

class MenusController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var MenuModelContract
     */
    protected $model;

    /**
     * MenusController constructor.
     *
     * @param MenuModelContract $model
     */
    public function __construct(MenuModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function locations()
    {
        meta()->set('title', 'Admin - Menu Locations');

        return view('varbox::admin.menus.locations')->with([
            'title' => 'Menu Locations',
            'locations' => $this->locationsToArray()
        ]);
    }

    /**
     * @param Request $request
     * @param MenuFilter $filter
     * @param MenuSort $sort
     * @param string $location
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, MenuFilter $filter, MenuSort $sort, $location)
    {
        cache()->forget('first_tree_load');

        return $this->_index(function () use ($request, $filter, $sort, $location) {
            $this->items = new Collection;
            $this->title = 'Menus';
            $this->view = view('varbox::admin.menus.index');
            $this->vars = [
                'location' => $location,
                'types' => $this->typesToArray(),
            ];
        });
    }

    /**
     * @param string $location
     * @param MenuModelContract $menuParent
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create($location, MenuModelContract $menuParent = null)
    {
        return $this->_create(function () use ($location, $menuParent) {
            $this->title = 'Add Menu';
            $this->view = view('varbox::admin.menus.add');
            $this->vars = [
                'location' => $location,
                'parent' => $menuParent,
                'types' => $this->typesToArray(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param string $location
     * @param MenuModelContract $menuParent
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request, $location, MenuModelContract $menuParent = null)
    {
        $request = $this->initRequest($location);

        return $this->_store(function () use ($request, $location, $menuParent) {
            $this->item = $this->model->create($request->all(), $menuParent ?: null);
            $this->redirect = redirect()->route('admin.menus.index', $location);
        }, $request);
    }

    /**
     * @param string $location
     * @param MenuModelContract $menu
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit($location, MenuModelContract $menu)
    {
        $this->guardAgainstWrongLocation($menu, $location);

        return $this->_edit(function () use ($location, $menu) {
            $this->item = $menu;
            $this->title = 'Edit Menu';
            $this->view = view('varbox::admin.menus.edit');
            $this->vars = [
                'location' => $location,
                'types' => $this->typesToArray(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param string $location
     * @param MenuModelContract $menu
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, $location, MenuModelContract $menu)
    {
        $this->guardAgainstWrongLocation($menu, $location);

        $request = $this->initRequest($location);

        return $this->_update(function () use ($location, $menu, $request) {
            $this->item = $menu;
            $this->redirect = redirect()->route('admin.menus.index', $location);

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param string $location
     * @param MenuModelContract $menu
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($location, MenuModelContract $menu)
    {
        return $this->_destroy(function () use ($location, $menu) {
            $this->item = $menu;
            $this->redirect = redirect()->route('admin.menus.index', $location);

            $this->item->delete();
        });
    }

    /**
     * @return string
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function route()
    {
        $routes = $this->model->getRoutes();
        $result = [];

        foreach ($routes as $route) {
            $result[] = [
                'value' => $route->getName(),
                'name' => $route->getName(),
            ];
        }

        return response()->json([
            'status' => true,
            'attributes' => $result,
        ]);
    }

    /**
     * @param string $type
     * @return string
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function entity($type)
    {
        $class = config('varbox.menus.types', [])[$type] ?? null;
        $model = $class && class_exists($class) ? app($class) : null;
        $result = [];

        if (!$model) {
            return response()->json([
                'status' => false,
            ], 400);
        }

        foreach ($model->get() as $item) {
            $result[] = [
                'value' => $item->id,
                'name' => $item->name,
            ];
        }

        return response()->json([
            'status' => true,
            'attributes' => $result,
        ]);
    }

    /**
     * @param string|null $location
     * @return mixed
     */
    protected function initRequest($location = null)
    {
        return app(config(
            'varbox.bindings.form_requests.menu_form_request', MenuRequest::class
        ))->merged($location);
    }

    /**
     * @param MenuModelContract $menu
     * @param string $location
     */
    protected function guardAgainstWrongLocation(MenuModelContract $menu, $location)
    {
        if ($menu->location != $location) {
            abort(404);
        }
    }

    /**
     * Get the formatted menu locations for a select.
     * Final format will be: [locatio  => title-cased location].
     *
     * @return array
     */
    protected function locationsToArray()
    {
        $locations = [];

        foreach ((array)config('varbox.menus.locations', []) as $location) {
            $locations[$location] = Str::title(str_replace(['_', '-', '.'], ' ', $location));
        }

        return $locations;
    }

    /**
     * Get the formatted types for a select.
     * Final format will be: [type => title-cased type].
     *
     * @return array
     */
    protected function typesToArray()
    {
        $types = [
            'url' => 'URL',
            'route' => 'Route',
        ];

        foreach (array_keys((array)config('varbox.menus.types', [])) as $type) {
            $types[$type] = Str::title(str_replace(['_', '-', '.'], ' ', $type));
        }

        return $types;
    }
}
