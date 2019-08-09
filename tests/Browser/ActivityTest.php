<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
use Varbox\Models\Activity;
use Varbox\Models\Role;
use Varbox\Models\User;

class ActivityTest extends TestCase
{
    /**
     * @var User
     */
    protected $user1;
    protected $user2;

    /**
     * @var Activity
     */
    protected $activity1;
    protected $activity2;
    protected $activity3;

    /**
     * @var Role
     */
    protected $activityModel;

    /**
     * @var string
     */
    protected $user1Email = 'test-user-1@mail.com';
    protected $user1Password = 'test_user_1_password';

    /**
     * @var string
     */
    protected $user2Email = 'test-user-2@mail.com';
    protected $user2Password = 'test_user_2_password';

    /**
     * @var string
     */
    protected $activity1Type = 'test-activity-1-type';
    protected $activity1Name = 'Test Activity 1 Name';
    protected $activity1Url = 'http://test-activity-1-url@tld';
    protected $activity1Event = 'test-activity-1-event';

    /**
     * @var string
     */
    protected $activity2Type = 'test-activity-2-type';
    protected $activity2Name = 'Test Activity 2 Name';
    protected $activity2Url = 'http://test-activity-2-url@tld';
    protected $activity2Event = 'test-activity-2-event';

    /**
     * @var string
     */
    protected $activityType = 'test-activity-type';
    protected $activityName = 'Test Activity Name';
    protected $activityUrl = 'http://test-activity-url.tld';
    protected $activityEvent = 'test-activity-event';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->assertPathIs('/admin/activity')
                ->assertSee('Activity');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('activity-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->assertPathIs('/admin/activity')
                ->assertSee('Activity');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('activity-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->assertSee('Unauthorized')
                ->assertDontSee('Activity');
        });
    }

    /** @test */
    public function an_admin_can_delete_an_activity_if_it_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createActivity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/activity/', $this->activityModel)
                ->assertSee($this->activityName)
                ->assertSee($this->activityType)
                ->assertSee($this->activityEvent)
                ->clickDeleteRecordButton($this->activityName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/activity/', $this->activityModel)
                ->assertDontSee($this->activityName)
                ->assertDontSee($this->activityType)
                ->assertDontSee($this->activityEvent);
        });
    }

    /** @test */
    public function an_admin_can_delete_an_activity_if_it_has_permission()
    {
        $this->admin->grantPermission('activity-list');
        $this->admin->grantPermission('activity-delete');

        $this->createActivity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/activity/', $this->activityModel)
                ->assertSee($this->activityName)
                ->assertSee($this->activityType)
                ->assertSee($this->activityEvent)
                ->clickDeleteRecordButton($this->activityName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/activity/', $this->activityModel)
                ->assertDontSee($this->activityName)
                ->assertDontSee($this->activityType)
                ->assertDontSee($this->activityEvent);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_an_activity_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('activity-list');
        $this->admin->revokePermission('activity-delete');

        $this->createActivity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->assertSourceMissing('button-delete');
        });

        $this->deleteActivity();
    }

    /** @test */
    public function an_admin_can_delete_old_activity_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->clickButtonWithConfirm('Delete Old Activity')
                ->assertPathIs('/admin/activity')
                ->assertSee('Old activity was successfully deleted')
                ->assertRecordsCount(2);
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_can_delete_old_activity_if_it_has_permission()
    {
        $this->admin->grantPermission('activity-list');
        $this->admin->grantPermission('activity-delete');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->clickButtonWithConfirm('Delete Old Activity')
                ->assertPathIs('/admin/activity')
                ->assertSee('Old activity was successfully deleted')
                ->assertRecordsCount(2);
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_cannot_delete_old_activity_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('activity-list');
        $this->admin->revokePermission('activity-delete');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->assertDontSee('Delete Old Activity');
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_can_delete_all_activity_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->clickButtonWithConfirm('Delete All Activity')
                ->assertPathIs('/admin/activity')
                ->assertSee('All activity was successfully deleted')
                ->assertSee('No records found');
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_can_delete_all_activity_if_it_has_permission()
    {
        $this->admin->grantPermission('activity-list');
        $this->admin->grantPermission('activity-delete');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->clickButtonWithConfirm('Delete All Activity')
                ->assertPathIs('/admin/activity')
                ->assertSee('All activity was successfully deleted')
                ->assertSee('No records found');
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_cannot_delete_all_activity_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('activity-list');
        $this->admin->revokePermission('activity-delete');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->assertDontSee('Delete All Activity');
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_can_filter_activity_by_user()
    {
        $this->admin->grantPermission('activity-list');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->filterRecordsBySelect('#user-input', $this->user1Email)
                ->assertQueryStringHas('user', $this->user1->id)
                ->assertRecordsCount(2)
                ->assertSee($this->activity1Name)
                ->assertSee($this->activity2Name)
                ->visit('/admin/activity')
                ->filterRecordsBySelect('#user-input', $this->user2Email)
                ->assertQueryStringHas('user', $this->user2->id)
                ->assertRecordsCount(1)
                ->assertDontSee($this->activity1Name)
                ->assertSee($this->activity2Name);
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_can_filter_activity_by_entity_type()
    {
        $this->admin->grantPermission('activity-list');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->filterRecordsBySelect('#entity-input', $this->activity1Type)
                ->assertQueryStringHas('entity', $this->activity1Type)
                ->assertRecordsCount(1)
                ->assertSee($this->activity1Name)
                ->assertDontSee($this->activity2Name)
                ->visit('/admin/activity')
                ->filterRecordsBySelect('#entity-input', $this->activity2Type)
                ->assertQueryStringHas('entity', $this->activity2Type)
                ->assertRecordsCount(2)
                ->assertSee($this->activity2Name)
                ->assertDontSee($this->activity1Name)
                ->visit('/admin/activity?entity=dummy-activity-type')
                ->assertSee('No records found');
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_can_filter_activity_by_event()
    {
        $this->admin->grantPermission('activity-list');

        $this->createActivities();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->filterRecordsBySelect('#event-input', $this->activity1Event)
                ->assertQueryStringHas('event', $this->activity1Event)
                ->assertRecordsCount(1)
                ->assertSee($this->activity1Name)
                ->assertDontSee($this->activity2Name)
                ->visit('/admin/activity')
                ->filterRecordsBySelect('#event-input', $this->activity2Event)
                ->assertQueryStringHas('event', $this->activity2Event)
                ->assertRecordsCount(2)
                ->assertSee($this->activity2Name)
                ->assertDontSee($this->activity1Name)
                ->visit('/admin/activity?event=dummy-activity-event')
                ->assertSee('No records found');
        });

        $this->deleteActivities();
    }

    /** @test */
    public function an_admin_can_filter_activity_by_start_date()
    {
        $this->admin->grantPermission('activity-list');

        $this->createActivity();

        $past = Carbon::now()->subDays(100)->format('Y-m-d');
        $future = Carbon::now()->addDays(100)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->visitLastPage('/admin/activity', $this->activityModel)
                ->assertSee($this->activityName)
                ->visit('/admin/activity')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteActivity();
    }

    /** @test */
    public function an_admin_can_filter_activity_by_end_date()
    {
        $this->admin->grantPermission('activity-list');

        $this->createActivity();

        $past = Carbon::now()->subDays(100)->format('Y-m-d');
        $future = Carbon::now()->addDays(100)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/activity')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', urlencode($past))
                ->assertSee('No records found')
                ->visit('/admin/activity')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', urlencode($future))
                ->visitLastPage('/admin/activity', $this->activityModel)
                ->assertSee($this->activityName);
        });

        $this->deleteActivity();
    }

    /** @test */
    public function an_admin_can_clear_activity_filters()
    {
        $this->admin->grantPermission('roles-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles/?user=1&entity=test-entity&event=test-event&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('user')
                ->assertQueryStringHas('entity')
                ->assertQueryStringHas('event')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/roles/')
                ->assertQueryStringMissing('user')
                ->assertQueryStringMissing('entity')
                ->assertQueryStringMissing('event')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /**
     * @return void
     */
    protected function createActivity()
    {
        $this->activityModel = Activity::create([
            'entity_type' => $this->activityType,
            'entity_name' => $this->activityName,
            'entity_url' => $this->activityUrl,
            'event' => $this->activityEvent,
        ]);
    }

    /**
     * @return void
     */
    protected function createActivities()
    {
        $this->user1 = User::create([
            'email' => $this->user1Email,
            'password' => $this->user1Password,
        ]);

        $this->user2 = User::create([
            'email' => $this->user2Email,
            'password' => $this->user2Password,
        ]);

        $this->activity1 = Activity::create([
            'user_id' => $this->user1->id,
            'entity_type' => $this->activity1Type,
            'entity_name' => $this->activity1Name,
            'entity_url' => $this->activity1Url,
            'event' => $this->activity1Event,
            'created_at' => Carbon::now()->subDays(31)->format('Y-m-d'),
            'updated_at' => Carbon::now()->subDays(31)->format('Y-m-d'),
        ]);

        $this->activity2 = Activity::create([
            'user_id' => $this->user2->id,
            'entity_type' => $this->activity2Type,
            'entity_name' => $this->activity2Name,
            'entity_url' => $this->activity2Url,
            'event' => $this->activity2Event,
        ]);

        $this->activity3 = Activity::create([
            'user_id' => $this->user1->id,
            'entity_type' => $this->activity2Type,
            'entity_name' => $this->activity2Name,
            'entity_url' => $this->activity2Url,
            'event' => $this->activity2Event,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteActivity()
    {
        Activity::whereEntityName($this->activityName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteActivities()
    {
        Activity::whereIn('entity_name', [
            $this->activity1Name, $this->activity2Name
        ])->delete();

        User::whereIn('email', [
            $this->user1Email, $this->user2Email
        ])->delete();
    }
}
