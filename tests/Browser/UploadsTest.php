<?php

namespace Varbox\Tests\Browser;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Varbox\Exceptions\UploadException;
use Varbox\Models\Upload;
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

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->assertPathIs('/admin/uploads')
                ->assertSee('Uploads');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('uploads-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->assertPathIs('/admin/uploads')
                ->assertSee('Uploads');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('uploads-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->assertSee('Unauthorized')
                ->assertDontSee('Uploads');
        });
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
     * @return void
     */
    protected function deleteUploads()
    {
        Upload::truncate();

        Storage::disk($this->disk)->deleteDirectory(
            Arr::first(Storage::disk($this->disk)->directories(null))
        );
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
