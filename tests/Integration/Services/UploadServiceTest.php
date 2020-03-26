<?php

namespace Varbox\Tests\Integration\Services;

use Faker\Generator;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Varbox\Exceptions\UploadException;
use Varbox\Models\Upload;
use Varbox\Services\UploadService;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\UploadPost;

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

        $loader = AliasLoader::getInstance();
        $loader->alias('Image', \Intervention\Image\Facades\Image::class);
        $loader->alias('FFMpeg', \Pbmedia\LaravelFFMpeg\FFMpegFacade::class);
    }

    /** @test */
    public function it_can_return_image_extensions()
    {
        $extensions = UploadService::getImageExtensions();

        $this->assertContains('jpg', $extensions);
        $this->assertContains('png', $extensions);
    }

    /** @test */
    public function it_can_return_video_extensions()
    {
        $extensions = UploadService::getVideoExtensions();

        $this->assertContains('mp4', $extensions);
        $this->assertContains('flv', $extensions);
    }

    /** @test */
    public function it_can_return_audio_extensions()
    {
        $extensions = UploadService::getAudioExtensions();

        $this->assertContains('mp3', $extensions);
        $this->assertContains('wav', $extensions);
    }

    /** @test */
    public function it_can_return_image_type()
    {
        $type = UploadService::getImageType();

        $this->assertEquals(UploadService::TYPE_IMAGE, $type);
    }

    /** @test */
    public function it_can_return_video_type()
    {
        $type = UploadService::getVideoType();

        $this->assertEquals(UploadService::TYPE_VIDEO, $type);
    }

    /** @test */
    public function it_can_return_audio_type()
    {
        $type = UploadService::getAudioType();

        $this->assertEquals(UploadService::TYPE_AUDIO, $type);
    }

    /** @test */
    public function it_can_return_file_type()
    {
        $type = UploadService::getFileType();

        $this->assertEquals(UploadService::TYPE_FILE, $type);
    }

    /** @test */
    public function it_can_determine_if_a_file_is_an_image()
    {
        Storage::fake($this->disk);

        $file = new UploadService($this->imageFile());

        $this->assertTrue($file->isImage());
    }

    /** @test */
    public function it_can_determine_if_a_file_is_a_video()
    {
        Storage::fake($this->disk);

        $file = new UploadService($this->videoFile());

        $this->assertTrue($file->isVideo());
    }

    /** @test */
    public function it_can_determine_if_a_file_is_an_audio()
    {
        Storage::fake($this->disk);

        $file = new UploadService($this->audioFile());

        $this->assertTrue($file->isAudio());
    }

    /** @test */
    public function it_can_determine_if_a_file_is_a_file()
    {
        Storage::fake($this->disk);

        $file = new UploadService($this->pdfFile());

        $this->assertTrue($file->isFile());
    }

    /** @test */
    public function it_can_upload_an_image()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
    }

    /** @test */
    public function it_can_upload_an_image_from_a_url()
    {
        Storage::fake($this->disk);

        $file = (new UploadService(app(Generator::class)->imageUrl()))->upload();

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
    public function it_can_unload_an_image()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        (new UploadService($file->getPath() . '/' . $file->getName()))->unload();

        $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
    }

    /** @test */
    public function it_can_unload_an_image_along_with_the_database_record()
    {
        $this->app['config']->set('varbox.upload.database.save', true);

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
        $this->assertEquals(1, Upload::count());

        (new UploadService($file->getPath() . '/' . $file->getName()))->unload();

        $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
        $this->assertEquals(0, Upload::count());
    }

    /** @test */
    public function it_can_unload_a_video()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->videoFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        (new UploadService($file->getPath() . '/' . $file->getName()))->unload();

        $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
    }

    /** @test */
    public function it_can_unload_a_video_along_with_the_database_record()
    {
        $this->app['config']->set('varbox.upload.database.save', true);

        Storage::fake($this->disk);

        $file = (new UploadService($this->videoFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
        $this->assertEquals(1, Upload::count());

        (new UploadService($file->getPath() . '/' . $file->getName()))->unload();

        $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
        $this->assertEquals(0, Upload::count());
    }

    /** @test */
    public function it_can_unload_an_audio()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->audioFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        (new UploadService($file->getPath() . '/' . $file->getName()))->unload();

        $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
    }

    /** @test */
    public function it_can_unload_an_audio_along_with_the_database_record()
    {
        $this->app['config']->set('varbox.upload.database.save', true);

        Storage::fake($this->disk);

        $file = (new UploadService($this->audioFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
        $this->assertEquals(1, Upload::count());

        (new UploadService($file->getPath() . '/' . $file->getName()))->unload();

        $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
        $this->assertEquals(0, Upload::count());
    }

    /** @test */
    public function it_can_unload_a_file()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->pdfFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());

        (new UploadService($file->getPath() . '/' . $file->getName()))->unload();

        $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
    }

    /** @test */
    public function it_can_unload_a_file_along_with_the_database_record()
    {
        $this->app['config']->set('varbox.upload.database.save', true);

        Storage::fake($this->disk);

        $file = (new UploadService($this->pdfFile()))->upload();

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
        $this->assertEquals(1, Upload::count());

        (new UploadService($file->getPath() . '/' . $file->getName()))->unload();

        $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
        $this->assertEquals(0, Upload::count());
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
    public function it_resizes_original_resolution_by_default_when_uploading_an_image()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $path = $file->getPath() . '/' . $file->getName();
        $size = getimagesize(Storage::disk($this->disk)->path($path));
        $width = $size[0];
        $height = $size[1];

        $this->assertLessThanOrEqual(1600, $width);
        $this->assertEquals(1600, $height);
    }

    /** @test */
    public function it_does_resize_original_resolution_when_uploading_an_image_if_enabled_from_config()
    {
        $this->app['config']->set('varbox.upload.images.max_resolution.width', 600);
        $this->app['config']->set('varbox.upload.images.max_resolution.height', 600);

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $path = $file->getPath() . '/' . $file->getName();
        $size = getimagesize(Storage::disk($this->disk)->path($path));
        $width = $size[0];
        $height = $size[1];

        $this->assertLessThanOrEqual(600, $width);
        $this->assertEquals(600, $height);
    }

    /** @test */
    public function it_doesnt_resize_original_resolution_when_uploading_an_image_if_disabled_from_config()
    {
        $this->app['config']->set('varbox.upload.images.max_resolution.width', null);
        $this->app['config']->set('varbox.upload.images.max_resolution.height', null);

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $path = $file->getPath() . '/' . $file->getName();

        $originalSize = getimagesize($this->imageFile());
        $originalWidth = $originalSize[0];
        $originalHeight = $originalSize[1];

        $size = getimagesize(Storage::disk($this->disk)->path($path));
        $width = $size[0];
        $height = $size[1];

        $this->assertEquals($originalWidth, $width);
        $this->assertEquals($originalHeight, $height);
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

    /** @test */
    public function it_generates_3_thumbnails_by_default_when_uploading_a_video()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->videoFile()))->upload();
        $thumbnail1 = $this->videoThumbnail($file, 1);
        $thumbnail2 = $this->videoThumbnail($file, 2);
        $thumbnail3 = $this->videoThumbnail($file, 3);

        Storage::disk($this->disk)->assertExists($thumbnail1);
        Storage::disk($this->disk)->assertExists($thumbnail2);
        Storage::disk($this->disk)->assertExists($thumbnail3);
    }

    /** @test */
    public function it_generates_number_of_thumbnails_specified_in_config_when_uploading_a_video()
    {
        $this->app['config']->set('varbox.upload.videos.thumbnails_number', 5);

        Storage::fake($this->disk);

        $file = (new UploadService($this->videoFile()))->upload();
        $thumbnail1 = $this->videoThumbnail($file, 1);
        $thumbnail2 = $this->videoThumbnail($file, 2);
        $thumbnail3 = $this->videoThumbnail($file, 3);
        $thumbnail4 = $this->videoThumbnail($file, 4);
        $thumbnail5 = $this->videoThumbnail($file, 5);
        $thumbnail6 = $this->videoThumbnail($file, 6);

        Storage::disk($this->disk)->assertExists($thumbnail1);
        Storage::disk($this->disk)->assertExists($thumbnail2);
        Storage::disk($this->disk)->assertExists($thumbnail3);
        Storage::disk($this->disk)->assertExists($thumbnail4);
        Storage::disk($this->disk)->assertExists($thumbnail5);
        Storage::disk($this->disk)->assertMissing($thumbnail6);
    }

    /** @test */
    public function it_doesnt_generate_any_additional_styles_for_an_uploaded_image_by_default()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile()))->upload();
        $thumbnail = $this->imageThumbnail($file);

        Storage::disk($this->disk)->assertExists($file->getPath() . '/' . $file->getName());
        Storage::disk($this->disk)->assertExists($thumbnail);

        $this->assertCount(2, Storage::disk($this->disk)->files(null, true));
    }

    /** @test */
    public function it_can_generate_additional_styles_for_an_uploaded_image_of_a_model_record()
    {
        $model = new class extends UploadPost {
            public function getUploadConfig()
            {
                return [
                    'images' => [
                        'styles' => [
                            'image' => [
                                'portrait' => [
                                    'width' => '50',
                                    'height' => '200',
                                ],
                                'landscape' => [
                                    'width' => '210',
                                    'height' => '60',
                                ],
                            ],
                        ]
                    ]
                ];
            }
        };

        Storage::fake($this->disk);

        $file = (new UploadService($this->imageFile(), $model, 'image'))->upload();
        $portrait = $this->imageStyle($file, 'portrait');
        $landscape = $this->imageStyle($file, 'landscape');

        $portraitSize = getimagesize(Storage::disk($this->disk)->path($portrait));
        $landscapeSize = getimagesize(Storage::disk($this->disk)->path($landscape));

        Storage::disk($this->disk)->assertExists($portrait);
        Storage::disk($this->disk)->assertExists($landscape);

        $this->assertEquals(50, $portraitSize[0]);
        $this->assertEquals(200, $portraitSize[1]);
        $this->assertEquals(210, $landscapeSize[0]);
        $this->assertEquals(60, $landscapeSize[1]);
    }

    /** @test */
    public function it_keeps_old_uploads_and_records_by_default_when_updating_a_model_upload()
    {
        $post = UploadPost::create([
            'name' => 'Test Post Name',
        ]);

        Storage::fake($this->disk);

        $oldFile = (new UploadService($this->imageFile(), $post, 'image'))->upload();

        $post->update([
            'image' => $oldFile->getPath() . '/' . $oldFile->getName()
        ]);

        $newFile = (new UploadService($this->imageFile(), $post, 'image'))->upload();

        Storage::disk($this->disk)->assertExists($oldFile->getPath() . '/' . $oldFile->getName());
        Storage::disk($this->disk)->assertExists($newFile->getPath() . '/' . $newFile->getName());

        $this->assertEquals(2, Upload::count());
        $this->assertEquals($oldFile->getName(), Upload::oldest('id')->first()->name);
        $this->assertEquals($newFile->getName(), Upload::latest('id')->first()->name);
    }

    /** @test */
    public function it_can_remove_old_uploads_and_records_when_updating_a_model_upload_if_specified_in_model_method()
    {
        $model = new class extends UploadPost {
            public function getUploadConfig()
            {
                return [
                    'storage' => [
                        'keep_old' => false
                    ]
                ];
            }
        };

        $post = $model->create([
            'name' => 'Test Post Name',
        ]);

        Storage::fake($this->disk);

        $oldFile = (new UploadService($this->imageFile(), $post, 'image'))->upload();

        $post->update([
            'image' => $oldFile->getPath() . '/' . $oldFile->getName()
        ]);

        $newFile = (new UploadService($this->imageFile(), $post, 'image'))->upload();

        Storage::disk($this->disk)->assertMissing($oldFile->getPath() . '/' . $oldFile->getName());
        Storage::disk($this->disk)->assertExists($newFile->getPath() . '/' . $newFile->getName());

        $this->assertEquals(1, Upload::count());
        $this->assertEquals($newFile->getName(), Upload::first()->name);
    }

    /** @test */
    public function it_removes_partially_uploaded_files_from_storage_if_an_error_occurred_along_the_way()
    {
        $model = new class extends UploadPost {
            public function getUploadConfig()
            {
                return [
                    'images' => [
                        'styles' => [
                            'image' => [
                                'portrait' => [
                                    'width' => '50',
                                ],
                            ],
                        ]
                    ]
                ];
            }
        };

        Storage::fake($this->disk);

        try {
            (new UploadService($this->imageFile(), $model, 'image'))->upload();
        } catch (UploadException $e) {
            $this->assertCount(0, Storage::disk($this->disk)->files(null, true));
        }
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
     * @param string $style
     * @return mixed
     */
    protected function imageStyle(UploadService $original, $style)
    {
        $path = $original->getPath() . '/' . $original->getName();

        return substr_replace(
            $path, '_' . $style, strpos($path, '.'), 0
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

    /**
     * @param UploadService $original
     * @param string $style
     * @return mixed
     */
    protected function videoStyle(UploadService $original, $style)
    {
        $path = $original->getPath() . '/' . $original->getName();

        return substr_replace(
            $path, '_' . $style, strpos($path, '.'), 0
        );
    }
}
