<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Traits\CanRevision;
use Varbox\Models\Email;
use Varbox\Traits\CanCrud;
use Varbox\Contracts\EmailModelContract;
use Varbox\Filters\EmailFilter;
use Varbox\Requests\EmailRequest;
use Varbox\Sorts\EmailSort;
use Varbox\Traits\CanDuplicate;
use Varbox\Traits\CanSoftDelete;

class EmailsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud, CanRevision, CanDuplicate, CanSoftDelete;

    /**
     * @var EmailModelContract
     */
    protected $model;

    /**
     * EmailsController constructor.
     *
     * @param EmailModelContract $model
     */
    public function __construct(EmailModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * @param Request $request
     * @param EmailFilter $filter
     * @param EmailSort $sort
     * @return \Illuminate\View\View
     * @throws Exception
     */
    public function index(Request $request, EmailFilter $filter, EmailSort $sort)
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

            /*if ($this->model->isDraftingEnabled()) {
                $query->withDrafts()->publishedOrNot($request->query('published'));
            }*/

            $query->filtered($request->all(), $filter)->sorted($request->all(), $sort);

            $this->items = $query->paginate(config('varbox.base.crud.per_page', 10));
            $this->title = 'Emails';
            $this->view = view('varbox::admin.emails.index');
            $this->vars = [
                'types' => $this->model->getTypesForSelect(),
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
        return $this->_create(function () use ($type) {
            $this->title = 'Add Email';
            $this->view = view('varbox::admin.emails.add');
            $this->vars = [
                'types' => $this->model->getTypesForSelect(),
                'fromEmail' => $this->model->getFromAddress(),
                'fromName' => $this->model->getFromName(),
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
        app(config('varbox.bindings.form_requests.email_form_request', EmailRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.emails.index');
        }, $request);
    }

    /**
     * @param EmailModelContract $email
     * @return \Illuminate\View\View
     * @throws Exception
     */
    public function edit(EmailModelContract $email)
    {
        return $this->_edit(function () use ($email) {
            $this->item = $email;
            $this->title = 'Edit Email';
            $this->view = view('varbox::admin.emails.edit');
            $this->vars = [
                'types' => $this->model->getTypesForSelect(),
                'variables' => $this->model->getEmailVariables($email->type),
                'fromEmail' => $this->model->getFromAddress(),
                'fromName' => $this->model->getFromName(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param EmailModelContract $email
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function update(Request $request, EmailModelContract $email)
    {
        app(config('varbox.bindings.form_requests.email_form_request', EmailRequest::class));

        return $this->_update(function () use ($email, $request) {
            $this->item = $email;
            $this->redirect = redirect()->route('admin.emails.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param EmailModelContract $email
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function destroy(EmailModelContract $email)
    {
        return $this->_destroy(function () use ($email) {
            $this->item = $email;
            $this->redirect = redirect()->route('admin.emails.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param EmailModelContract $email
     * @return \Illuminate\Support\HtmlString
     */
    /*public function preview(Request $request, EmailModelContract $email = null)
    {
        app(config('varbox.cms.binding.form_requests.email_form_request', EmailRequest::class));

        DB::beginTransaction();

        if ($email && $email->exists) {
            $email->update($request->all());
        } else {
            $email = $this->model->create($request->all());
        }

        $types = $this->model->getTypes();
        $view = $types[$email->type]['view'];
        $data = (array)$email->metadata;

        DB::rollBack();

        return (new Markdown(view(), config('mail.markdown')))
            ->render($view, $data);
    }*/

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
        return 'Email Revision';
    }

    /**
     * Get the blade view to be rendered as the revision view.
     *
     * @return string
     */
    protected function revisionView(): string
    {
        return 'varbox::admin.emails.revision';
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
            'types' => $this->model->getTypesForSelect(),
            'variables' => $this->model->getEmailVariables($revisionable->type),
            'fromEmail' => $this->model->getFromAddress(),
            'fromName' => $this->model->getFromName(),
        ];
    }

    /**
     * Get the model to be duplicated.
     *
     * @return Model
     */
    protected function duplicateModel(): string
    {
        return config('varbox.bindings.models.email_model', Email::class);
    }

    /**
     * Get the url to redirect to after the duplication.
     *
     * @param Model $duplicate
     * @return string
     */
    protected function duplicateRedirectTo(Model $duplicate): string
    {
        return route('admin.emails.edit', $duplicate->getKey());
    }

    /**
     * Get the model to be soft deleted.
     *
     * @return Model
     */
    protected function softDeleteModel(): string
    {
        return config('varbox.bindings.models.email_model', Email::class);
    }

    /**
     * Get the url to redirect to after the soft deletion.
     *
     * @return string
     */
    protected function softDeleteRedirectTo(): string
    {
        return route('admin.emails.index');
    }
}
