<?php

namespace Varbox\Tests\Browser;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Notifications\Notifiable;
use Varbox\Models\Backup;

class BackupsTest extends TestCase
{
    /**
     * @var Backup
     */
    protected $backupModel;

    /**
     * Setup the test environment.
     *
     * @param $app
     * @return void
     */
    public function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.backup.name', 'VarBox');
        $app['config']->set('varbox.backup.source.databases', ['sqlite']);
        $app['config']->set('backup.notifications.notifiable', Notifiable::class);
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('Backups');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('backups-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('Backups');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('backups-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertSee('401')
                ->assertDontSee('Backups');
        });
    }

    /** @test */
    public function an_admin_can_create_a_new_backup_if_it_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->clickButtonWithConfirm('Create New Backup')
                ->assertPathIs('/admin/backups')
                ->assertSee('The backup was successfully created')
                ->assertDontSee('No records found')
                ->assertRecordsCount(1);
        });

        $this->backupModel = Backup::first();

        $this->assertFileExists($this->backupPath());

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_create_a_new_backup_if_it_has_permission()
    {
        $this->admin->grantPermission('backups-list');
        $this->admin->grantPermission('backups-create');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups/')
                ->clickButtonWithConfirm('Create New Backup')
                ->assertSee('The backup was successfully created')
                ->assertRecordsCount(1);
        });

        $this->backupModel = Backup::first();

        $this->assertFileExists($this->backupPath());

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_cannot_create_a_new_backup_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('backups-list');
        $this->admin->revokePermission('backups-create');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups/')
                ->assertDontSee('Create New Backup');
        });

        $this->assertEmpty($this->backupFiles());
    }

    /** @test */
    public function an_admin_can_delete_a_backup_if_it_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/backups/', $this->backupModel)
                ->assertSee($this->backupModel->name)
                ->assertSee($this->backupModel->size_in_mb)
                ->assertSee($this->backupModel->date->toDateTimeString())
                ->clickDeleteRecordButton($this->backupModel->name)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/backups/', $this->backupModel)
                ->assertDontSee($this->backupModel->name)
                ->assertDontSee($this->backupModel->size_in_mb)
                ->assertDontSee($this->backupModel->date->toDateTimeString());
        });

        $this->assertFileNotExists($this->backupPath());
    }

    /** @test */
    public function an_admin_can_delete_a_backup_if_it_has_permission()
    {
        $this->admin->grantPermission('backups-list');
        $this->admin->grantPermission('backups-delete');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/backups/', $this->backupModel)
                ->assertSee($this->backupModel->name)
                ->assertSee($this->backupModel->size_in_mb)
                ->assertSee($this->backupModel->date->toDateTimeString())
                ->clickDeleteRecordButton($this->backupModel->name)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/backups/', $this->backupModel)
                ->assertDontSee($this->backupModel->name)
                ->assertDontSee($this->backupModel->size_in_mb)
                ->assertDontSee($this->backupModel->date->toDateTimeString());
        });

        $this->assertFileNotExists($this->backupPath());
    }

    /** @test */
    public function an_admin_cannot_delete_a_backup_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('backups-list');
        $this->admin->revokePermission('backups-delete');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertSourceMissing('button-delete');
        });

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_delete_old_backups_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->clickButtonWithConfirm('Delete Old Backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('Old backups were successfully deleted')
                ->assertRecordsCount(1)
                ->assertSee($this->backupModel->name)
                ->assertSee($this->backupModel->size_in_mb)
                ->assertSee($this->backupModel->date->toDateTimeString());

        });

        $this->assertCount(1, $this->backupFiles());

        $this->cleanBackups();
        $this->createBackup();

        $this->backupModel->date = today()->subDays(31);
        $this->backupModel->save();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->clickButtonWithConfirm('Delete Old Backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('Old backups were successfully deleted')
                ->assertSee('No records found')
                ->assertDontSee($this->backupModel->name)
                ->assertDontSee($this->backupModel->size_in_mb)
                ->assertDontSee($this->backupModel->date->toDateTimeString());
        });

        $this->assertCount(0, $this->backupFiles());
    }

    /** @test */
    public function an_admin_can_delete_old_backups_if_it_has_permission()
    {
        $this->admin->grantPermission('backups-list');
        $this->admin->grantPermission('backups-delete');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->clickButtonWithConfirm('Delete Old Backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('Old backups were successfully deleted')
                ->assertRecordsCount(1)
                ->assertSee($this->backupModel->name)
                ->assertSee($this->backupModel->size_in_mb)
                ->assertSee($this->backupModel->date->toDateTimeString());

        });

        $this->assertCount(1, $this->backupFiles());

        $this->cleanBackups();
        $this->createBackup();

        $this->backupModel->date = today()->subDays(31);
        $this->backupModel->save();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->clickButtonWithConfirm('Delete Old Backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('Old backups were successfully deleted')
                ->assertSee('No records found')
                ->assertDontSee($this->backupModel->name)
                ->assertDontSee($this->backupModel->size_in_mb)
                ->assertDontSee($this->backupModel->date->toDateTimeString());
        });

        $this->assertCount(0, $this->backupFiles());
    }

    /** @test */
    public function an_admin_cannot_delete_old_backups_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('backups-list');
        $this->admin->revokePermission('backups-delete');

        $this->createBackup();

        $this->backupModel->date = today()->subDays(31);
        $this->backupModel->save();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertDontSee('Delete Old Backups');
        });

        $this->assertCount(1, $this->backupFiles());

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_delete_all_backups_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBackup();

        $this->backupModel->date = today()->subDays(31);
        $this->backupModel->save();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->clickButtonWithConfirm('Delete All Backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('All backups were successfully deleted')
                ->assertSee('No records found')
                ->assertDontSee($this->backupModel->name)
                ->assertDontSee($this->backupModel->size_in_mb)
                ->assertDontSee($this->backupModel->date->toDateTimeString());
        });

        $this->assertCount(0, $this->backupFiles());
    }

    /** @test */
    public function an_admin_can_delete_all_backups_if_it_has_permission()
    {
        $this->admin->grantPermission('backups-list');
        $this->admin->grantPermission('backups-delete');

        $this->createBackup();

        $this->backupModel->date = today()->subDays(31);
        $this->backupModel->save();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->clickButtonWithConfirm('Delete All Backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('All backups were successfully deleted')
                ->assertSee('No records found')
                ->assertDontSee($this->backupModel->name)
                ->assertDontSee($this->backupModel->size_in_mb)
                ->assertDontSee($this->backupModel->date->toDateTimeString());
        });

        $this->assertCount(0, $this->backupFiles());
    }

    /** @test */
    public function an_admin_cannot_delete_all_backups_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('backups-list');
        $this->admin->revokePermission('backups-delete');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertDontSee('Delete All Backups');
        });

        $this->assertCount(1, $this->backupFiles());

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_filter_backups_by_keyword()
    {
        $this->admin->grantPermission('backups-list');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->filterRecordsByText('#search-input', $this->backupModel->name)
                ->assertQueryStringHas('search', $this->backupModel->name)
                ->assertSee($this->backupModel->name)
                ->assertRecordsCount(1)
                ->visit('/admin/backups')
                ->filterRecordsByText('#search-input', 'something-returning-no-results')
                ->assertQueryStringHas('search', 'something-returning-no-results')
                ->assertDontSee($this->backupModel->name)
                ->assertSee('No records found');
        });

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_filter_backups_by_min_size()
    {
        $this->admin->grantPermission('backups-list');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->filterRecordsByText('size[0]', 0)
                ->visitLastPage('/admin/backups', $this->backupModel)
                ->assertSee($this->backupModel->name)
                ->assertSee($this->backupModel->size_in_mb)
                ->visit('/admin/backups')
                ->filterRecordsByText('size[0]', 1000)
                ->assertSee('No records found');
        });

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_filter_backups_by_max_size()
    {
        $this->admin->grantPermission('backups-list');

        $this->createBackup();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->filterRecordsByText('size[1]', 1000)
                ->visitLastPage('/admin/backups', $this->backupModel)
                ->assertSee($this->backupModel->name)
                ->assertSee($this->backupModel->size_in_mb)
                ->visit('/admin/backups')
                ->filterRecordsByText('size[1]', 0)
                ->assertSee('No records found');
        });

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_filter_backups_by_start_date()
    {
        $this->admin->grantPermission('backups-list');

        $this->createBackup();

        $past = today()->subDays(100)->format('Y-m-d');
        $future = today()->addDays(100)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->visitLastPage('/admin/backups', $this->backupModel)
                ->assertSee($this->backupModel->name)
                ->assertSee($this->backupModel->date->toDateTimeString())
                ->visit('/admin/backups')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_filter_backups_by_end_date()
    {
        $this->admin->grantPermission('backups-list');

        $this->createBackup();

        $past = today()->subDays(100)->format('Y-m-d');
        $future = today()->addDays(100)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/backups')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date',$future)
                ->visitLastPage('/admin/backups', $this->backupModel)
                ->assertSee($this->backupModel->name)
                ->assertSee($this->backupModel->date->toDateTimeString());
        });

        $this->cleanBackups();
    }

    /** @test */
    public function an_admin_can_clear_backup_filters()
    {
        $this->admin->grantPermission('backups-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups/?search=something&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/backups/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /**
     * @return void
     */
    protected function createBackup()
    {
        Artisan::call('backup:run');

        $this->backupModel = Backup::first();
    }

    /**
     * @return void
     */
    protected function cleanBackups()
    {
        Storage::disk($this->backupModel->disk)->delete(
            Storage::disk($this->backupModel->disk)->allFiles(config('varbox.backup.name'))
        );

        Backup::truncate();
    }

    /**
     * @return string
     */
    protected function backupPath()
    {
        return Storage::disk($this->backupModel->disk)->getDriver()->getAdapter()->getPathPrefix() .
            $this->backupModel->path;
    }

    /**
     * @return array
     */
    protected function backupFiles()
    {
        return Storage::disk('backups')->allFiles(config('varbox.backup.name'));
    }

}
