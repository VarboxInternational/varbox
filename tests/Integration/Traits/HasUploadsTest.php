<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageServiceProvider;
use Varbox\Helpers\UploadedHelper;
use Varbox\Models\Upload;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\UploadPost;

class HasUploadsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var UploadPost
     */
    protected $post;

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

    /** @test */
    public function it_can_upload_a_file()
    {
        $this->createPost();

        Storage::fake($this->disk);

        $this->assertEquals(0, Upload::count());

        $image = $this->post->uploadFile($this->imageFile(), 'image');

        Storage::disk($this->disk)->assertExists($image->getPath() . '/' . $image->getName());

        $this->assertEquals(1, Upload::count());
    }

    /** @test */
    public function it_can_return_the_uploaded_helper()
    {
        $this->createPost();

        Storage::fake($this->disk);

        $image = $this->post->uploadFile($this->imageFile(), 'image');

        $this->post->update([
            'image' => $image->getPath() . '/' . $image->getName(),
        ]);

        $uploaded = $this->post->uploadedFile('image');

        $this->assertEquals(UploadedHelper::class, get_class($uploaded));
        $this->assertEquals($uploaded->getOriginal(), $this->post->image);
        $this->assertEquals($uploaded->getFile(), $this->post->image);
    }

    /**
     * @return void
     */
    protected function createPost()
    {
        $this->post = UploadPost::create([
            'name' => 'Post test name',
        ]);
    }

    /**
     * @return UploadedFile
     */
    protected function imageFile()
    {
        return new UploadedFile(__DIR__ . '/../../Files/test.jpg', 'test.jpg', null, null, true);
    }
}
