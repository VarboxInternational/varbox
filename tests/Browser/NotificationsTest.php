<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Varbox\Models\User;

class NotificationsTest extends TestCase
{
    /**
     * @var User
     */
    protected $userModel;

    /**
     * @var User
     */
    protected $anotherUserModel;

    /**
     * @var DatabaseNotification
     */
    protected $notification1Model;

    /**
     * @var DatabaseNotification
     */
    protected $notification2Model;

    /**
     * @var string
     */
    protected $notification1Type = 'test-notification-1-type';
    protected $notification1Subject = 'Test Notification 1 Subject';
    protected $notification1Url = 'http://test-notification-1-url.tld';

    /**
     * @var string
     */
    protected $notification2Type = 'test-notification-2-type';
    protected $notification2Subject = 'Test Notification 2 Subject';
    protected $notification2Url = 'http://test-notification-2-url.tld';

    /**
     * @var string
     */
    protected $userName = 'Test User';
    protected $userEmail = 'test-user@mail.com';
    protected $userPassword = 'test_user_password';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertPathIs('/admin/notifications')
                ->assertSee('Notifications');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('notifications-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertPathIs('/admin/notifications')
                ->assertSee('Notifications');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('notifications-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertSee('401')
                ->assertDontSee('Notifications');
        });
    }

    /** @test */
    public function an_admin_can_delete_a_notification_if_it_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createNotification();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/notifications/', $this->notification1Model)
                ->assertSee($this->notification1Subject)
                ->clickDeleteRecordButton($this->notification1Subject)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/notifications/', $this->notification1Model)
                ->assertDontSee($this->notification1Subject);
        });
    }

    /** @test */
    public function an_admin_can_delete_a_notification_if_it_has_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->grantPermission('notifications-delete');

        $this->createNotification();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/notifications/', $this->notification1Model)
                ->assertSee($this->notification1Subject)
                ->clickDeleteRecordButton($this->notification1Subject)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/notifications/', $this->notification1Model)
                ->assertDontSee($this->notification1Subject);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_notification_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->revokePermission('notifications-delete');

        $this->createNotification();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertSourceMissing('button-delete');
        });

        $this->deleteNotification();
    }

    /** @test */
    public function an_admin_can_mark_a_notification_as_read_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createNotification();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/notifications', $this->notification1Model)
                ->assertSeeIn('.badge', 'No')
                ->clickButton($this->notification1Subject, 'button-mark-as-read')
                ->assertSee('The notification has been successfully marked as read')
                ->visitLastPage('/admin/notifications', $this->notification1Model)
                ->assertSeeIn('.badge', 'Yes');
        });

        $this->deleteNotification();
    }

    /** @test */
    public function an_admin_can_mark_a_notification_as_read_if_it_has_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->grantPermission('notifications-read');

        $this->createNotification();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/notifications', $this->notification1Model)
                ->assertSeeIn('.badge', 'No')
                ->clickButton($this->notification1Subject, 'button-mark-as-read')
                ->assertSee('The notification has been successfully marked as read')
                ->visitLastPage('/admin/notifications', $this->notification1Model)
                ->assertSeeIn('.badge', 'Yes');
        });

        $this->deleteNotification();
    }

    /** @test */
    public function an_admin_cannot_mark_a_notification_as_read_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->revokePermission('notifications-read');

        $this->createNotification();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/notifications', $this->notification1Model)
                ->assertSourceMissing('button-mark-as-read');
        });

        $this->deleteNotification();
    }

    /** @test */
    public function an_admin_can_mark_all_notifications_as_read_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertPresent('.badge-success')
                ->assertSeeIn('.badge-success', 'Yes')
                ->assertPresent('.badge-danger')
                ->assertSeeIn('.badge-danger', 'No')
                ->clickButtonWithConfirm('Mark All As Read')
                ->assertPathIs('/admin/notifications')
                ->assertSee('All unread notifications have been successfully marked as read')
                ->assertPresent('.badge-success')
                ->assertSeeIn('.badge-success', 'Yes')
                ->assertMissing('.badge-danger')
                ->assertDontSeeIn('.badge', 'No');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_mark_all_notifications_as_read_if_it_has_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->grantPermission('notifications-read');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertPresent('.badge-success')
                ->assertSeeIn('.badge-success', 'Yes')
                ->assertPresent('.badge-danger')
                ->assertSeeIn('.badge-danger', 'No')
                ->clickButtonWithConfirm('Mark All As Read')
                ->assertPathIs('/admin/notifications')
                ->assertSee('All unread notifications have been successfully marked as read')
                ->assertPresent('.badge-success')
                ->assertSeeIn('.badge-success', 'Yes')
                ->assertMissing('.badge-danger')
                ->assertDontSeeIn('.badge', 'No');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_cannot_mark_all_notifications_as_read_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->revokePermission('notifications-read');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertDontSee('Mark All As Read');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_delete_all_read_notifications_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertRecordsCount(2)
                ->assertPresent('.badge-success')
                ->assertSeeIn('.badge-success', 'Yes')
                ->clickButtonWithConfirm('Delete Read Notifications')
                ->assertPathIs('/admin/notifications')
                ->assertSee('All read notifications have been successfully deleted')
                ->assertRecordsCount(1)
                ->assertMissing('.badge-success');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_delete_all_read_notifications_if_it_has_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->grantPermission('notifications-delete');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertRecordsCount(2)
                ->assertPresent('.badge-success')
                ->assertSeeIn('.badge-success', 'Yes')
                ->clickButtonWithConfirm('Delete Read Notifications')
                ->assertPathIs('/admin/notifications')
                ->assertSee('All read notifications have been successfully deleted')
                ->assertRecordsCount(1)
                ->assertMissing('.badge-success');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_cannot_delete_all_read_notifications_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->revokePermission('notifications-delete');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertDontSee('Delete Read Notifications');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_delete_old_notifications_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertRecordsCount(2)
                ->clickButtonWithConfirm('Delete Old Notifications')
                ->assertPathIs('/admin/notifications')
                ->assertSee('Old notifications were successfully deleted')
                ->assertRecordsCount(1);
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_delete_old_notifications_if_it_has_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->grantPermission('notifications-delete');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertRecordsCount(2)
                ->clickButtonWithConfirm('Delete Old Notifications')
                ->assertPathIs('/admin/notifications')
                ->assertSee('Old notifications were successfully deleted')
                ->assertRecordsCount(1);
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_cannot_delete_old_notifications_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->revokePermission('notifications-delete');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertDontSee('Delete Old Notifications');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_delete_all_notifications_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertRecordsCount(2)
                ->clickButtonWithConfirm('Delete All Notifications')
                ->assertPathIs('/admin/notifications')
                ->assertSee('All notifications were successfully deleted')
                ->assertSee('No records found');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_delete_all_notifications_if_it_has_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->grantPermission('notifications-delete');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertRecordsCount(2)
                ->clickButtonWithConfirm('Delete All Notifications')
                ->assertPathIs('/admin/notifications')
                ->assertSee('All notifications were successfully deleted')
                ->assertSee('No records found');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_cannot_delete_all_notifications_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('notifications-list');
        $this->admin->revokePermission('notifications-delete');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertDontSee('Delete All Notifications');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_filter_notifications_by_user()
    {
        $this->admin->grantPermission('notifications-list');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->filterRecordsBySelect('#user-input', $this->userModel->email)
                ->assertQueryStringHas('user', $this->userModel->id)
                ->assertRecordsCount(2)
                ->visit('/admin/notifications')
                ->filterRecordsBySelect('#user-input', $this->anotherUserModel->email)
                ->assertQueryStringHas('user', $this->anotherUserModel->id)
                ->assertSee('No records found');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_filter_notifications_by_read()
    {
        $this->admin->grantPermission('notifications-list');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->assertRecordsCount(2)
                ->filterRecordsBySelect('#read-input', 'Read')
                ->assertQueryStringHas('read', '1')
                ->assertRecordsCount(1)
                ->visit('/admin/notifications')
                ->assertRecordsCount(2)
                ->filterRecordsBySelect('#read-input', 'Unread')
                ->assertQueryStringHas('read', '2')
                ->assertRecordsCount(1);
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function an_admin_can_filter_notifications_by_start_date()
    {
        $this->admin->grantPermission('notifications-list');

        $this->createNotification();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->visitLastPage('/admin/notifications', $this->notification1Model)
                ->assertSee($this->notification1Subject)
                ->visit('/admin/notifications')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteNotification();
    }

    /** @test */
    public function an_admin_can_filter_notifications_by_end_date()
    {
        $this->admin->grantPermission('notifications-list');

        $this->createNotification();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', urlencode($past))
                ->assertSee('No records found')
                ->visit('/admin/notifications')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', urlencode($future))
                ->visitLastPage('/admin/notifications', $this->notification1Model)
                ->assertSee($this->notification1Subject);
        });

        $this->deleteNotification();
    }

    /** @test */
    public function an_admin_can_view_another_user_notifications()
    {
        $this->admin->grantPermission('notifications-list');

        $this->createNotifications();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/notifications')
                ->filterRecordsBySelect('#user-input', $this->anotherUserModel->email)
                ->assertSee('You are now viewing another user\'s notifications')
                ->assertMissing('Mark All As Read')
                ->assertMissing('Delete Read Notifications')
                ->assertMissing('Delete Old Notifications')
                ->assertMissing('Delete All Notifications')
                ->assertMissing('.button-action')
                ->assertMissing('.button-mark-as-read')
                ->assertMissing('.button-delete');
        });

        $this->deleteNotifications();
    }

    /** @test */
    public function it_displays_the_notifications_icon_in_header_on_every_page()
    {
        $this->admin->assignRoles('Super');

        $this->createNotification();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin')
                ->assertPresent('.notifications-dropdown')
                ->click('.notifications-dropdown')
                ->waitForText($this->notification1Subject)
                ->assertSee($this->notification1Subject);
        });

        $this->deleteNotification();
    }

    /** @test */
    public function it_displays_a_read_dot_in_the_notifications_icon_when_the_admin_has_unread_notifications()
    {
        $this->admin->assignRoles('Super');

        $this->createNotification();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin')
                ->assertPresent('.nav-unread');
        });

        $this->deleteNotification();
    }

    /** @test */
    public function it_doesnt_display_a_read_dot_in_the_notifications_icon_when_the_admin_doesnt_have_unread_notifications()
    {
        $this->admin->assignRoles('Super');

        $this->createNotification([
            'read_at' => Carbon::now()
        ]);

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin')
                ->assertMissing('.nav-unread');
        });

        $this->deleteNotification();
    }

    /**
     * @param array $attributes
     * @return void
     */
    protected function createNotification(array $attributes = [])
    {
        $this->userModel = User::first();

        $this->notification1Model = $this->userModel->notifications()->create(array_merge($attributes, [
            'id' => Str::uuid()->toString(),
            'type' => $this->notification1Type,
            'data' => [
                'subject' => $this->notification1Subject,
                'url' => $this->notification1Url,
            ],
        ]));
    }

    /**
     * @return void
     */
    protected function createNotifications()
    {
        $this->userModel = User::first();
        $this->anotherUserModel = User::create([
            'name' => $this->userName,
            'email' => $this->userEmail,
            'password' => $this->userPassword,
        ]);

        $this->notification1Model = $this->userModel->notifications()->create([
            'id' => Str::uuid()->toString(),
            'type' => $this->notification1Type,
            'data' => [
                'subject' => $this->notification1Subject,
                'url' => $this->notification1Url,
            ],
        ]);

        $this->notification2Model = $this->userModel->notifications()->create([
            'id' => Str::uuid()->toString(),
            'type' => $this->notification2Type,
            'data' => [
                'subject' => $this->notification2Subject,
                'url' => $this->notification2Url,
            ],
            'read_at' => Carbon::now(),
            'created_at' => Carbon::now()->subDays(31),
        ]);
    }

    /**
     * @return void
     */
    protected function deleteNotification()
    {
        DatabaseNotification::where('type', $this->notification1Type)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteNotifications()
    {
        User::whereEmail($this->userEmail)->first()->delete();

        DatabaseNotification::whereIn('type', [
            $this->notification1Type, $this->notification2Type
        ])->delete();
    }
}
