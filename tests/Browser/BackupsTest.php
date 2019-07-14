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
     * @return void
     */
    public function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('backup.notifications.notifiable', Notifiable::class);
        $app['config']->set('varbox.backup.name', 'VarBox');
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
                ->assertSee('Unauthorized')
                ->assertDontSee('Backups');
        });
    }

    /** @test */
    public function an_admin_can_create_a_new_backup_if_it_a_super_admin()
    {
        $this->admin->assignRoles('Super');

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
                ->clickButtonWithConfirm('Create New Backup')
                ->assertDontSee('The backup was successfully created')
                ->assertSee('Unauthorized')
                ->visit('/admin/backups')
                ->assertSee('No records found');
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
                ->deleteRecord($this->backupModel->name)
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
                ->deleteRecord($this->backupModel->name)
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
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });

        $this->cleanBackups();
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
