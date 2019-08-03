<?php

namespace Varbox\Tests\Http\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageServiceProvider;
use Varbox\Services\UploadService;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Post;

class HasUploadsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Post
     */
    protected $post;

    /**
     * @var string
     */
    protected $disk = 'uploads';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->register(ImageServiceProvider::class);
    }

    /** @test */
    public function it_can_download_an_uploaded_file()
    {
        $this->withoutExceptionHandling();
        $this->createPost();

        Storage::fake($this->disk);

        $image = $this->post->uploadFile($this->imageFile(), 'image');

        $this->post->update([
            'image' => $image->getPath() . '/' . $image->getName(),
        ]);

        Route::get('/_test/download-file', function () {
            return  $this->post->downloadFile('image');
        });

        $response = $this->get('/_test/download-file');

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=' . $image->getFile()->getClientOriginalName());
    }

    /** @test */
    public function it_can_show_an_uploaded_file()
    {
        $this->withoutExceptionHandling();
        $this->createPost();

        Storage::fake($this->disk);

        $image = $this->post->uploadFile($this->imageFile(), 'image');

        $this->post->update([
            'image' => $image->getPath() . '/' . $image->getName(),
        ]);

        Route::get('/_test/show-file', function () {
            return $this->post->showFile('image');
        });

        $response = $this->get('/_test/show-file');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', $image->getFile()->getMimeType());
    }

    /**
     * @return void
     */
    protected function createPost()
    {
        $this->post = Post::create([
            'name' => 'Post test name',
            'content' => 'Post test content',
            'views' => 100,
            'approved' => true,
            'published_at' => today(),
        ]);
    }

    /**
     * @return UploadedFile
     */
    protected function imageFile()
    {
        return new UploadedFile(__DIR__ . '/../../Files/test.jpg', 'test.jpg', null, null, true);
    }
}
