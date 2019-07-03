<?php

namespace Varbox\Tests\Integration\Traits;

use Exception;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Varbox\Options\ActivityOptions;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Post;

class HasActivityTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Post
     */
    protected $post;

    /** @test */
    public function it_morphs_many_activity_logs()
    {
        $this->createPost()->updatePost()->deletePost();

        $this->assertTrue($this->post->activity() instanceof MorphMany);
        $this->assertEquals(3, $this->post->activity()->count());
    }

    /** @test */
    public function it_creates_an_activity_log_when_creating_a_record()
    {
        $this->createPost();

        $this->assertEquals(1, $this->post->activity()->count());
        $this->assertEquals('created', $this->post->activity()->first()->event);
    }

    /** @test */
    public function it_creates_an_activity_log_when_updating_a_record()
    {
        $this->createPost()->updatePost();

        $this->assertEquals(2, $this->post->activity()->count());
        $this->assertEquals('updated', $this->post->activity()->latest('id')->first()->event);
    }

    /** @test */
    public function it_creates_an_activity_log_when_deleting_a_record()
    {
        $this->createPost()->deletePost();

        $this->assertEquals(2, $this->post->activity()->count());
        $this->assertEquals('deleted', $this->post->activity()->latest('id')->first()->event);
    }

    /** @test */
    public function it_can_skip_creating_any_activity_log_if_manually_specified()
    {
        $this->createPost((new Post)->doNotLogActivity())
            ->updatePost(false)->deletePost(false);

        $this->assertEquals(0, $this->post->activity()->count());
    }

    /** @test */
    public function it_ignores_creating_any_activity_log_if_disabled_from_config()
    {
        $this->app['config']->set('varbox.varbox-activity.enabled', false);

        $this->createPost()->updatePost()->deletePost();

        $this->assertEquals(0, $this->post->activity()->count());
    }

    /** @test */
    public function it_creates_activity_logs_only_for_the_specified_events()
    {
        $model = new class extends Post {
            public static function activityEventsToBeLogged(): Collection
            {
                return collect(['created']);
            }
        };

        $this->createPost($model)->updatePost()->deletePost();

        $this->assertEquals(1, $this->post->activity()->count());
        $this->assertEquals('created', $this->post->activity()->first()->event);

        $model = new class extends Post {
            public static function activityEventsToBeLogged(): Collection
            {
                return collect(['updated', 'deleted']);
            }
        };

        $this->createPost($model)->updatePost()->deletePost();

        $this->assertEquals(2, $this->post->activity()->count());
        $this->assertEquals('updated', $this->post->activity()->oldest('id')->first()->event);
        $this->assertEquals('deleted', $this->post->activity()->latest('id')->first()->event);
    }

    /** @test */
    public function it_marks_all_activity_logs_of_a_record_as_obsolete_upon_deleting_that_record()
    {
        $this->createPost()->updatePost();

        $this->assertEquals(2, $this->post->activity()->count());
        $this->assertEquals(0, $this->post->activity()->whereObsolete(true)->count());

        $this->deletePost();

        $this->assertEquals(3, $this->post->activity()->count());
        $this->assertEquals(3, $this->post->activity()->whereObsolete(true)->count());
    }

    /** @test */
    public function it_stores_the_options_passed_from_the_options_method_when_creating_an_activity_log()
    {
        $model = new class extends Post {
            public function getActivityOptions() : ActivityOptions
            {
                return ActivityOptions::instance()
                    ->withEntityType('Some Testing Type')
                    ->withEntityName('Some Testing Name')
                    ->withEntityUrl('some-testing-url');
            }
        };

        $this->createPost($model);

        $activity = $this->post->activity()->first();

        $this->assertEquals('Some Testing Type', $activity->entity_type);
        $this->assertEquals('Some Testing Name', $activity->entity_name);
        $this->assertEquals('some-testing-url', $activity->entity_url);
    }

    /** @expectedException Exception */
    public function it_requires_an_entity_type_to_be_specified_in_the_options_method()
    {
        $model = new class extends Post {
            public function getActivityOptions() : ActivityOptions
            {
                return ActivityOptions::instance()
                    ->withEntityName($this->name);
            }
        };

        $this->createPost($model);
    }

    /** @expectedException Exception */
    public function it_requires_an_entity_name_to_be_specified_in_the_options_method()
    {
        $model = new class extends Post {
            public function getActivityOptions() : ActivityOptions
            {
                return ActivityOptions::instance()
                    ->withEntityType('post');
            }
        };

        $this->createPost($model);
    }

    /**
     * @param Post|null $model
     * @return $this
     */
    protected function createPost(Post $model = null)
    {
        $model = $model && $model instanceof Post ?
            $model : (new Post)->doLogActivity();

        $this->post = $model->create([
            'name' => 'Post Name',
            'slug' => 'post-slug',
            'content' => 'Post Content',
        ]);

        return $this;
    }

    /**
     * @param bool $logActivity
     * @return $this
     */
    protected function updatePost($logActivity = true)
    {
        if ($logActivity === false) {
            $this->post->doNotLogActivity();
        }

        $this->post->update([
            'name' => 'Post Name Modified',
            'slug' => 'post-slug-modified',
            'content' => 'Post Content Modified',
        ]);

        return $this;
    }

    /**
     * @param bool $logActivity
     * @return $this
     */
    protected function deletePost($logActivity = true)
    {
        if ($logActivity === false) {
            $this->post->doNotLogActivity();
        }

        try {
            $this->post->delete();
        } catch (Exception $e) {}

        return $this;
    }
}
