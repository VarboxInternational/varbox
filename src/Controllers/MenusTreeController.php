<?php

namespace Varbox\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Varbox\Filters\MenuFilter;
use Varbox\Sorts\MenuSort;

class MenusTreeController extends MenusController
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fixTree()
    {
        $this->model->doNotLogActivity()->fixTree();

        flash()->success('Tree items fixed successfully!');

        return back();
    }

    /**
     * @param string $location
     * @param int|null $parent
     * @return array
     * @throws \Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function loadNodes($location, $parent = null)
    {
        $data = [];
        $query = $this->model->whereLocation($location);

        if ($parent) {
            $items = $query->whereDescendantOf($parent)->defaultOrder()->get()->toTree();
        } elseif (cache()->has('first_tree_load')) {
            cache()->forget('first_tree_load');

            $items = $query->whereIsRoot()->defaultOrder()->get();
        } else {
            cache()->forever('first_tree_load', true);

            $data[] = [
                'id' => 'root_id',
                'text' => Str::title($location) . ' Menu',
                'children' => true,
                'type' => 'root',
                'icon' => 'fa fa-bars'
            ];
        }

        if (isset($items)) {
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->id,
                    'text' => $item->name,
                    'children' => $item->children->count() > 0 ? true : false,
                    'type' => 'child',
                    'icon' => 'fa fa-link'
                ];
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param MenuFilter $filter
     * @param MenuSort $sort
     * @param string $location
     * @param int|null $parent
     * @return \Illuminate\View\View
     */
    public function listItems(Request $request, MenuFilter $filter, MenuSort $sort, $location, $parent = null)
    {
        $q = $this->model->whereLocation($location);
        $query = $this->model->whereLocation($location)
            ->filtered($request->all(), $filter)
            ->sorted($request->all(), $sort)
            ->orderBy($this->model->getLftName());

        if ($parent = $q->find($parent)) {
            $query->ofParent($parent);
        } else {
            $query->whereIsRoot();
        }

        return view('varbox::admin.menus._table')->with([
            'items' => $query->get(),
            'parent' => $parent,
            'location' => $location,
            'types' => $this->typesToArray(),
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function sortItems(Request $request)
    {
        $tree = [];
        $branch = head($request->input('tree'))['children'];

        $this->rebuildBranch($branch, $tree);

        return $this->model->rebuildTree($tree);
    }

    /**
     * @param array $items
     * @param array $array
     * @return void
     */
    private function rebuildBranch(array $items, array &$array)
    {
        foreach ($items as $item) {
            if (!is_numeric($item['id'])) {
                continue;
            }

            $_item = [
                'id' => $item['id'],
                'name' => $item['text'],
            ];

            if (isset($item['children']) && is_array($item['children'])) {
                $_item['children'] = [];

                $this->rebuildBranch($item['children'], $_item['children']);
            }

            $array[] = $_item;
        }
    }
}
