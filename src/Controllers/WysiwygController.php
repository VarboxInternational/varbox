<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class WysiwygController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        try {
            $file = $request->file('wysiwyg_image');

            $allowedMaxSize = config('varbox.wysiwyg.image_max_size');
            $allowedExtensions = config('varbox.wysiwyg.image_allowed_extensions');

            if (!$file->isValid()) {
                throw new Exception('The image supplied is invalid!', 422);
            }

            if ($allowedMaxSize && $file->getSize() > $allowedMaxSize) {
                throw new Exception('The image size must be less than ' . $allowedMaxSize / 1024 / 1024 . ' MB!', 413);
            }

            if ($allowedExtensions && !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
                throw new Exception('Please upload images with the following extensions: ' . implode(', ', $allowedExtensions), 415);
            }

            $path = $file->store(null, 'wysiwyg');

            return response()->json(Storage::disk('wysiwyg')->url($path));
        } catch (Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
}
