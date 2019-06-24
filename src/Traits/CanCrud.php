<?php

namespace Varbox\Traits;

use Closure;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

trait CanCrud
{
    /**
     * The collection of existing records from the database.
     * Setting the $items should be done at controller level, in the callback.
     *
     * @var Collection
     */
    protected $items;

    /**
     * The loaded model.
     * Loading should be done at controller level, in the callback.
     *
     * @var Model
     */
    protected $item;

    /**
     * The title of the page.
     * This is used to build the meta title tag.
     *
     * @var string
     */
    protected $title;

    /**
     * The view to be returned for a given request.
     * Setting the $view should be done at controller level, in the callback.
     *
     * @var View
     */
    protected $view;

    /**
     * The redirect to be returned for a given request.
     * Setting the $redirect should be done at controller level, in the callback.
     *
     * @var RedirectResponse
     */
    protected $redirect;

    /**
     * All the variables that will be assigned to the view.
     * You can also use this property to assign variables to the view at controller level, in the callback.
     *
     * @var array
     */
    protected $vars = [];

    /**
     * Mapping of action => method.
     * Used to verify if a CRUD system respects the standards.
     *
     * @var array
     */
    protected static $crudMethods = [
        'index' => [
            'GET',
        ],
        'create' => [
            'GET',
        ],
        'store' => [
            'POST',
        ],
        'edit' => [
            'GET',
        ],
        'update' => [
            'PUT',
        ],
        'destroy' => [
            'DELETE',
        ],
    ];

