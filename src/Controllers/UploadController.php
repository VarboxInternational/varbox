<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Varbox\Contracts\UploadModelContract;
use Varbox\Exceptions\UploadException;

class UploadController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param UploadModelContract $model
     * @return JsonResponse
     */
    public function upload(Request $request, UploadModelContract $model)
    {
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid file!'
            ]);
        }

        if ($request->input('accept') && !in_array($request->file('file')->getClientOriginalExtension(), explode(',', $request->input('accept')))) {
            return response()->json([
                'status' => false,
                'message' => 'File type is not allowed! Allowed extensions: ' . $request->input('accept')
            ]);
        }

        try {
            if (!$request->filled('model') || !$request->filled('field')) {
                throw new Exception;
            }

            $file = upload($request->file('file'), app($request->input('model')), $request->input('field'))->upload();
            $upload = $model->whereFullPath($file->getPath() . '/' . $file->getName())->firstOrFail();

            return response()->json([
                'status' => true,
                'message' => 'Upload successful!',
                'type' => Str::snake($model->getFileTypes()[$file->getType()]),
                'html' => view()->make('varbox::helpers.uploader.partials.items')->with([
                    'type' => Str::snake($model->getFileTypes()[$file->getType()]),
                    'uploads' => collect()->push($upload),
                ])->render()
            ]);
        } catch (UploadException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Upload failed! Please try again.'
            ]);
        }
    }

    /**
     * @param Request $request
     * @param UploadModelContract $model
     * @param array|string|int|null $type
     * @return JsonResponse
     * @throws Throwable
     */
    public function get(Request $request, UploadModelContract $model, $type = null)
    {
        $uploads = $model->latest()->onlyTypes($type)->onlyExtensions($request->query('accept'))->like([
            'original_name' => $request->query('keyword'),
        ])->paginate(28);

        return response()->json([
            'status' => $request->query('page') > 1 && !$uploads->count() ? false : true,
            'html' => $request->query('page') > 1 && !$uploads->count() ? '' : view('varbox::helpers.uploader.partials.items')->with([
                'type' => $type,
                'uploads' => $uploads,
            ])->render()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function set(Request $request)
    {
        try {
            if (!$request->filled('path') || !$request->filled('model') || !$request->filled('field')) {
                throw new Exception;
            }

            $upload = upload(
                $request->input('path'), app($request->input('model')), $request->input('field')
            )->upload();

            return response()->json([
                'status' => true,
                'path' => $upload->getPath() . '/' . $upload->getName(),
                'name' => $upload->getOriginal()->original_name
            ]);
        } catch (UploadException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not set the file!',
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function crop(Request $request)
    {
        $index = $request->query('index');
        $model = app($request->query('model'));
        $field = $request->query('field');
        $url = $request->query('url');
        $path = $request->query('path');
        $style = $request->query('style');

        if (isset($model->getUploadConfig()['images']['styles'])) {
            foreach ($model->getUploadConfig()['images']['styles'] as $name => $styles) {
                if (str_is($name, $field)) {
                    $field = $name;
                    break;
                }
            }
        }

        $width = array_get($model->getUploadConfig(), "images.styles.{$field}.{$style}.width");
        $height = array_get($model->getUploadConfig(), "images.styles.{$field}.{$style}.height");

        $imageSize = getimagesize(Storage::disk(config('varbox.media.upload.storage.disk', 'uploads'))->path($path));
        $cropSize = [$width, $height];
        $dCropSize = $cropSize;

        if ($dCropSize[0] && !$dCropSize[1]) {
            $dCropSize[1] = floor($dCropSize[0] / $imageSize[0] * $imageSize[1]);
        }

        if ($dCropSize[1] && !$dCropSize[0]) {
            $dCropSize[0] = floor($dCropSize[1] / $imageSize[1] * $imageSize[0]);
        }

        return response()->json([
            'status' => true,
            'html' => view('varbox::helpers.uploader.partials.crop')->with([
                'index' => $index,
                'url' => $url,
                'path' => $path,
                'style' => $style,
                'imageSize' => $imageSize,
                'cropSize' => $cropSize,
                'dCropSize' => $dCropSize,
            ])->render()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cut(Request $request)
    {
        try {
            upload(
                $request->input('path')
            )->crop(
                $request->input('path'),
                $request->input('style'),
                $request->input('size'),
                $request->input('w'),
                $request->input('h'),
                $request->input('x'),
                $request->input('y')
            );

            return response()->json([
                'status' => true
            ]);
        } catch (UploadException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
            ]);
        }
    }
}
