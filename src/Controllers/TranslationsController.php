<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\LanguageModelContract;
use Varbox\Contracts\TranslationModelContract;
use Varbox\Contracts\TranslationServiceContract;
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
     * @param LanguageModelContract $language
     */
    public function __construct(TranslationModelContract $model, LanguageModelContract $language)
    {
        $this->model = $model;
        $this->language = $language;
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
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.crud.per_page', 30));

            $this->title = 'Translations';
            $this->view = view('varbox::admin.translations.index');
            $this->vars = [
                'locales' => $this->language->onlyActive()->get()->pluck('code', 'code')->toArray(),
                'groups' => $this->model->distinctGroup()->get()->pluck('group', 'group')->toArray(),
            ];
        });
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
            $this->view = view('varbox::admin.translations.edit');
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
     * @param TranslationServiceContract $translation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(TranslationServiceContract $translation)
    {
        try {
            $translation->importTranslations();

            flash()->success('The translations have been successfully imported!');
        } catch (Exception $e) {
            flash()->error('Could not import the translations! Please try again.', $e);
        }

        return redirect()->route('admin.translations.index');
    }

    /**
     * @param TranslationServiceContract $translation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export(TranslationServiceContract $translation)
    {
        try {
            $translation->exportTranslations();

            flash()->success('The translations have been successfully exported!');
        } catch (Exception $e) {
            flash()->error('Could not export the translations! Please try again.', $e);
        }

        return redirect()->route('admin.translations.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        try {
            $this->model->truncate();

            flash()->success('All translations have been successfully removed!');
        } catch (Exception $e) {
            flash()->error('Could not remove the translations! Please try again.', $e);
        }

        return redirect()->route('admin.translations.index');
    }
}
