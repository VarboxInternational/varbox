<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Throwable;
use Varbox\Contracts\RevisionModelContract;

class RevisionsController extends Controller
{
    /**
     * The revisions belonging to the revisionable model instance.
     *
     * @var Collection
     */
    protected $revisions;

    /**
     * The route to redirect back to.
     *
     * @var string
     */
    protected $route;

    /**
     * The additional route parameters.
     *
     * @var string
     */
    protected $parameters;

    /**
     * Get the revisions.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws Throwable
     */
    public function getRevisions(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $this->validateRevisionableAjaxData($request);

        try {
            $this->revisions = $this->getRevisionRecords($request);
            $this->route = $request->input('route');
            $this->parameters = $request->input('parameters');

            return response()->json([
                'status' => true,
                'html' => $this->buildTableHtml(),
            ]);

        } catch (Exception $e) {
            dd($e);
            logger()->error($e);

            return response()->json([
                'status' => false,
            ]);
        }
    }

    /**
     * Rollback a revision.
     *
     * @param RevisionModelContract $revision
     * @return array|mixed
     */
    public function rollbackRevision(RevisionModelContract $revision)
    {
        try {
            $redirect = session()->pull('revision_back_url_' . $revision->id);

            $revision->revisionable->rollbackToRevision($revision);

            flash()->success('The revision was successfully rolled back!');

            if (request()->ajax()) {
                return [
                    'status' => true
                ];
            }

            return $redirect ? redirect($redirect) : back();
        } catch (Exception $e) {
            logger()->error($e);

            return [
                'status' => false,
            ];
        }
    }

    /**
     * Remove a revision.
     *
     * @param Request $request
     * @param RevisionModelContract $revision
     * @return array|mixed
     */
    public function removeRevision(Request $request, RevisionModelContract $revision)
    {
        $this->validateRevisionableAjaxData($request);

        try {
            return DB::transaction(function () use ($request, $revision) {
                $revision->delete();

                $this->revisions = $this->getRevisionRecords($request);
                $this->route = $request->input('route');
                $this->parameters = json_decode($request->input('parameters'), true);

                return [
                    'status' => true,
                    'html' => $this->buildTableHtml(),
                ];
            });
        } catch (Exception $e) {
            logger()->error($e);

            return [
                'status' => false,
            ];
        }
    }

    /**
     * Get the revisions belonging to a revisionable model.
     *
     * @param Request $request
     * @return mixed
     */
    protected function getRevisionRecords(Request $request)
    {
        return app(RevisionModelContract::class)->with('user')->whereRevisionable(
            $request->input('revisionable_id'),
            $request->input('revisionable_type')
        )->latest()->get();
    }

    /**
     * Populate the revision helper table with the updated revisions and route.
     * Return the html as string.
     *
     * @return mixed
     * @throws Exception
     * @throws Throwable
     */
    protected function buildTableHtml()
    {
        return view('varbox::helpers.revision.partials.table')->with([
            'revisions' => $this->revisions,
            'route' => $this->route,
            'parameters' => $this->parameters,
        ])->render();
    }

    /**
     * Validate the revisionable data coming from an ajax request from the revision helper.
     *
     * @param Request $request
     * @return void
     */
    protected function validateRevisionableAjaxData(Request $request)
    {
        $request->validate([
            'revisionable_id' => 'required|numeric',
            'revisionable_type' => 'required',
            'route' => 'required',
        ]);
    }
}
