<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Helpers\UploadedHelper;
use Varbox\Models\Upload;
use Varbox\Services\UploadService;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class UploadTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Upload
     */
    protected $imageUpload;

    /**
     * @var Upload
     */
    protected $videoUpload;

    /**
     * @var Upload
     */
    protected $audioUpload;

    /**
     * @var Upload
     */
    protected $fileUpload;

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Upload::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Upload::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Upload::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Upload::class));
    }

    /** @test */
    public function it_can_get_the_uploaded_helper_instance()
    {
        $this->createImageUpload();

        $this->assertTrue($this->imageUpload->helper instanceof UploadedHelper);
    }

    /** @test */
    public function it_can_return_the_size_in_megabytes()
    {
        $this->createImageUpload();

        $this->assertEquals('1.00', $this->imageUpload->size_mb);
    }

    /** @test */
    public function it_can_return_only_records_having_the_specified_types()
    {
        $this->createUploads();

        $uploads = Upload::onlyTypes(UploadService::getImageType())->get();

        $this->assertEquals(1, $uploads->count());
        $this->assertEquals('test-image', $uploads->first()->name);

        $uploads = Upload::onlyTypes(UploadService::getImageType(), UploadService::getVideoType())->get();

        $this->assertEquals(2, $uploads->count());
        $this->assertEquals('test-image', $uploads->first()->name);
        $this->assertEquals('test-video', $uploads->last()->name);
    }

    /** @test */
    public function it_can_return_only_records_not_having_the_specified_types()
    {
        $this->createUploads();

        $uploads = Upload::excludingTypes(UploadService::getImageType())->get();

        $this->assertEquals(3, $uploads->count());

        $uploads = Upload::excludingTypes(UploadService::getImageType(), UploadService::getVideoType())->get();

        $this->assertEquals(2, $uploads->count());
        $this->assertEquals('test-audio', $uploads->first()->name);
        $this->assertEquals('test-file', $uploads->last()->name);
    }

    /** @test */
    public function it_can_return_only_records_with_the_specified_extensions()
    {
        $this->createUploads();

        $uploads = Upload::withExtensions('jpg')->get();

        $this->assertEquals(1, $uploads->count());
        $this->assertEquals('test-image', $uploads->first()->name);

        $uploads = Upload::withExtensions('jpg', 'mp4')->get();

        $this->assertEquals(2, $uploads->count());
        $this->assertEquals('test-image', $uploads->first()->name);
        $this->assertEquals('test-video', $uploads->last()->name);
    }

    /** @test */
    public function it_can_return_only_records_without_the_specified_extensions()
    {
        $this->createUploads();

        $uploads = Upload::withoutExtensions('jpg')->get();

        $this->assertEquals(3, $uploads->count());

        $uploads = Upload::withoutExtensions('jpg', 'mp4')->get();

        $this->assertEquals(2, $uploads->count());
        $this->assertEquals('test-audio', $uploads->first()->name);
        $this->assertEquals('test-file', $uploads->last()->name);
    }

    /** @test */
    public function it_can_return_only_records_with_the_specified_mimes()
    {
        $this->createUploads();

        $uploads = Upload::withMimes('image/jpeg')->get();

        $this->assertEquals(1, $uploads->count());
        $this->assertEquals('test-image', $uploads->first()->name);

        $uploads = Upload::withMimes('image/jpeg', 'video/mp4')->get();

        $this->assertEquals(2, $uploads->count());
        $this->assertEquals('test-image', $uploads->first()->name);
        $this->assertEquals('test-video', $uploads->last()->name);
    }

    /** @test */
    public function it_can_return_only_records_without_the_specified_mimes()
    {
        $this->createUploads();

        $uploads = Upload::withoutMimes('image/jpeg')->get();

        $this->assertEquals(3, $uploads->count());

        $uploads = Upload::withoutMimes('image/jpeg', 'video/mp4')->get();

        $this->assertEquals(2, $uploads->count());
        $this->assertEquals('test-audio', $uploads->first()->name);
        $this->assertEquals('test-file', $uploads->last()->name);
    }

    /** @test */
    public function it_can_return_only_records_having_the_size_between_two_values()
    {
        $this->createUploads();

        $uploads = Upload::sizeBetween(0, 1)->get();

        $this->assertEquals(1, $uploads->count());
        $this->assertEquals('test-image', $uploads->first()->name);

        $uploads = Upload::sizeBetween(3, 4)->get();

        $this->assertEquals(2, $uploads->count());
        $this->assertEquals('test-audio', $uploads->first()->name);
        $this->assertEquals('test-file', $uploads->last()->name);
    }

    /** @test */
    public function it_can_return_the_records_ordered_alphabetically_by_original_name()
    {
        $this->createUploads();

        $uploads = Upload::alphabetically()->get();

        $this->assertEquals(4, $uploads->count());
        $this->assertEquals('Test Original Audio', $uploads->first()->original_name);
        $this->assertEquals('Test Original Video', $uploads->last()->original_name);
    }

    /** @test */
    public function it_can_determine_if_a_record_is_for_an_uploaded_image()
    {
        $this->createUploads();

        $this->assertTrue($this->imageUpload->isImage());
        $this->assertFalse($this->videoUpload->isImage());
        $this->assertFalse($this->audioUpload->isImage());
        $this->assertFalse($this->fileUpload->isImage());
    }

    /** @test */
    public function it_can_determine_if_a_record_is_for_an_uploaded_video()
    {
        $this->createUploads();

        $this->assertFalse($this->imageUpload->isVideo());
        $this->assertTrue($this->videoUpload->isVideo());
        $this->assertFalse($this->audioUpload->isVideo());
        $this->assertFalse($this->fileUpload->isVideo());
    }

    /** @test */
    public function it_can_determine_if_a_record_is_for_an_uploaded_audio()
    {
        $this->createUploads();

        $this->assertFalse($this->imageUpload->isAudio());
        $this->assertFalse($this->videoUpload->isAudio());
        $this->assertTrue($this->audioUpload->isAudio());
        $this->assertFalse($this->fileUpload->isAudio());
    }

    /** @test */
    public function it_can_determine_if_a_record_is_for_an_uploaded_file()
    {
        $this->createUploads();

        $this->assertFalse($this->imageUpload->isFile());
        $this->assertFalse($this->videoUpload->isFile());
        $this->assertFalse($this->audioUpload->isFile());
        $this->assertTrue($this->fileUpload->isFile());
    }

    /** @test */
    public function it_can_return_all_available_file_types()
    {
        $types = Upload::getFileTypes();

        $this->assertArrayHasKey(UploadService::getImageType(), $types);
        $this->assertArrayHasKey(UploadService::getVideoType(), $types);
        $this->assertArrayHasKey(UploadService::getAudioType(), $types);
        $this->assertArrayHasKey(UploadService::getFileType(), $types);

        $this->assertEquals('Image', $types[UploadService::getImageType()]);
        $this->assertEquals('Video', $types[UploadService::getVideoType()]);
        $this->assertEquals('Audio', $types[UploadService::getAudioType()]);
        $this->assertEquals('File', $types[UploadService::getFileType()]);
    }

    /**
     * @return void
     */
    protected function createImageUpload()
    {
        $this->imageUpload = Upload::create([
            'name' => 'test-image',
            'original_name' => 'Test Original Image',
            'path' => '/test/images',
            'full_path' => '/test/images/image.jpg',
            'extension' => 'jpg',
            'size' => 1048576,
            'mime' => 'image/jpeg',
            'type' => UploadService::getImageType(),
        ]);
    }

    /**
     * @return void
     */
    protected function createVideoUpload()
    {
        $this->videoUpload = Upload::create([
            'name' => 'test-video',
            'original_name' => 'Test Original Video',
            'path' => '/test/videos',
            'full_path' => '/test/videos/image.mp4',
            'extension' => 'mp4',
            'size' => 2097152,
            'mime' => 'video/mp4',
            'type' => UploadService::getVideoType(),
        ]);
    }

    /**
     * @return void
     */
    protected function createAudioUpload()
    {
        $this->audioUpload = Upload::create([
            'name' => 'test-audio',
            'original_name' => 'Test Original Audio',
            'path' => '/test/audios',
            'full_path' => '/test/audios/audio.mp3',
            'extension' => 'mp3',
            'size' => 3145728,
            'mime' => 'audio/mp3',
            'type' => UploadService::getAudioType(),
        ]);
    }

    /**
     * @return void
     */
    protected function createFileUpload()
    {
        $this->fileUpload = Upload::create([
            'name' => 'test-file',
            'original_name' => 'Test Original File',
            'path' => '/test/files',
            'full_path' => '/test/files/file.mp3',
            'extension' => 'mp3',
            'size' => 4194304,
            'mime' => 'file/pdf',
            'type' => UploadService::getFileType(),
        ]);
    }

    /**
     * @return void
     */
    protected function createUploads()
    {
        $this->createImageUpload();
        $this->createVideoUpload();
        $this->createAudioUpload();
        $this->createFileUpload();
    }
}
