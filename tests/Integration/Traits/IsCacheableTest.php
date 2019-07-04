<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Varbox\Contracts\QueryCacheServiceContract;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Comment;
use Varbox\Tests\Models\Post;

class IsCacheableTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_insert_records()
    {
        $this->app['config']->set('varbox.varbox-cache.query.all.enabled', true);
        $this->app['config']->set('varbox.varbox-cache.query.all.store', 'array');

        $this->createPostsAndComments();

        Post::query()->insert([
            'name' => 'Test post name',
            'slug' => 'test-post-name',
            'content' => 'Test post content',
        ]);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_update_records()
    {
        $this->app['config']->set('varbox.varbox-cache.query.all.enabled', true);
        $this->app['config']->set('varbox.varbox-cache.query.all.store', 'array');

        $this->createPostsAndComments();

        Post::query()->where([
            'name' => 'Test post name',
        ])->update([
            'name' => 'Test post name modified',
        ]);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_delete_records()
    {
        $this->app['config']->set('varbox.varbox-cache.query.all.enabled', true);
        $this->app['config']->set('varbox.varbox-cache.query.all.store', 'array');

        $this->createPostsAndComments();
        Post::query()->delete();

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    protected function createPostsAndComments()
    {
        for ($i = 1; $i <= 3; $i++) {
            $post = Post::create([
                'name' => 'Test post name '.$i,
                'slug' => 'test-post-slug-'.$i,
                'content' => 'Test post content'.$i,
            ]);

            for ($j = 1; $j <= 3; $j++) {
                $post->comments()->create([
                    'title' => 'Test comment title '.$i.' '.$j,
                    'content' => 'Test comment content '.$i.' '.$j,
                ]);
            }
        }
    }

    /**
     * @return void
     */
    protected function executePostQueries()
    {
        for ($i = 1; $i <= 10; $i++) {
            Post::all();
        }

        for ($i = 1; $i <= 10; $i++) {
            Post::where(1)->get();
        }
    }

    /**
     * @return void
     */
    protected function executeCommentQueries()
    {
        for ($i = 1; $i <= 10; $i++) {
            Comment::all();
        }

        for ($i = 1; $i <= 10; $i++) {
            Comment::where(1)->get();
        }
    }
}
