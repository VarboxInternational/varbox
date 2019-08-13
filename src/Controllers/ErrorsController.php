<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\ErrorModelContract;
use Varbox\Filters\ErrorFilter;
use Varbox\Sorts\ErrorSort;
use Varbox\Traits\CanCrud;

class ErrorsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var ErrorModelContract
     */
    protected $model;

    /**
     * ErrorsController constructor.
     *
     * @param ErrorModelContract $model
     */
    public function __construct(ErrorModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * @param Request $request
     * @param ErrorFilter $filter
     * @param ErrorSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, ErrorFilter $filter, ErrorSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->orderBy('created_at', 'desc')
                ->paginate(config('varbox.crud.per_page', 10));

            $this->title = 'Errors';
            $this->view = view('varbox::admin.errors.index');
            $this->vars = [
                'days' => config('varbox.errors.old_threshold', 30),
            ];
        });
    }

    /**
     * @param ErrorModelContract $error
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function show(ErrorModelContract $error)
    {
        return $this->_edit(function () use ($error) {
            $this->item = $error;
            $this->title = 'View Error';
            $this->view = view('varbox::admin.errors.show');
        });
    }

    /**
     * @param ErrorModelContract $error
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(ErrorModelContract $error)
    {
        return $this->_destroy(function () use ($error) {
            $this->item = $error;
            $this->redirect = redirect()->route('admin.errors.index');

            $this->item->delete();
        });
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete()
    {
        try {
            $this->model->truncate();

            flash()->success('All errors have been successfully deleted!');
        } catch (\Exception $e) {
            flash()->error('Could not delete the errors! Please try again.', $e);
        }

        return redirect()->route('admin.errors.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function clean()
    {
        try {
            $this->model->deleteOld();

            flash()->success('Old errors have been successfully deleted!');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.', $e);
        }

        return redirect()->route('admin.errors.index');
    }
}
