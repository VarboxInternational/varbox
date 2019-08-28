<?php

namespace Varbox\Tests\Http\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Varbox\Tests\Controllers\PreviewController;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\PreviewPost;
use Varbox\Tests\Models\PreviewTag;

class CanPreviewTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var PreviewPost
     */
    protected $post;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::post('/_test/preview-post/{post}', PreviewController::class . '@preview');
    }

    /** @test */
    public function it_can_preview_a_model()
    {
        $this->withoutExceptionHandling();
        $this->createPost();

        $response = $this->post('/_test/preview-post/' . $this->post->id, [
            'name' => 'Post name modified',
            'content' => 'Post content modified',
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('Post name modified', $response->getContent());
        $this->assertStringContainsString('Post content modified', $response->getContent());

        $this->assertEquals('Post test name', $this->post->name);
        $this->assertEquals('Post test content', $this->post->content);
    }

    /** @test */
    public function it_can_preview_a_model_with_pivoted_relations()
    {
        $this->withoutExceptionHandling();
        $this->createPost();
        $this->createTags();

        $response = $this->post('/_test/preview-post/' . $this->post->id, [
            'name' => 'Post name modified',
            'content' => 'Post content modified',
            'tags' => PreviewTag::all()->pluck('id')->toArray(),
        ]);

        $response->assertStatus(200);

        $this->assertStringContainsString('Post name modified', $response->getContent());
        $this->assertStringContainsString('Post content modified', $response->getContent());
        $this->assertStringContainsString('Test tag 1', $response->getContent());
        $this->assertStringContainsString('Test tag 2', $response->getContent());

        $this->assertEquals('Post test name', $this->post->name);
        $this->assertEquals('Post test content', $this->post->content);
        $this->assertEquals(0, $this->post->tags()->count());
    }

    /**
     * @return void
     */
    protected function createPost()
    {
        $this->post = PreviewPost::create([
            'name' => 'Post test name',
            'content' => 'Post test content',
        ]);
    }

    /**
     * @return void
     */
    protected function createTags()
    {
        PreviewTag::create([
            'name' => 'Test tag 1',
        ]);

        PreviewTag::create([
            'name' => 'Test tag 2',
        ]);
    }
}
