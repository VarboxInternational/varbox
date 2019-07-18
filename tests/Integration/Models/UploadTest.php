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
    protected $upload;

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
        $this->createUpload();

        $this->assertTrue($this->upload->helper instanceof UploadedHelper);
    }

    /** @test */
    public function it_can_return_the_size_in_megabytes()
    {
        $this->createUpload();

        $this->assertEquals('1.00', $this->upload->size_mb);
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

    /**
     * @return void
     */
    protected function createUpload()
    {
        $this->upload = Upload::create([
            'name' => 'Test Name',
            'original_name' => 'Test Original Name',
            'path' => '/test/path',
            'full_path' => '/test/full/path/file.ext',
            'extension' => 'ext',
            'size' => 1048576,
            'mime' => 'test/ext',
        ]);
    }

    /**
     * @return void
     */
    protected function createUploads()
    {
        Upload::create([
            'name' => 'test-image',
            'original_name' => 'Test Original Image',
            'path' => '/test/images',
            'full_path' => '/test/images/image.jpg',
            'extension' => 'jpg',
            'size' => 1048576,
            'mime' => 'image/jpeg',
            'type' => UploadService::getImageType(),
        ]);

        Upload::create([
            'name' => 'test-video',
            'original_name' => 'Test Original Video',
            'path' => '/test/videos',
            'full_path' => '/test/videos/image.mp4',
            'extension' => 'mp4',
            'size' => 1048576,
            'mime' => 'video/mp4',
            'type' => UploadService::getVideoType(),
        ]);

        Upload::create([
            'name' => 'test-audio',
            'original_name' => 'Test Original Audio',
            'path' => '/test/audios',
            'full_path' => '/test/audios/audio.mp3',
            'extension' => 'mp3',
            'size' => 1048576,
            'mime' => 'audio/mp3',
            'type' => UploadService::getAudioType(),
        ]);

        Upload::create([
            'name' => 'test-file',
            'original_name' => 'Test Original File',
            'path' => '/test/files',
            'full_path' => '/test/files/file.mp3',
            'extension' => 'mp3',
            'size' => 1048576,
            'mime' => 'file/pdf',
            'type' => UploadService::getFileType(),
        ]);
    }
}
