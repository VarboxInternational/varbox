<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\CacheComment;
use Varbox\Tests\Models\CachePost;

class IsCacheableTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('database.redis.client', 'predis');
    }

    /** @test */
    public function it_can_insert_records()
    {
        $this->app['config']->set('cache.default', 'array');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        CachePost::query()->insert([
            'name' => 'Test post name',
            'content' => 'Test post content',
        ]);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_update_records()
    {
        $this->app['config']->set('cache.default', 'array');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        CachePost::query()->where([
            'name' => 'Test post name',
        ])->update([
            'name' => 'Test post name modified',
        ]);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_delete_records()
    {
        $this->app['config']->set('cache.default', 'array');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();
        CachePost::query()->delete();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_disable_query_caching_with_redis()
    {
        $this->app['config']->set('cache.default', 'redis');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        app(CachePost::class)->disableQueryCache();
        app(CacheComment::class)->disableQueryCache();

        DB::enableQueryLog();

        $this->executePostQueries();
        $this->assertEquals(20, count(DB::getQueryLog()));

        $this->executePostQueries();
        $this->assertEquals(40, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    /** @test */
    public function it_can_enable_query_caching_with_redis()
    {
        $this->app['config']->set('cache.default', 'redis');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        app(CachePost::class)->disableQueryCache();
        app(CacheComment::class)->disableQueryCache();

        DB::enableQueryLog();

        $this->executePostQueries();
        $this->assertEquals(20, count(DB::getQueryLog()));

        DB::flushQueryLog();

        app(CachePost::class)->enableQueryCache();
        app(CacheComment::class)->enableQueryCache();

        $this->executePostQueries();
        $this->assertEquals(2, count(DB::getQueryLog()));

        DB::flushQueryLog();

        $this->executePostQueries();
        $this->assertEquals(0, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    /** @test */
    public function it_caches_duplicate_queries_using_redis()
    {
        $this->app['config']->set('cache.default', 'redis');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        DB::enableQueryLog();

        $this->executePostQueries();
        $this->assertEquals(2, count(DB::getQueryLog()));

        DB::flushQueryLog();

        $this->executePostQueries();
        $this->assertEquals(0, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    /** @test */
    public function it_caches_only_duplicate_queries_using_redis()
    {
        $this->app['config']->set('cache.default', 'redis');
        $this->app['config']->set('varbox.query-cache.duplicate.enabled', true);

        $this->createPostsAndComments();

        DB::enableQueryLog();

        $this->executePostQueries();

        DB::disableQueryLog();

        $this->assertEquals(2, count(DB::getQueryLog()));
    }

    /** @test */
    public function it_removes_cached_queries_from_redis_when_creating_a_new_record()
    {
        $this->app['config']->set('cache.default', 'redis');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        DB::enableQueryLog();

        $this->executePostQueries();

        CachePost::create([
            'name' => 'New post name',
            'content' => 'New post content',
        ]);

        DB::flushQueryLog();

        $this->executePostQueries();
        $this->assertEquals(2, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    /** @test */
    public function it_removes_cached_queries_from_redis_when_updating_a_new_record()
    {
        $this->app['config']->set('cache.default', 'redis');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        DB::enableQueryLog();

        $this->executePostQueries();

        CachePost::first()->update([
            'name' => 'Updated post name',
        ]);

        DB::flushQueryLog();

        $this->executePostQueries();
        $this->assertEquals(2, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    /** @test */
    public function it_removes_related_cached_queries_from_redis_when_creating_a_new_record()
    {
        $this->app['config']->set('cache.default', 'redis');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        DB::enableQueryLog();

        $this->executePostQueries();
        $this->executeCommentQueries();

        CachePost::create([
            'name' => 'New post name',
            'content' => 'New post content',
        ]);

        DB::flushQueryLog();

        $this->executePostQueries();
        $this->executeCommentQueries();

        $this->assertEquals(4, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    /** @test */
    public function it_removes_related_cached_queries_from_redis_when_updating_a_new_record()
    {
        $this->app['config']->set('cache.default', 'redis');
        $this->app['config']->set('varbox.query-cache.all.enabled', true);

        $this->createPostsAndComments();

        DB::enableQueryLog();

        $this->executePostQueries();
        $this->executeCommentQueries();

        CachePost::first()->update([
            'name' => 'Updated post name',
        ]);

        DB::flushQueryLog();

        $this->executePostQueries();
        $this->executeCommentQueries();

        $this->assertEquals(4, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    /** @test */
    public function it_caches_only_duplicate_queries_using_array()
    {
        $this->app['config']->set('cache.default', 'array');
        $this->app['config']->set('varbox.query-cache.duplicate.enabled', true);

        DB::enableQueryLog();

        $this->executePostQueries();

        DB::disableQueryLog();

        $this->assertEquals(2, count(DB::getQueryLog()));
    }

    /**
     * @return void
     */
    protected function createPostsAndComments()
    {
        for ($i = 1; $i <= 3; $i++) {
            $post = CachePost::create([
                'name' => 'Test post name '.$i,
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
            CachePost::all();
        }

        for ($i = 1; $i <= 10; $i++) {
            CachePost::where(1)->get();
        }
    }

    /**
     * @return void
     */
    protected function executeCommentQueries()
    {
        for ($i = 1; $i <= 10; $i++) {
            CacheComment::all();
        }

        for ($i = 1; $i <= 10; $i++) {
            CacheComment::where(1)->get();
        }
    }
}
