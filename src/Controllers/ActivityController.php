<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\ActivityModelContract;
use Varbox\Filters\ActivityFilter;
use Varbox\Sorts\ActivitySort;
use Varbox\Contracts\UserModelContract;
use Varbox\Traits\CanCrud;

class ActivityController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var ActivityModelContract
     */
    protected $model;

    /**
     * @var UserModelContract
     */
    protected $user;

    /**
     * @param ActivityModelContract $model
     * @param UserModelContract $user
     */
    public function __construct(ActivityModelContract $model, UserModelContract $user)
    {
        $this->model = $model;
        $this->user = $user;
    }

    /**
     * @param Request $request
     * @param ActivityFilter $filter
     * @param ActivitySort $sort
     * @return \Illuminate\View\View
     * @throws Exception
     */
    public function index(Request $request, ActivityFilter $filter, ActivitySort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.varbox-crud.per_page', 10));

            $this->title = 'Activity';
            $this->view = view('varbox::admin.activity.index');
            $this->vars = [
                'users' => $this->user->alphabetically()->get(),
                'entities' => $this->model->getDistinctEntities(),
                'events' => $this->model->getDistinctEvents(),
                'days' => config('varbox.varbox-activity.old_threshold', 30),
            ];
        });
    }

    /**
     * @param ActivityModelContract $activity
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(ActivityModelContract $activity)
    {
        return $this->_destroy(function () use ($activity) {
            $this->item = $activity;
            $this->redirect = redirect()->route('admin.activity.index');

            $this->item->delete();
        });
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function delete()
    {
        try {
            $this->model->query()->delete();

            flash()->success('All activity was successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return redirect()->route('admin.activity.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function clean()
    {
        try {
            $this->model->deleteOld();

            flash()->success('Old activity was successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return redirect()->route('admin.activity.index');
    }
}
