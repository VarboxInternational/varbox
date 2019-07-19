<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageServiceProvider;
use Varbox\Exceptions\UploadException;
use Varbox\Models\Activity;
use Varbox\Models\Role;
use Varbox\Models\Upload;
use Varbox\Models\User;
use Varbox\Services\UploadService;

class UploadsTest extends TestCase
{
    /**
     * @var Upload
     */
    protected $image;

    /**
     * @var Upload
     */
    protected $video;

    /**
     * @var Upload
     */
    protected $audio;

    /**
     * @var Upload
     */
    protected $file;

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

    

    /**
     * @return void
     * @throws UploadException
     */
    protected function createImage()
    {
        $image = (new UploadService($this->imageFile()))->upload();

        $this->image = Upload::whereName($image->getName())->first();
    }

    /**
     * @return void
     * @throws UploadException
     */
    protected function createVideo()
    {
        $video = (new UploadService($this->videoFile()))->upload();

        $this->video = Upload::whereName($video->getName())->first();
    }

    /**
     * @return void
     * @throws UploadException
     */
    protected function createAudio()
    {
        $audio = (new UploadService($this->audioFile()))->upload();

        $this->audio = Upload::whereName($audio->getName())->first();
    }

    /**
     * @return void
     * @throws UploadException
     */
    protected function createFile()
    {
        $file = (new UploadService($this->pdfFile()))->upload();

        $this->file = Upload::whereName($file->getName())->first();
    }

    /**
     * @return UploadedFile
     */
    protected function imageFile()
    {
        return new UploadedFile(__DIR__ . '/../Files/test.jpg', 'test.jpg', null, null, true);
    }

    /**
     * @return UploadedFile
     */
    protected function videoFile()
    {
        return new UploadedFile(__DIR__ . '/../Files/test.mp4', 'test.mp4', null, null, true);
    }

    /**
     * @return UploadedFile
     */
    protected function audioFile()
    {
        return new UploadedFile(__DIR__ . '/../Files/test.mp3', 'test.mp3', null, null, true);
    }

    /**
     * @return UploadedFile
     */
    protected function pdfFile()
    {
        return new UploadedFile(__DIR__ . '/../Files/test.pdf', 'test.pdf', null, null, true);
    }
}