    /**
     * This method should be called inside the controller's index() method.
     * The closure should at least set the $items and $view properties.
     *
     * $this->items = Model::get();
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure|null $function
     * @return View
     * @throws Exception
     */
    public function _index(Closure $function = null)
    {
        return $this->performGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            $this->checkCrudItems()->initCrudItems();

            $this->vars['items'] = $this->items;
        });
    }

    /**
     * This method should be called inside the controller's create() method.
     * The closure should at least set the $view property.
     *
     * $this->view = view('view.file');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure|null $function
     * @return View
     * @throws Exception
     */
    public function _create(Closure $function = null)
    {
        return $this->performGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            $this->vars['item'] = $this->model;
        });
    }

    /**
     * This method should be called inside the controller's store() method.
     * The closure should at least create the database record and set the $redirect property.
     *
     * $this->item = Model::create($request()->all());
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function _store(Closure $function = null, Request $request = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function, $request) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success($this->createSuccessMessage());
        }, $request);
    }

    /**
     * This method should be called inside the controller's edit() method.
     * The closure should at least attempt to find the record in the database.
     *
     * $this->item = Model::findOrFail($id); OR $this->item = $model; (if implicit route model binding)
     * $this->view = view('view.file');
     *
     * Although not required but strongly recommended is to also set a redirect in case somethind fails.
     *
     * $this->redirect = redirect()->route('redirect.route');
     *
     * Additionally you can also set variables to be available in the view.
     *
     * $this->vars['var_1'] = $var1;
     * $this->vars['var_2'] = $var2;
     *
     * @param Closure|null $function
     * @return View
     * @throws Exception
     */
    public function _edit(Closure $function = null)
    {
        return $this->performGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            $this->vars['item'] = $this->item ?: $this->model;
        });
    }

    /**
     * This method should be called inside the controller's update() method.
     * The closure should at least attempt to find and update the record in the database and to set the $redirect property.
     *
     * $this->item = Model::findOrFail($id)->update($request->all());
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @param Request|null $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function _update(Closure $function = null, Request $request = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function, $request) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success($this->updateSuccessMessage());
        }, $request);
    }

    /**
     * This method should be called inside the controller's destroy() method.
     * The closure should at least attempt to find and delete the record from the database and to set the $redirect property.
     *
     * $this->item = Model::findOrFail($id)->delete();
     * $this->redirect = redirect()->route('redirect.route');
     *
     * @param Closure|null $function
     * @return RedirectResponse
     * @throws Exception
     */
    public function _destroy(Closure $function = null)
    {
        return $this->performNonGetCrudRequest(function () use ($function) {
            if ($function) {
                call_user_func($function);
            }

            flash()->success($this->deleteSuccessMessage());
        });
    }

    /**
     * Perform a get crud request based on a closure.
     * The general logic resides on this method.
     * This means that the $function parameter should be a closure representing the special logic.
     *
     * @param Closure $function
     * @return RedirectResponse|View
     * @throws Exception
     */
    protected function performGetCrudRequest(Closure $function)
    {
        $this->checkCrudMethod()->checkCrudModel()->initCrudModel();
        $this->accountForCurrentQueryString();

        try {
            call_user_func($function);

            $this->checkCrudView()->initCrudView();
            $this->establishPageTitle();

            return $this->view->with($this->vars);
        } catch (ModelNotFoundException $e) {
            flash()->error($this->recordNotFoundMessage(), $e);
        } catch (Exception $e) {
            $this->throwSoftOrHardException($e);
        }

        return $this->redirectProperly();
    }

    /**
     * Perform a non-get crud request based on a closure.
     * The general logic resides on this method.
     * This means that the $function parameter should be a closure representing the special logic.
     *
     * @param Closure $function
     * @param Request|null $request
     * @return RedirectResponse
     * @throws Exception
     */
    protected function performNonGetCrudRequest(Closure $function, Request $request = null)
    {
        $this->checkCrudMethod()->checkCrudModel()->initCrudModel();

        try {
            $this->beginCrudDbTransaction();

            call_user_func($function);

            $this->checkCrudRedirect()->initCrudRedirect();

            $this->accountForPreviousQueryString();
            $this->commitCrudDbTransaction();
        } catch (ModelNotFoundException $e) {
            $this->rollbackCrudDbTransaction();

            flash()->error($this->recordNotFoundMessage(), $e);
        } catch (Exception $e) {
            $this->rollbackCrudDbTransaction();

            if (in_array(get_class($e), config('varbox.varbox-crud.soft_exceptions', []))) {
                flash()->error($e->getMessage(), $e);

                return back()->withInput($request ? $request->all() : []);
            }

            throw $e;
        }

        return $this->redirectProperly($request);
    }

    /**
     * Get the message to display when a "create" action has been completed successfully.
     *
     * @return string
     */
    protected function createSuccessMessage()
    {
        return 'The record was successfully created!';
    }

    /**
     * Get the message to display when an "update" action has been completed successfully.
     *
     * @return string
     */
    protected function updateSuccessMessage()
    {
        return 'The record was successfully updated!';
    }

    /**
     * Get the message to display when a "delete" action has been completed successfully.
     *
     * @return string
     */
    protected function deleteSuccessMessage()
    {
        return 'The record was successfully deleted!';
    }

    /**
     * Get the message to display when the admin is attempting to access a non-existent database record.
     *
     * @return string
     */
    protected function recordNotFoundMessage()
    {
        return 'You are trying to access a record that does not exist!';
    }

    /**
     * Determine if crud operations should be wrapped inside database transactions.
     *
     * @return bool
     */
    protected function shouldUseTransactions()
    {
        return config('varbox.varbox-crud.use_transactions', true) === true;
    }

    /**
     * Begin a transaction for an entire crud operation.
     * - store, update, destroy
     *
     * @return void
     */
    protected function beginCrudDbTransaction()
    {
        if ($this->shouldUseTransactions()) {
            DB::beginTransaction();
        }
    }

    /**
     * Rollback a transaction for an entire crud operation.
     * - store, update, destroy
     *
     * @return void
     */
    protected function rollbackCrudDbTransaction()
    {
        if ($this->shouldUseTransactions()) {
            DB::rollBack();
        }
    }

    /**
     * Commit a transaction for an entire crud operation.
     * - store, update, destroy
     *
     * @return void
     */
    protected function commitCrudDbTransaction()
    {
        if ($this->shouldUseTransactions()) {
            DB::commit();
        }
    }

    /**
     * Establish the current CRUD page title.
     * Set the meta title and also pass a $title variable to the view.
     *
     * @return void
     */
    protected function establishPageTitle()
    {
        if ($this->title) {
            $namespace = config('varbox.varbox-crud.namespace', 'Admin');

            meta()->set('title', $namespace . ($this->title ? ' - ' . $this->title : ''));

            $this->vars['title'] = $this->title;
        }
    }

    /**
     * Redirect the user to the proper page based on the following rules:
     * - if a "save_stay" request key is filled, it means the user specifically intended to remain on the same page
     * - if a proper redirect has been supplied by the developer | also if that redirect contains a query string, account for that
     * - as fallback, just redirect to the previous url
     *
     * @param Request|null $request
     * @return mixed
     */
    protected function redirectProperly(Request $request = null)
    {
        if ($request && $request->filled('save_stay')) {
            return back();
        }

        if ($request && $request->filled('save_continue') && $request->filled('save_continue_route')) {
            return redirect()->route($request->get('save_continue_route'), $this->item->getKey());
        }

        if ($this->redirect) {
            $query = session()->pull('crud_query');

            return redirect($this->redirect->getTargetUrl() . ($query ? '?' . $query : ''));
        }

        return back();
    }

    /**
     * Take into account if the current request contains a query string.
     * If it does, then store it in a session, in order to be used for building proper redirects.
     * If not, the just forget the session value so it won't interfere with future requests.
     *
     * @return void
     */
    protected function accountForCurrentQueryString()
    {
        if ($query = parse_url(url()->current(), PHP_URL_QUERY)) {
            session()->put('crud_query', $query);
        } else {
            session()->forget('crud_query');
        }
    }

    /**
     * Take into account if the previous request contained a query string.
     * If it does, then store it in a session, in order to be used for building proper redirects.
     *
     * @return void
     */
    protected function accountForPreviousQueryString()
    {
        if (!session()->has('crud_query')) {
            session()->put('crud_query', parse_url(url()->previous(), PHP_URL_QUERY));
        }
    }

    /**
     * Based on what "soft exceptions" are defined inside "config/varbox/crud.php" config file.
     * Either flash an error message to the user, or throw an exception.
     *
     * @param Exception $exception
     * @throws Exception
     */
    protected function throwSoftOrHardException(Exception $exception)
    {
        if (in_array(get_class($exception), config('varbox.varbox-crud.soft_exceptions', []))) {
            flash()->error($exception->getMessage(), $exception);
        } else {
            throw $exception;
        }
    }

    /**
     * Instantiate the $model property to a Model representation based on the class provided.
     *
     * @return $this
     */
    protected function initCrudModel()
    {
        if (!($this->model instanceof Model)) {
            $this->model = app($this->model);
        }

        return $this;
    }

    /**
     * Instantiate the $view property to a View representation based on the string provided.
     *
     * @return $this
     */
    protected function initCrudView()
    {
        if (!($this->view instanceof View)) {
            $this->view = view($this->view);
        }

        return $this;
    }

    /**
     * Instantiate the $view property to a View representation based on the string provided.
     *
     * @return $this
     */
    protected function initCrudRedirect()
    {
        if (!($this->redirect instanceof RedirectResponse)) {
            $this->redirect = redirect($this->redirect);
        }

        return $this;
    }

    /**
     * Instantiate the $view property to a View representation based on the string provided.
     *
     * @return $this
     */
    protected function initCrudItems()
    {
        if (!($this->items instanceof Collection) && !($this->items instanceof LengthAwarePaginator)) {
            $this->items = collect($this->items);
        }

        return $this;
    }

    /**
     * Verify if the current action runs under the appropriate CRUD method.
     *
     * @throws Exception
     * @return $this
     */
    protected function checkCrudMethod()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (isset(self::$crudMethods[$action])) {
            if (!in_array(request()->method(), self::$crudMethods[$action])) {
                throw new Exception(
                    'Action ' . $action . '() of class ' . get_class($this) . ' must use the ' . self::$crudMethods[$action] . ' request method!' . PHP_EOL .
                    'Please set this in your route definition for this request.'
                );
            }
        }

        return $this;
    }

    /**
     * Verify if the protected $model property has been properly set on the controller.
     *
     * @throws Exception
     * @return $this
     */
    protected function checkCrudModel()
    {
        $error = false;

        if (!isset($this->model) || !$this->model) {
            $error = true;
        }

        if (is_string($this->model) && !class_exists($this->model)) {
            $error = true;
        }

        if (is_object($this->model) && !($this->model instanceof Model)) {
            $error = true;
        }

        if ($error) {
            throw new Exception(
                'The $model property is not defined or is incorrect.' . PHP_EOL .
                'Please define a protected property $model on the ' . get_class($this) . ' class.' . PHP_EOL .
                'The $model should contain the entity\'s model full class name you wish to crud, as string.' . PHP_EOL .
                'Example: protected $model = "Full\Namespace\Model\Class";'
            );
        }

        return $this;
    }

    /**
     * Verify if the $view property has been properly assigned inside the callback of the given method.
     *
     * @throws Exception
     * @return $this
     */
    protected function checkCrudView()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (!$this->view || (!($this->view instanceof View) && !is_string($this->view))) {
            throw new Exception(
                'The $view property is not defined or is incorrect.' . PHP_EOL .
                'Please instantiate the $view property on the ' . $controller . ' inside the callback of the ' . $action . '() method.' . PHP_EOL .
                'The $view should be either a string, or an instance of the Illuminate\View\View class.' . PHP_EOL .
                'Example: $this->view = view("view.file");'
            );
        }

        return $this;
    }

    /**
     * Verify if the $redirect property has been properly assigned inside the callback of the given method.
     *
     * @throws Exception
     * @return $this
     */
    protected function checkCrudRedirect()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (!$this->redirect || (!($this->redirect instanceof RedirectResponse) && !is_string($this->redirect))) {
            throw new Exception(
                'The $redirect property is not defined or is incorrect.' . PHP_EOL .
                'Please instantiate the $redirect property on the ' . $controller . ' inside the callback of the ' . $action . '() method.' . PHP_EOL .
                'The $redirect should be either a string representing a URL, or an instance of the Illuminate\Http\RedirectResponse class.' . PHP_EOL .
                'Example: $this->redirect = redirect()->route("redirect.route");'
            );
        }

        return $this;
    }

    /**
     * Verify if the $redirect property has been properly assigned inside the callback of the given method.
     *
     * @throws Exception
     * @return $this
     */
    protected function checkCrudItems()
    {
        list($controller, $action) = explode('@', Route::getCurrentRoute()->getActionName());

        if (!$this->items || (!($this->items instanceof Collection) && !($this->items instanceof LengthAwarePaginator) && !is_array($this->items))) {
            throw new Exception(
                'The $items property is not defined or is incorrect.' . PHP_EOL .
                'Please instantiate the $items property on the ' . $controller . ' inside the callback of the ' . $action . '() method.' . PHP_EOL .
                'The $items should be either an array or an instance of Illuminate\Database\Eloquent\Collection or Illuminate\Contracts\Pagination\LengthAwarePaginator.' . PHP_EOL .
                'Example: $this->items = Model::queryScope()->paginate();'
            );
        }

        return $this;
    }
}
