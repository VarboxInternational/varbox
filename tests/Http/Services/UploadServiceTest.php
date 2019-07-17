<?php

namespace Varbox\Tests\Http\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Varbox\Services\UploadService;
use Varbox\Tests\Integration\TestCase;

class UploadServiceTest extends TestCase
{
    use DatabaseTransactions;

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

        $this->app->register(\Intervention\Image\ImageServiceProvider::class);
    }

    /** @test */
    public function it_can_download_an_uploaded_file()
    {
        $this->withoutExceptionHandling();

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();

        Route::get('/_test/download-file', function () use ($file) {
            return $file->download();
        });

        $response = $this->get('/_test/download-file');

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=' . $file->getFile()->getClientOriginalName());
    }

    /** @test */
    public function it_can_show_an_uploaded_file()
    {
        $this->withoutExceptionHandling();

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();

        Route::get('/_test/show-file', function () use ($file) {
            return $file->show();
        });

        $response = $this->get('/_test/show-file');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', $file->getFile()->getMimeType());
    }

    /**
     * @return UploadedFile
     */
    protected function imageFile()
    {
        return new UploadedFile(__DIR__ . '/../../Files/test.jpg', 'test.jpg', null, null, true);
    }
}
