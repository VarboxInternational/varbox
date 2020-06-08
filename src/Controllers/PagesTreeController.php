<?php

namespace Varbox\Controllers;

use Illuminate\Http\Request;
use Varbox\Contracts\PageFilterContract;
use Varbox\Sorts\PageSort;

class PagesTreeController extends PagesController
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fixTree()
    {
        $this->model->doNotGenerateUrl()->doNotLogActivity()->fixTree();

        $this->refreshUrls();

        flash()->success('Tree items fixed successfully!');

        return back();
    }

    /**
     * @param Request $request
     * @param int|null $parent
     * @return array
     * @throws \Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function loadNodes(Request $request, $parent = null)
    {
        $data = [];
        $query = $this->model->withDrafts();

        if ($parent) {
            $items = $query->whereDescendantOf($parent)->defaultOrder()->get()->toTree();
        } elseif (cache()->has('first_tree_load')) {
            cache()->forget('first_tree_load');

            $items = $query->whereIsRoot()->defaultOrder()->get();
        } else {
            cache()->forever('first_tree_load', true);

            $data[] = [
                'id' => 'root_id',
                'text' => 'Pages',
                'children' => true,
                'type' => 'root',
                'icon' => 'fa fa-copy'
            ];
        }

        if (isset($items)) {
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->id,
                    'text' => $item->name,
                    'children' => $item->children()->withDrafts()->count() > 0 ? true : false,
                    'type' => 'child',
                    'icon' => 'fa fa-file'
                ];
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param PageFilterContract $filter
     * @param PageSort $sort
     * @param int|null $parent
     * @return \Illuminate\View\View
     */
    public function listItems(Request $request, PageFilterContract $filter, PageSort $sort, $parent = null)
    {
        $q = $this->model->withDrafts();
        $query = $this->model->withDrafts()
            ->filtered($request->all(), $filter)
            ->sorted($request->all(), $sort)
            ->orderBy($this->model->getLftName());

        if ($parent = $q->find($parent)) {
            $query->ofParent($parent);
        } else {
            $query->whereIsRoot();
        }

        return view('varbox::admin.pages._table')->with([
            'items' => $query->get(),
            'parent' => $parent,
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

        return $this->model->doNotGenerateUrl()->withDrafts()->rebuildTree($tree);
    }

    /**
     * @return void
     */
    public function refreshUrls()
    {
        foreach ($this->model->withDrafts()->defaultOrder()->get() as $page) {
            $ancestors = $page->ancestors()->withDrafts()->defaultOrder()->get();
            $segments = [];

            foreach ($ancestors as $ancestor) {
                $segments[] = $ancestor->slug;
            }

            $segments[] = $page->slug;

            $page->url()->update([
                'url' => implode('/' , (array)$segments)
            ]);
        }
    }

    /**
     * @param array $items
     * @param array $array
     * @return void
     */
    protected function rebuildBranch(array $items, array &$array)
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
