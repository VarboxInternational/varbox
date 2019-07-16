<?php

namespace Varbox\Tests\Integration\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Varbox\Exceptions\UploadException;
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







    /** @test */
    public function it_guards_against_file_sizes_exceeding_the_limit_from_config_when_uploading_images()
    {
        Storage::fake($this->disk);

        $this->app['config']->set('varbox.upload.images.max_size', 2);

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        $this->app['config']->set('varbox.upload.images.max_size', 0.1);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage(UploadException::maxSizeExceeded('images',  0.1)->getMessage());

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_guards_against_file_sizes_exceeding_the_limit_from_config_when_uploading_videos()
    {
        Storage::fake($this->disk);

        $this->app['config']->set('varbox.upload.videos.max_size', 10);

        $file = (new UploadService($this->videoFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        $this->app['config']->set('varbox.upload.videos.max_size', 0.1);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage(UploadException::maxSizeExceeded('videos',  0.1)->getMessage());

        $file = (new UploadService($this->videoFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_guards_against_file_sizes_exceeding_the_limit_from_config_when_uploading_audios()
    {
        Storage::fake($this->disk);

        $this->app['config']->set('varbox.upload.audios.max_size', 1);

        $file = (new UploadService($this->audioFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        $this->app['config']->set('varbox.upload.audios.max_size', 0.1);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage(UploadException::maxSizeExceeded('audios',  0.1)->getMessage());

        $file = (new UploadService($this->audioFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_guards_against_file_sizes_exceeding_the_limit_from_config_when_uploading_files()
    {
        Storage::fake($this->disk);

        $this->app['config']->set('varbox.upload.files.max_size', 1);

        $file = (new UploadService($this->pdfFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        $this->app['config']->set('varbox.upload.files.max_size', 0.1);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage(UploadException::maxSizeExceeded('files',  0.1)->getMessage());

        $file = (new UploadService($this->pdfFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
    }








    /** @test */
    public function it_only_uploads_images_with_allowed_extensions()
    {
        Storage::fake($this->disk);

        $this->app['config']->set('varbox.upload.images.allowed_extensions', ['jpg', 'jpeg']);

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        $this->app['config']->set('varbox.upload.images.allowed_extensions', ['png']);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage(UploadException::extensionNotAllowed('images',  'png')->getMessage());

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_only_uploads_videos_with_allowed_extensions()
    {
        Storage::fake($this->disk);

        $this->app['config']->set('varbox.upload.videos.allowed_extensions', ['mp4']);

        $file = (new UploadService($this->videoFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        $this->app['config']->set('varbox.upload.videos.allowed_extensions', ['flv']);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage(UploadException::extensionNotAllowed('videos',  'flv')->getMessage());

        $file = (new UploadService($this->videoFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_only_uploads_audios_with_allowed_extensions()
    {
        Storage::fake($this->disk);

        $this->app['config']->set('varbox.upload.audios.allowed_extensions', ['mp3']);

        $file = (new UploadService($this->audioFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        $this->app['config']->set('varbox.upload.audios.allowed_extensions', ['wav']);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage(UploadException::extensionNotAllowed('audios',  'wav')->getMessage());

        $file = (new UploadService($this->audioFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_only_uploads_files_with_allowed_extensions()
    {
        Storage::fake($this->disk);

        $this->app['config']->set('varbox.upload.files.allowed_extensions', ['pdf']);

        $file = (new UploadService($this->pdfFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        $this->app['config']->set('varbox.upload.files.allowed_extensions', ['txt']);

        $this->expectException(UploadException::class);
        $this->expectExceptionMessage(UploadException::extensionNotAllowed('files',  'txt')->getMessage());

        $file = (new UploadService($this->pdfFile()))->upload();

        Storage::disk($this->disk)->assertMissing($file->getPath() . '/' . $file->getName());
    }








    /** @test */
    public function it_generates_thumbnail_by_default_when_uploading_an_image()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $thumbnail = $this->imageThumbnail($file);

        Storage::disk($this->disk)->assertExists($thumbnail);
    }

    /** @test */
    public function it_does_generate_thumbnail_when_uploading_an_image_if_enabled_from_config()
    {
        $this->app['config']->set('varbox.upload.images.generate_thumbnail', true);

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $thumbnail = $this->imageThumbnail($file);

        Storage::disk($this->disk)->assertExists($thumbnail);
    }

    /** @test */
    public function it_doesnt_generate_thumbnail_when_uploading_an_image_if_disabled_from_config()
    {
        $this->app['config']->set('varbox.upload.images.generate_thumbnail', false);

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $thumbnail = $this->imageThumbnail($file);

        Storage::disk($this->disk)->assertMissing($thumbnail);
    }

    /** @test */
    public function it_generates_thumbnail_at_100_x_100_pixels_by_default_when_uploading_an_image()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $thumbnail = $this->imageThumbnail($file);
        $size = getimagesize(Storage::disk($this->disk)->path($thumbnail));

        $this->assertEquals(100, $size[0]);
        $this->assertEquals(100, $size[1]);
    }

    /** @test */
    public function it_can_generate_thumbnail_at_defined_pixel_value_from_config_when_uploading_an_image()
    {
        $this->app['config']->set('varbox.upload.images.thumbnail_style.width', 80);
        $this->app['config']->set('varbox.upload.images.thumbnail_style.height', 160);

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $thumbnail = $this->imageThumbnail($file);
        $size = getimagesize(Storage::disk($this->disk)->path($thumbnail));

        $this->assertEquals(80, $size[0]);
        $this->assertEquals(160, $size[1]);
    }








    /** @test */
    public function it_generates_thumbnails_by_default_when_uploading_a_video()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->videoFile()))->upload();
        $thumbnail = $this->videoThumbnail($file);

        Storage::disk($this->disk)->assertExists($thumbnail);
    }

    /** @test */
    public function it_does_generate_thumbnails_when_uploading_a_video_if_enabled_from_config()
    {
        $this->app['config']->set('varbox.upload.videos.generate_thumbnails', true);

        Storage::fake($this->disk);

        $file = (new UploadService($this->videoFile()))->upload();
        $thumbnail = $this->videoThumbnail($file);

        Storage::disk($this->disk)->assertExists($thumbnail);
    }

    /** @test */
    public function it_doesnt_generate_thumbnails_when_uploading_a_video_if_disabled_from_config()
    {
        $this->app['config']->set('varbox.upload.videos.generate_thumbnails', false);

        Storage::fake($this->disk);

        $file = (new UploadService($this->videoFile()))->upload();
        $thumbnail = $this->videoThumbnail($file);

        Storage::disk($this->disk)->assertMissing($thumbnail);
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

    /**
     * @param UploadService $original
     * @return mixed
     */
    protected function imageThumbnail(UploadService $original)
    {
        $path = $original->getPath() . '/' . $original->getName();
        $extension = $original->getExtension();

        return substr_replace(
            preg_replace('/\..+$/', '.' . $extension, $path), '_thumbnail', strpos($path, '.'), 0
        );
    }

    /**
     * @param UploadService $original
     * @param int $number
     * @return mixed
     */
    protected function videoThumbnail(UploadService $original, $number = 1)
    {
        $path = $original->getPath() . '/' . $original->getName();

        return substr_replace(
            preg_replace('/\..+$/', '.jpg', $path), '_thumbnail_' . ($number ?: 1), strpos($path, '.'), 0
        );
    }
}
