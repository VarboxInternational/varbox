<?php

namespace Varbox\Tests\Models\Integration;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Activity;
use Varbox\Models\User;
use Varbox\Tests\Integration\TestCase;
use Varbox\Tests\Models\Post;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class ActivityTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Post
     */
    protected $post;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpTestingConditions();
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Activity::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Activity::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Activity::class));
    }


    /** @test */
    public function it_can_belong_to_a_user()
    {
        $this->assertNull($this->activity->user()->first());

        $this->activity->user()->associate($this->user);

        $this->assertTrue($this->activity->user() instanceof BelongsTo);
        $this->assertEquals($this->user->email, $this->activity->user()->first()->email);
    }

    /** @test */
    public function it_morphs_to_a_subject()
    {
        $this->assertTrue($this->activity->subject() instanceof MorphTo);
        $this->assertTrue($this->activity->subject()->first() instanceof Post);
        $this->assertEquals($this->post->name, $this->activity->subject()->first()->name);
    }

    /** @test */
    public function it_can_return_a_pretty_formatted_message_for_an_activity_log()
    {
        $this->assertEquals(
            '<strong>A user</strong> ' . $this->activity->event . ' a ' . $this->activity->entity_type . ' (<a href="' . url($this->activity->entity_url) . '" target="_blank">' . $this->activity->entity_name . '</a>)',
            $this->activity->message
        );
    }

    /** @test */
    public function it_can_return_a_pretty_formatted_message_for_an_activity_log_containing_the_user()
    {
        $this->activity->user()->associate($this->user);

        $this->assertEquals(
            '<strong>' . $this->user->email . '</strong> ' . $this->activity->event . ' a ' . $this->activity->entity_type . ' (<a href="' . url($this->activity->entity_url) . '" target="_blank">' . $this->activity->entity_name . '</a>)',
            $this->activity->message
        );
    }

    /** @test */
    public function it_can_return_a_pretty_formatted_message_for_an_activity_log_without_url_if_not_supplied()
    {
        $this->activity->update(['entity_url' => null]);

        $this->assertEquals(
            '<strong>A user</strong> ' . $this->activity->event . ' a ' . $this->activity->entity_type . ' (<span style="color: #868e96;">' . $this->activity->entity_name . '</span>)',
            $this->activity->message
        );
    }

    /** @test */
    public function it_can_return_a_pretty_formatted_message_for_an_activity_log_without_url_if_activity_is_obsolete()
    {
        $this->activity->update(['obsolete' => true]);

        $this->assertEquals(
            '<strong>A user</strong> ' . $this->activity->event . ' a ' . $this->activity->entity_type . ' (<span style="color: #868e96;">' . $this->activity->entity_name . '</span>)',
            $this->activity->message
        );
    }

    /** @test */
    public function it_can_filter_activity_logs_by_causer()
    {
        $this->assertEquals(0, Activity::causedBy($this->user)->count());

        $this->activity->user_id = $this->user->id;
        $this->activity->save();

        $this->assertEquals(1, Activity::causedBy($this->user)->count());
    }

    /** @test */
    public function it_can_filter_activity_logs_by_subject()
    {
        $this->assertEquals(0, Activity::forSubject($this->user)->count());
        $this->assertEquals(1, Activity::forSubject($this->post)->count());
    }

    /** @test */
    public function it_can_delete_old_activity_logs()
    {
        $this->app['config']->set('varbox.activity.old_threshold', 30);

        for ($i = 1; $i <= 3; $i++) {
            Activity::create([
                'event' => 'created',
                'created_at' => today()->subDays(31),
            ]);
        }

        $this->assertEquals(4, Activity::count());

        Activity::deleteOld();

        $this->assertEquals(1, Activity::count());
    }

    /** @test */
    public function it_can_return_all_distinct_activity_events()
    {
        Activity::truncate();

        for ($i = 1; $i <= 3; $i++) {
            Activity::create([
                'event' => 'created',
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            Activity::create([
                'event' => 'updated',
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            Activity::create([
                'event' => 'deleted',
            ]);
        }

        $this->assertEquals(9, Activity::count());

        $events = Activity::getDistinctEvents();

        $this->assertCount(3, $events);
        $this->assertArrayHasKey('created', $events);
        $this->assertEquals('created', $events['created']);
        $this->assertArrayHasKey('updated', $events);
        $this->assertEquals('updated', $events['updated']);
        $this->assertArrayHasKey('deleted', $events);
        $this->assertEquals('deleted', $events['deleted']);
    }

    /** @test */
    public function it_can_return_all_distinct_activity_entities()
    {
        Activity::truncate();

        for ($i = 1; $i <= 3; $i++) {
            Activity::create([
                'entity_type' => 'entity-1',
                'event' => 'created',
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            Activity::create([
                'entity_type' => 'entity-2',
                'event' => 'updated',
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            Activity::create([
                'entity_type' => 'entity-3',
                'event' => 'deleted',
            ]);
        }

        $this->assertEquals(9, Activity::count());

        $entities = Activity::getDistinctEntities();

        $this->assertCount(3, $entities);
        $this->assertArrayHasKey('entity-1', $entities);
        $this->assertEquals('entity-1', $entities['entity-1']);
        $this->assertArrayHasKey('entity-2', $entities);
        $this->assertEquals('entity-2', $entities['entity-2']);
        $this->assertArrayHasKey('entity-3', $entities);
        $this->assertEquals('entity-3', $entities['entity-3']);
    }

    /**
     * @return void
     */
    protected function setUpTestingConditions()
    {
        $this->user = (new User)->doNotLogActivity()->create([
            'email' => 'test-user@mail.com',
            'password' => 'test_password',
        ]);

        $this->post = Post::create([
            'name' => 'Post Name',
            'slug' => 'post-slug',
            'content' => 'Post Content',
        ]);

        $this->activity = Activity::first();
    }
}
