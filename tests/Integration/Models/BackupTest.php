<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Backup;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class BackupTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Backup
     */
    protected $backup;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('filesystems.disks.backups', [
            'driver' => 'local',
            'root' => storage_path('backups'),
        ]);
    }

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Backup::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Backup::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Backup::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Backup::class));
    }

    /** @test */
    public function it_can_return_the_size_in_megabytes()
    {
        $this->createBackup();

        $this->assertEquals('1.00', $this->backup->size_in_mb);
        $this->assertEquals(1, (int)$this->backup->size_in_mb);
    }

    /** @test */
    public function it_can_order_records_alphabetically()
    {
        $this->createBackups();

        $this->assertEquals('Test Name A', Backup::alphabetically()->first()->name);
    }

    /** @test */
    public function it_can_determine_if_the_filesystem_disk_driver_is_local()
    {
        $this->createBackup();

        $this->assertTrue($this->backup->isLocallyStored());

        $this->app['config']->set('filesystems.disks.backups.driver', 'not-local');

        $this->assertFalse($this->backup->isLocallyStored());
    }

    /** @test */
    public function it_can_delete_a_single_backup_record()
    {
        $this->createBackup();

        $this->assertEquals(1, Backup::count());

        $this->backup->deleteRecordAndFile();

        $this->assertEquals(0, Backup::count());
    }

    /** @test */
    public function it_can_delete_all_backup_records()
    {
        $this->createBackups();

        $this->assertEquals(3, Backup::count());

        Backup::deleteAll();

        $this->assertEquals(0, Backup::count());
    }

    /** @test */
    public function it_can_delete_only_old_backup_records()
    {
        $this->app['config']->set('varbox.backup.old_threshold', 30);

        $this->createBackups();

        $this->assertEquals(3, Backup::count());

        Backup::deleteOld();

        $this->assertEquals(1, Backup::count());
    }

    /**
     * @return void
     */
    protected function createBackup()
    {
        $this->backup = Backup::create([
            'name' => 'Test Name',
            'disk' => 'backups',
            'path' => '/test/path',
            'size' => '1048576',
            'date' => today(),
        ]);
    }

    /**
     * @return void
     */
    protected function createBackups()
    {
        Backup::create([
            'name' => 'Test Name C',
            'disk' => 'backups',
            'path' => '/test/path',
            'size' => '1048576',
            'date' => today()->subDays(31),
        ]);

        Backup::create([
            'name' => 'Test Name B',
            'disk' => 'backups',
            'path' => '/test/path',
            'size' => '1048576',
            'date' => today()->subDays(31),
        ]);

        Backup::create([
            'name' => 'Test Name A',
            'disk' => 'backups',
            'path' => '/test/path',
            'size' => '1048576',
            'date' => today(),
        ]);
    }
}
