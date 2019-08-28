<?php

namespace Varbox\Tests\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Tests\Models\PreviewPost;
use Varbox\Traits\CanPreview;

class PreviewController extends Controller
{
    use CanPreview;

    public function show(Request $request, $post)
    {
        $post = PreviewPost::find($post);

        return implode(', ', array_merge([
            $post->name, $post->content
        ], $post->tags->pluck('name')->toArray()));
    }

    public function previewModel(): string
    {
        return PreviewPost::class;
    }

    public function previewController(): string
    {
        return PreviewController::class;
    }

    public function previewAction(): string
    {
        return 'show';
    }

    public function previewRequest(): ?string
    {
        return null;
    }
}
