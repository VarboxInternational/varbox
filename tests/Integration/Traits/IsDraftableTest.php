<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Post;

class IsDraftableTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Post
     */
    protected $post;

    /** @test */
    public function it_can_store_a_new_record_as_draft()
    {
        $draft = (new Post)->saveAsDraft([
            'name' => 'Post test name',
            'content' => 'Post test content',
            'views' => 100,
            'approved' => true,
        ]);

        $this->assertTrue($draft->exists);
        $this->assertNotNull($draft->{$draft->getDraftedAtColumn()});

        $this->assertEquals('Post test name', $draft->name);
        $this->assertEquals('Post test content', $draft->content);
    }

    /** @test */
    public function it_can_update_an_existing_record_as_draft()
    {
        $this->createPost();

        $draft = $this->post->saveAsDraft();

        $this->assertNotNull($draft->{$draft->getDraftedAtColumn()});

        $this->assertEquals('Post test name', $draft->name);
        $this->assertEquals('Post test content', $draft->content);
    }

    /** @test */
    public function it_can_update_an_existing_record_as_draft_and_modify_it_at_the_same_time()
    {
        $this->createPost();

        $draft = $this->post->saveAsDraft([
            'name' => 'Draft test name',
            'content' => 'Draft test content',
        ]);

        $this->assertNotNull($draft->{$draft->getDraftedAtColumn()});

        $this->assertEquals('Draft test name', $draft->name);
        $this->assertEquals('Draft test content', $draft->content);
    }

    /** @test */
    public function it_can_publish_a_drafted_record()
    {
        $this->createPost();

        $draft = $this->post->saveAsDraft();

        $this->assertNotNull($draft->{$draft->getDraftedAtColumn()});

        $published = $draft->publishDraft();

        $this->assertNull($published->{$published->getDraftedAtColumn()});
    }

    /** @test */
    public function it_can_determine_if_a_record_is_drafted()
    {
        $this->createPost();

        $this->post->saveAsDraft();
        $this->post = $this->post->fresh();

        $this->assertTrue($this->post->isDrafted());

        $this->post->publishDraft();
        $this->post = $this->post->fresh();

        $this->assertFalse($this->post->isDrafted());
    }

    /** @test */
    public function it_has_a_method_for_returning_the_drafted_at_column()
    {
        $post = new Post;

        $this->assertEquals('drafted_at',$post->getDraftedAtColumn());
    }


    /** @test */
    public function it_has_a_method_for_returning_the_qualified_drafted_at_column()
    {
        $post = new Post;

        $this->assertEquals($post->getTable() . '.drafted_at', $post->getQualifiedDraftedAtColumn());
    }

    /** @test */
    public function it_excludes_drafted_records_by_default_when_fetching()
    {
        $this->createPosts();

        $this->assertEquals(2, Post::count());

        $this->post->saveAsDraft();

        $this->assertEquals(1, Post::count());
    }

    /** @test */
    public function it_can_include_drafted_records_when_fetching()
    {
        $this->createPosts();

        $this->post->saveAsDraft();

        $this->assertEquals(2, Post::withDrafts()->count());
    }

    /** @test */
    public function it_can_exclude_drafted_records_when_fetching()
    {
        $this->createPosts();

        $this->post->saveAsDraft();

        $this->assertEquals(1, Post::withoutDrafts()->count());
    }

    /** @test */
    public function it_can_fetch_only_drafted_records()
    {
        $this->createPosts();

        $this->post->saveAsDraft();

        $this->assertEquals(1, Post::onlyDrafts()->count());
    }

    /**
     * @return void
     */
    protected function createPost()
    {
        $this->post = Post::create([
            'name' => 'Post test name',
            'content' => 'Post test content',
            'views' => 100,
            'approved' => true,
            'published_at' => today(),
        ]);
    }

    /**
     * @param Post|null $model
     * @return void
     */
    protected function createPosts()
    {
        $this->createPost();

        Post::create([
            'name' => 'Another test name',
            'content' => 'Another test content',
            'views' => 10,
            'approved' => false,
            'published_at' => today(),
        ]);
    }
}
