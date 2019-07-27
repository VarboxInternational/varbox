<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FroalaController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request)
    {
        try {
            $file = $request->file('froala_file');

            $allowedMaxSize = config('varbox.froala.file_max_size');
            $allowedExtensions = config('varbox.froala.file_allowed_extensions');

            if (!$file->isValid()) {
                throw new Exception('The file supplied is invalid!');
            }

            if ($allowedMaxSize && $file->getSize() > $allowedMaxSize) {
                throw new Exception('The file size must be less than 5 MB!');
            }

            if ($allowedExtensions && !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
                throw new Exception('Please upload files with the following extensions: ' . implode(', ', $allowedExtensions));
            }

            $path = $file->store(null, 'froala');

            return response()->json([
                'link' => Storage::disk('froala')->url($path),
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        try {
            $file = $request->file('froala_image');

            $allowedMaxSize = config('varbox.froala.image_max_size');
            $allowedExtensions = config('varbox.froala.image_allowed_extensions');

            if (!$file->isValid()) {
                throw new Exception('The image supplied is invalid!');
            }

            if ($allowedMaxSize && $file->getSize() > $allowedMaxSize) {
                throw new Exception('The image size must be less than 5 MB!');
            }

            if ($allowedExtensions && !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
                throw new Exception('Please upload images with the following extensions: ' . implode(', ', $allowedExtensions));
            }

            $path = $file->store(null, 'froala');

            return response()->json([
                'link' => Storage::disk('froala')->url($path),
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadVideo(Request $request)
    {
        try {
            $file = $request->file('froala_video');

            $allowedMaxSize = config('varbox.froala.video_max_size');
            $allowedExtensions = config('varbox.froala.video_allowed_extensions');

            if (!$file->isValid()) {
                throw new Exception('The video supplied is invalid!');
            }

            if ($allowedMaxSize && $file->getSize() > $allowedMaxSize) {
                throw new Exception('The video size must be less than 5 MB!');
            }

            if ($allowedExtensions && !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
                throw new Exception('Please upload videos with the following extensions: ' . implode(', ', $allowedExtensions));
            }

            $path = $file->store(null, 'froala');

            return response()->json([
                'link' => Storage::disk('froala')->url($path),
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }
}
