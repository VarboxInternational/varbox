<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Models\Email;
use Varbox\Traits\CanCrud;
use Varbox\Contracts\EmailModelContract;
use Varbox\Filters\EmailFilter;
use Varbox\Requests\EmailRequest;
use Varbox\Sorts\EmailSort;
use Varbox\Traits\CanDuplicate;

class EmailsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //use CanCrud, CanDraft, CanRevision, CanDuplicate, CanSoftDelete;
    use CanCrud, CanDuplicate;

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

            /*if ($this->model->isDraftingEnabled()) {
                $query->withDrafts()->publishedOrNot($request->query('published'));
            }

            if ($this->model->isSoftDeletingEnabled()) {
                $query->withTrashed()->trashedOrNot($request->query('trashed'));
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
     * Set the options for the CanDraft trait.
     *
     * @return DraftOptions
     */
    /*public function getDraftOptions()
    {
        $model = config('varbox.cms.binding.models.email_model', Email::class);
        $request = config('varbox.cms.binding.form_requests.email_form_request', EmailRequest::class);

        return DraftOptions::instance()
            ->setEntityModel($model)
            ->setValidatorRequest(new $request)
            ->setTitle('Email Draft')
            ->setDraftView('varbox::admin.emails.draft')
            ->setLimboView('varbox::admin.emails.limbo')
            ->setRedirectUrl('admin.emails.index')
            ->setViewVariables([
                'types' => $this->model->getTypesForSelect(),
                'fromEmail' => $this->model->getFromAddress(),
                'fromName' => $this->model->getFromName(),
            ]);
    }*/

    /**
     * Set the options for the CanRevision trait.
     *
     * @return RevisionOptions
     */
    /*public function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->setPageTitle('Email Revision')
            ->setPageview('varbox::admin.emails.revision')
            ->setViewVariables([
                'fromEmail' => $this->model->getFromAddress(),
                'fromName' => $this->model->getFromName(),
            ]);
    }*/

    /**
     * Set the options for the CanDuplicate trait.
     *
     * @return DuplicateOptions
     */
    /*public function getDuplicateOptions()
    {
        $model = config('varbox.cms.binding.models.email_model', Email::class);

        return DuplicateOptions::instance()
            ->setEntityModel($model)
            ->setRedirectUrl('admin.emails.edit');
    }*/

    /**
     * Get the model to be duplicated.
     *
     * @return Model
     */
    protected function modelToBeDuplicated(): string
    {
        return config('varbox.bindings.models.email_model', Email::class);
    }

    /**
     * Get the route name to redirect to after the duplication.
     *
     * @param Model $duplicatedModel
     * @return string
     */
    protected function redirectAfterDuplication(Model $duplicatedModel): string
    {
        return route('admin.emails.edit', $duplicatedModel->getKey());
    }

    /**
     * Set the options for the CanSoftDelete trait.
     *
     * @return SoftDeleteOptions
     */
    /*public function getSoftDeleteOptions()
    {
        return SoftDeleteOptions::instance()
            ->setEntityModel(config('varbox.cms.binding.models.email_model', Email::class))
            ->setRedirectUrl('admin.emails.index');
    }*/
}
