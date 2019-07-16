<?php

namespace Varbox\Tests\Integration\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Varbox\Models\Upload;
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
        $this->app->register(\Pbmedia\LaravelFFMpeg\FFMpegServiceProvider::class);

        $this->app->alias('Image', \Intervention\Image\Facades\Image::class);
        $this->app->alias('FFMpeg', \Pbmedia\LaravelFFMpeg\FFMpegFacade::class);
    }

    /** @test */
    public function it_can_upload_an_image()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_can_upload_a_video()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->videoFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_can_upload_an_audio()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->audioFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_can_upload_a_file()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->pdfFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
    }








    /** @test */
    public function it_uploads_a_file_to_the_uploads_disk_by_default()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_uploads_a_file_to_the_specified_disk_from_config()
    {
        $this->app['config']->set('varbox.upload.storage.disk', 'test');

        Storage::fake($this->disk);
        Storage::fake('test');

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
        Storage::disk('test')->assertExists($file->getPath() . '/' . $file->getName());
    }







    /** @test */
    public function it_stores_the_upload_to_database_by_default()
    {
        Storage::fake($this->disk);
        Upload::truncate();

        $file = (new UploadService($this->imageFile()))->upload();
        $upload = Upload::first();

        $this->assertEquals(1, Upload::count());
        $this->assertEquals($file->getName(), $upload->name);
        $this->assertEquals('test.jpg', $upload->original_name);
        $this->assertEquals($file->getPath(), $upload->path);
        $this->assertEquals($file->getPath() . '/' . $file->getName(), $upload->full_path);
        $this->assertEquals($file->getExtension(), $upload->extension);
        $this->assertEquals(UploadService::TYPE_IMAGE, $upload->type);
    }

    /** @test */
    public function it_stores_the_upload_to_database_if_enabled_from_config()
    {
        $this->app['config']->set('varbox.upload.database.save', true);

        Storage::fake($this->disk);
        Upload::truncate();

        $file = (new UploadService($this->imageFile()))->upload();
        $upload = Upload::first();

        $this->assertEquals(1, Upload::count());
        $this->assertEquals($file->getName(), $upload->name);
        $this->assertEquals('test.jpg', $upload->original_name);
        $this->assertEquals($file->getPath(), $upload->path);
        $this->assertEquals($file->getPath() . '/' . $file->getName(), $upload->full_path);
        $this->assertEquals($file->getExtension(), $upload->extension);
        $this->assertEquals(UploadService::TYPE_IMAGE, $upload->type);
    }

    /** @test */
    public function it_doesnt_store_the_upload_to_database_if_disabled_from_config()
    {
        $this->app['config']->set('varbox.upload.database.save', false);

        Storage::fake($this->disk);
        Upload::truncate();

        (new UploadService($this->imageFile()))->upload();

        $this->assertEquals(0, Upload::count());
    }








    

    /**
     * @return UploadedFile
     */
    protected function imageFile()
    {
        return new UploadedFile(__DIR__ . '/../../Files/test.jpg', 'test.jpg', null, null, true);
    }

    /**
     * @return UploadedFile
     */
    protected function videoFile()
    {
        return new UploadedFile(__DIR__ . '/../../Files/test.mp4', 'test.mp4', null, null, true);
    }

    /**
     * @return UploadedFile
     */
    protected function audioFile()
    {
        return new UploadedFile(__DIR__ . '/../../Files/test.mp3', 'test.mp3', null, null, true);
    }

    /**
     * @return UploadedFile
     */
    protected function pdfFile()
    {
        return new UploadedFile(__DIR__ . '/../../Files/test.pdf', 'test.pdf', null, null, true);
    }
}
