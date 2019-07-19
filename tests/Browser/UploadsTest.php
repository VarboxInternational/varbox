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

    /** @test */
    public function an_admin_can_view_an_uploaded_image()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createImage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->clickViewButton($this->image->original_name);

            $browser->driver->switchTo()->window(
                collect($browser->driver->getWindowHandles())->last()
            );

            $browser->assertPathIs(uploaded($this->image->full_path)->url());
        });

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_view_an_uploaded_video()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createVideo();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->clickViewButton($this->video->original_name);

            $browser->driver->switchTo()->window(
                collect($browser->driver->getWindowHandles())->last()
            );

            $browser->assertPathIs(uploaded($this->video->full_path)->url());
        });

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_view_an_uploaded_audio()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createAudio();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->clickViewButton($this->audio->original_name);

            $browser->driver->switchTo()->window(
                collect($browser->driver->getWindowHandles())->last()
            );

            $browser->assertPathIs(uploaded($this->audio->full_path)->url());
        });

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_filter_uploads_by_keyword()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createImage();
        $this->createVideo();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->filterRecordsByText('#search-input', $this->image->original_name)
                ->assertQueryStringHas('search', $this->image->original_name)
                ->assertRecordsCount(1)
                ->assertSee($this->image->original_name)
                ->assertDontSee($this->video->original_name)
                ->visit('/admin/uploads')
                ->filterRecordsByText('#search-input', $this->video->original_name)
                ->assertQueryStringHas('search', $this->video->original_name)
                ->assertRecordsCount(1)
                ->assertDontSee($this->image->original_name)
                ->assertSee($this->video->original_name);
        });

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_filter_uploads_by_file_type()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createImage();
        $this->createVideo();
        $this->createAudio();
        $this->createFile();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->filterRecordsBySelect('#type-input', UploadService::getImageType())
                ->assertQueryStringHas('type', UploadService::getImageType())
                ->assertRecordsCount(1)
                ->assertSee($this->image->original_name)
                ->assertDontSee($this->video->original_name)
                ->assertDontSee($this->audio->original_name)
                ->assertDontSee($this->file->original_name)
                ->visit('/admin/uploads')
                ->filterRecordsBySelect('#type-input', UploadService::getVideoType())
                ->assertQueryStringHas('type', UploadService::getVideoType())
                ->assertRecordsCount(1)
                ->assertDontSee($this->image->original_name)
                ->assertSee($this->video->original_name)
                ->assertDontSee($this->audio->original_name)
                ->assertDontSee($this->file->original_name)
                ->visit('/admin/uploads')
                ->filterRecordsBySelect('#type-input', UploadService::getAudioType())
                ->assertQueryStringHas('type', UploadService::getAudioType())
                ->assertRecordsCount(1)
                ->assertDontSee($this->image->original_name)
                ->assertDontSee($this->video->original_name)
                ->assertSee($this->audio->original_name)
                ->assertDontSee($this->file->original_name)
                ->visit('/admin/uploads')
                ->filterRecordsBySelect('#type-input', UploadService::getFileType())
                ->assertQueryStringHas('type', UploadService::getFileType())
                ->assertRecordsCount(1)
                ->assertDontSee($this->image->original_name)
                ->assertDontSee($this->video->original_name)
                ->assertDontSee($this->audio->original_name)
                ->assertSee($this->file->original_name);
        });

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_filter_uploads_by_min_size()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createVideo();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->resize(1500, 1000)
                ->visit('/admin/uploads')
                ->filterRecordsByText('size[0]', 1)
                ->assertSee($this->video->original_name)
                ->assertSee($this->video->size_mb)
                ->visit('/admin/uploads')
                ->filterRecordsByText('size[0]', 100)
                ->assertSee('No records found');
        });

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_filter_uploads_by_max_size()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createVideo();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->resize(1500, 1000)
                ->visit('/admin/uploads')
                ->filterRecordsByText('size[1]', 100)
                ->assertSee($this->video->original_name)
                ->assertSee($this->video->size_mb)
                ->visit('/admin/uploads')
                ->filterRecordsByText('size[1]', 1)
                ->assertSee('No records found');
        });

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_filter_uploads_by_start_date()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createFile();

        $past = today()->subDays(100)->format('Y-m-d');
        $future = today()->addDays(100)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->visit('/admin/uploads')
                ->assertSee($this->file->original_name)
                ->assertSee($this->file->size_mb)
                ->visit('/admin/uploads')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteUploads();
    }

    /** @test */
    public function an_admin_can_filter_uploads_by_end_date()
    {
        $this->admin->grantPermission('uploads-list');

        $this->createFile();

        $past = today()->subDays(100)->format('Y-m-d');
        $future = today()->addDays(100)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/uploads')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/uploads')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date',$future)
                ->visit('/admin/uploads')
                ->assertSee($this->file->original_name)
                ->assertSee($this->file->size_mb);
        });

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
