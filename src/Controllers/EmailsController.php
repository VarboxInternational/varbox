<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Varbox\Traits\CanDraft;
use Varbox\Traits\CanRevision;
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
    use CanCrud, CanDraft, CanRevision, CanDuplicate;

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

            $query->filtered($request->all(), $filter)->sorted($request->all(), $sort);

            $this->items = $query->paginate(config('varbox.base.crud.per_page', 30));
            $this->title = 'Emails';
            $this->view = view('varbox::admin.emails.index');
            $this->vars = [
                'types' => $this->typesToArray(),
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
                'types' => $this->typesToArray(),
                'fromEmail' => config('mail.from.address', null),
                'fromName' => config('mail.from.address', null),
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
                'variables' => $email->variables,
                'types' => $this->typesToArray(),
                'fromEmail' => config('mail.from.address', null),
                'fromName' => config('mail.from.address', null),
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
    public function preview(Request $request, EmailModelContract $email = null)
    {
        app(config('varbox.bindings.form_requests.email_form_request', EmailRequest::class));

        DB::beginTransaction();

        if ($email && $email->exists) {
            $email->update($request->all());
        } else {
            $email = $this->model->create($request->all());
        }

        DB::rollBack();

        return (new Markdown(view(), config('mail.markdown')))
            ->render($email->view, $email->data);
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
        return 'Email Revision';
    }

    /**
     * Get the blade view to be rendered as the revision view.
     *
     * @return string
     */
    protected function revisionView(): string
    {
        return 'varbox::admin.emails.edit';
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
            'variables' => $revisionable->variables,
            'types' => $this->typesToArray(),
            'fromEmail' => config('mail.from.address', null),
            'fromName' => config('mail.from.address', null),
        ];
    }

    /**
     * Get the model to be drafted.
     *
     * @return string
     */
    protected function draftModel(): string
    {
        return config('varbox.bindings.models.email_model', Email::class);
    }

    /**
     * Get the form request to validate the draft upon.
     *
     * @return string|null
     */
    protected function draftRequest(): ?string
    {
        return config('varbox.bindings.form_requests.email_form_request', EmailRequest::class);
    }

    /**
     * Get the url to redirect after drafting/publishing.
     *
     * @param Model $model
     * @return string
     */
    protected function draftRedirectTo(Model $model): string
    {
        return route('admin.emails.edit', $model->getKey());
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
     * Get the formatted email types for a select.
     * Final format will be: [type => title-cased type].
     *
     * @return array
     */
    protected function typesToArray()
    {
        $types = [];

        foreach (array_keys((array)config('varbox.emails.types', [])) as $type) {
            $types[$type] = Str::title(str_replace(['_', '-', '.'], ' ', $type));
        }

        return $types;
    }
}
