<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\LanguageModelContract;
use Varbox\Contracts\TranslationModelContract;
use Varbox\Filters\TranslationFilter;
use Varbox\Requests\TranslationRequest;
use Varbox\Sorts\TranslationSort;
use Varbox\Traits\CanCrud;

class TranslationsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var TranslationModelContract
     */
    protected $model;

    /**
     * @var LanguageModelContract
     */
    protected $language;

    /**
     * RolesController constructor.
     *
     * @param TranslationModelContract $model
     */
    public function __construct(TranslationModelContract $model, LanguageModelContract $language)
    {
        $this->model = $model;
    }

    /**
     * @param Request $request
     * @param TranslationFilter $filter
     * @param TranslationSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, TranslationFilter $filter, TranslationSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model
                ->filtered($request, $filter)
                ->sorted($request, $sort)
                ->paginate(config('crud.per_page'));

            $this->title = 'Translations';
            $this->view = view('admin.translations.index');
            $this->vars = [
                'locales' => $this->language->onlyActive()->get()->pluck('name', 'code')->toArray(),
                'groups' => $this->model->distinctGroup()->get()->pluck('group_formatted', 'group')->toArray(),
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Translation';
            $this->view = view('admin.translations.add');
        });
    }

    /**
     * @param TranslationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(TranslationRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.translations.index');
        }, $request);
    }

    /**
     * @param TranslationModelContract $translation
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(TranslationModelContract $translation)
    {
        return $this->_edit(function () use ($translation) {
            $this->item = $translation;
            $this->title = 'Edit Translation';
            $this->view = view('admin.translations.edit');
        });
    }

    /**
     * @param TranslationRequest $request
     * @param TranslationModelContract $translation
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(TranslationRequest $request, TranslationModelContract $translation)
    {
        return $this->_update(function () use ($request, $translation) {
            $this->item = $translation;
            $this->redirect = redirect()->route('admin.translations.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param TranslationModelContract $translation
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(TranslationModelContract $translation)
    {
        return $this->_destroy(function () use ($translation) {
            $this->item = $translation;
            $this->redirect = redirect()->route('admin.translations.index');

            $this->item->delete();
        });
    }

    /**
     * @param TranslationService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(TranslationService $service)
    {
        try {
            $service->importAllTranslations();

            flash()->success('The translations have been successfully imported!');
        } catch (TranslationException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Could not import the translations! Please try again.');
        }

        return redirect()->route('admin.translations.index');
    }

    /**
     * @param TranslationService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export(TranslationService $service)
    {
        try {
            $service->exportAllTranslations();

            flash()->success('The translations have been successfully exported!');
        } catch (TranslationException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Could not export the translations! Please try again.');
        }

        return redirect()->route('admin.translations.index');
    }

    /**
     * @param TranslationService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sync(TranslationService $service)
    {
        try {
            $service->findMissingTranslations();

            flash()->success('The translations have been successfully synced!');
        } catch (TranslationException $e) {
            flash()->error($e->getMessage());
        } catch (Exception $e) {
            flash()->error('Could not sync the missing translations if any! Please try again.');
        }

        return redirect()->route('admin.translations.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        try {
            Translation::truncate();

            flash()->success('The translations have been successfully removed!');
        } catch (Exception $e) {
            flash()->error('Could remove the translations! Please try again.');
        }

        return redirect()->route('admin.translations.index');
    }
}
