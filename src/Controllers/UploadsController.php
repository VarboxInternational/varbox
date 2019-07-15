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
use Varbox\Contracts\UploadModelContract;
use Varbox\Exceptions\UploadException;
use Varbox\Models\Upload;
use Varbox\Filters\UploadFilter;
use Varbox\Sorts\UploadSort;

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
     * @param UploadFilter $filter
     * @param UploadSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, UploadFilter $filter, UploadSort $sort)
    {
        meta()->set('title', 'Admin - Uploads');

        $items = $this->model
            ->filtered($request->all(), $filter)
            ->sorted($request->all(), $sort)
            ->paginate(config('varbox.crud.per_page', 10));

        return view('varbox::admin.uploads.index')->with([
            'title' => 'Uploads',
            'items' => $items,
            'types' => Upload::getFileTypes(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
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
            upload($request->file('file'))->upload();

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
            upload($upload->full_path)->unload();

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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(UploadModelContract $upload)
    {
        try {
            return upload($upload->full_path)->download();
        } catch (ModelNotFoundException $e) {
            flash()->error('You are trying to download a file that does not exist!', $e);

            return redirect()->route('admin.uploads.index');
        }
    }
}
