<?php

namespace Varbox\Tests\Integration\Helpers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Varbox\Helpers\UploadedHelper;
use Varbox\Services\UploadService;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\UploadPost;

class UploadedHelperTest extends TestCase
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
    public function it_can_return_the_url_of_an_uploaded_image()
    {
        Storage::fake($this->disk);

        $image = (new UploadService($this->imageFile()))->upload();
        $url = (new UploadedHelper($image->getPath() . '/' . $image->getName()))->url();

        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $url));
    }

    /** @test */
    public function it_can_return_the_url_of_an_uploaded_video()
    {
        Storage::fake($this->disk);

        $video = (new UploadService($this->videoFile()))->upload();
        $url = (new UploadedHelper($video->getPath() . '/' . $video->getName()))->url();

        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $url));
    }

    /** @test */
    public function it_can_return_the_url_of_an_uploaded_audio()
    {
        Storage::fake($this->disk);

        $audio = (new UploadService($this->audioFile()))->upload();
        $url = (new UploadedHelper($audio->getPath() . '/' . $audio->getName()))->url();

        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $url));
    }

    /** @test */
    public function it_can_return_the_url_of_an_uploaded_file()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->pdfFile()))->upload();
        $url = (new UploadedHelper($file->getPath() . '/' . $file->getName()))->url();

        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $url));
    }

    /** @test */
    public function it_can_return_the_url_for_a_style_of_an_uploaded_image()
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

        $image = (new UploadService($this->imageFile(), $model, 'image'))->upload();
        $portrait = (new UploadedHelper($image->getPath() . '/' . $image->getName()))->url('portrait');
        $landscape = (new UploadedHelper($image->getPath() . '/' . $image->getName()))->url('landscape');

        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $portrait));
        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $landscape));
    }

    /** @test */
    public function it_can_return_the_path_of_an_uploaded_image()
    {
        Storage::fake($this->disk);

        $image = (new UploadService($this->imageFile()))->upload();
        $path = (new UploadedHelper($image->getPath() . '/' . $image->getName()))->path();

        Storage::disk($this->disk)->assertExists($path);
    }

    /** @test */
    public function it_can_return_the_path_of_an_uploaded_video()
    {
        Storage::fake($this->disk);

        $video = (new UploadService($this->videoFile()))->upload();
        $path = (new UploadedHelper($video->getPath() . '/' . $video->getName()))->path();

        Storage::disk($this->disk)->assertExists($path);
    }

    /** @test */
    public function it_can_return_the_path_of_an_uploaded_audio()
    {
        Storage::fake($this->disk);

        $audio = (new UploadService($this->audioFile()))->upload();
        $path = (new UploadedHelper($audio->getPath() . '/' . $audio->getName()))->path();

        Storage::disk($this->disk)->assertExists($path);
    }

    /** @test */
    public function it_can_return_the_path_of_an_uploaded_file()
    {
        Storage::fake($this->disk);

        $file = (new UploadService($this->pdfFile()))->upload();
        $path = (new UploadedHelper($file->getPath() . '/' . $file->getName()))->path();

        Storage::disk($this->disk)->assertExists($path);
    }

    /** @test */
    public function it_can_return_the_path_for_a_style_of_an_uploaded_image()
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

        $image = (new UploadService($this->imageFile(), $model, 'image'))->upload();
        $portrait = (new UploadedHelper($image->getPath() . '/' . $image->getName()))->path('portrait');
        $landscape = (new UploadedHelper($image->getPath() . '/' . $image->getName()))->path('landscape');

        Storage::disk($this->disk)->assertExists($portrait);
        Storage::disk($this->disk)->assertExists($landscape);
    }

    /** @test */
    public function it_can_return_the_thumbnail_of_an_uploaded_image()
    {
        Storage::fake($this->disk);

        $image = (new UploadService($this->imageFile()))->upload();
        $thumbnail = (new UploadedHelper($image->getPath() . '/' . $image->getName()))->thumbnail();

        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $thumbnail));
    }

    /** @test */
    public function it_can_return_the_thumbnails_of_an_uploaded_video()
    {
        Storage::fake($this->disk);

        $video = (new UploadService($this->videoFile()))->upload();
        $thumbnail1 = (new UploadedHelper($video->getPath() . '/' . $video->getName()))->thumbnail(1);
        $thumbnail2 = (new UploadedHelper($video->getPath() . '/' . $video->getName()))->thumbnail(2);
        $thumbnail3 = (new UploadedHelper($video->getPath() . '/' . $video->getName()))->thumbnail(3);

        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $thumbnail1));
        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $thumbnail2));
        Storage::disk($this->disk)->assertExists(str_replace('/storage', '', $thumbnail3));
    }

    /** @test */
    public function it_can_determine_if_a_file_exists_on_disk()
    {
        Storage::fake($this->disk);

        $image = (new UploadService($this->imageFile()))->upload();

        $this->assertTrue(uploaded($image->getPath() . '/' . $image->getName())->exists());
        $this->assertFalse(uploaded($image->getPath() . '/not-found/' . $image->getName())->exists());
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
