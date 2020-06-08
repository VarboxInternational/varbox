<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\UploadFilterContract;
use Varbox\Contracts\UploadModelContract;
use Varbox\Contracts\UploadServiceContract;
use Varbox\Contracts\UploadSortContract;
use Varbox\Exceptions\UploadException;
use Varbox\Models\Upload;

class UploadsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var UploadModelContract
     */
    protected $model;

    /**
     * UploadsController constructor.
     *
     * @param UploadModelContract $model
     */
    public function __construct(UploadModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * @param Request $request
     * @param UploadFilterContract $filter
     * @param UploadSortContract $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, UploadFilterContract $filter, UploadSortContract $sort)
    {
        meta()->set('title', 'Admin - Uploads');

        $items = $this->model
            ->filtered($request->all(), $filter)
            ->sorted($request->all(), $sort)
            ->paginate(config('varbox.crud.per_page', 30));

        return view('varbox::admin.uploads.index')->with([
            'title' => 'Uploads',
            'items' => $items,
            'types' => Upload::getFileTypes(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $status = $message = null;

        if (!($request->hasFile('file') && $request->file('file')->isValid())) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or missing file',
            ]);
        }

        try {
            app(UploadServiceContract::class, [
                'file' => $request->file('file')
            ])->upload();

            $status = true;
        } catch (UploadException $e) {
            logger()->error($e);

            $status = false;
            $message = $e->getMessage();
        } catch (Exception $e) {
            logger()->error($e);

            $status = false;
            $message = 'Could not upload the file!';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    /**
     * @param UploadModelContract $upload
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(UploadModelContract $upload)
    {
        try {
            app(UploadServiceContract::class, [
                'file' => $upload->full_path
            ])->unload();

            flash()->success('The record was successfully deleted!');

            return redirect()->route('admin.uploads.index', parse_url(url()->previous(), PHP_URL_QUERY) ?: []);
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to delete a record that does not exist!', $e);

            return redirect()->route('admin.uploads.index');
        } catch (QueryException $e) {
            flash()->error('Could not delete the file because it is used by other entities!', $e);

            return redirect()->route('admin.uploads.index');
        } catch (UploadException $e) {
            flash()->error($e->getMessage(), $e);

            return back();
        } catch (Exception $e) {
            flash()->error('The record could not be deleted! Please try again.', $e);

            return back();
        }
    }

    /**
     * @param UploadModelContract $upload
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(UploadModelContract $upload)
    {
        try {
            return app(UploadServiceContract::class, [
                'file' => $upload->full_path
            ])->download();
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to download a file that does not exist!', $e);

            return redirect()->route('admin.uploads.index');
        }
    }
}
