<?php

namespace Varbox\Tests\Browser;

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

    /** @test */
    public function an_admin_can_upload_a_file_if_it_has_permission()
    {
        $this->admin->grantPermission('uploads-list');
        $this->admin->grantPermission('uploads-upload');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->attach('input.dz-hidden-input', __DIR__ . '/../Files/test.pdf')
                ->waitFor('.dz-size')
                ->visit('/admin/uploads')
                ->assertRecordsCount(1)
                ->assertSee('test.pdf');
        });

        $this->assertEquals(1, Upload::count());
        $this->assertEquals(1, count(Storage::disk($this->disk)->files(null, true)) - 1);

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_cannot_upload_a_file_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('uploads-list');
        $this->admin->revokePermission('uploads-upload');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->attach('input.dz-hidden-input', __DIR__ . '/../Files/test.pdf')
                ->visit('/admin/uploads')
                ->assertSee('No records found');
        });

        $this->assertEquals(0, Upload::count());
    }

    /** @test */
    public function an_admin_can_upload_an_image()
    {
        $this->admin->grantPermission('uploads-list');
        $this->admin->grantPermission('uploads-upload');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->attach('input.dz-hidden-input', __DIR__ . '/../Files/test.jpg')
                ->waitFor('.dz-complete')
                ->visit('/admin/uploads')
                ->assertRecordsCount(1)
                ->assertSee('test.jpg');
        });

        $this->assertEquals(1, Upload::count());
        $this->assertEquals(2, count(Storage::disk($this->disk)->files(null, true)) - 1);

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_upload_a_video()
    {
        $this->admin->grantPermission('uploads-list');
        $this->admin->grantPermission('uploads-upload');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->attach('input.dz-hidden-input', __DIR__ . '/../Files/test.mp4')
                ->waitFor('.dz-complete')
                ->visit('/admin/uploads')
                ->assertRecordsCount(1)
                ->assertSee('test.mp4');
        });

        $this->assertEquals(1, Upload::count());
        $this->assertEquals(4, count(Storage::disk($this->disk)->files(null, true)) - 1);

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_upload_an_audio()
    {
        $this->admin->grantPermission('uploads-list');
        $this->admin->grantPermission('uploads-upload');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->attach('input.dz-hidden-input', __DIR__ . '/../Files/test.mp3')
                ->waitFor('.dz-complete')
                ->visit('/admin/uploads')
                ->assertRecordsCount(1)
                ->assertSee('test.mp3');
        });

        $this->assertEquals(1, Upload::count());
        $this->assertEquals(1, count(Storage::disk($this->disk)->files(null, true)) - 1);

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_delete_an_upload_is_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createImage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->deleteRecord($this->image->original_name)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/uploads/', new Upload)
                ->assertDontSee($this->image->original_name);
        });

        $this->assertEquals(0, count(Storage::disk($this->disk)->files(null, true)) - 1);

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_delete_an_upload_is_it_has_permission()
    {
        $this->admin->grantPermission('uploads-list');
        $this->admin->grantPermission('uploads-delete');

        $this->createImage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->deleteRecord($this->image->original_name)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/uploads/', new Upload)
                ->assertDontSee($this->image->original_name);
        });

        $this->assertEquals(0, count(Storage::disk($this->disk)->files(null, true)) - 1);

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_cannot_delete_an_upload_is_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('uploads-list');
        $this->admin->revokePermission('uploads-delete');

        $this->createImage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });

        $this->assertEquals(2, count(Storage::disk($this->disk)->files(null, true)) - 1);

        $this->deleteUploads();
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
