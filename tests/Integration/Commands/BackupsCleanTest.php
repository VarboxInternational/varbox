<?php

namespace Varbox\Tests\Integration\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Backup;
use Varbox\Tests\Integration\TestCase;

class BackupsCleanTest extends TestCase
{
    use DatabaseTransactions;

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

        $this->setUpTestingConditions();
    }

    /** @test */
    public function it_can_delete_old_backups()
    {
        $this->app['config']->set('varbox.backup.old_threshold', 30);

        $this->assertEquals(4, Backup::count());

        $this->artisan('varbox:clean-backups');

        $this->assertEquals(1, Backup::count());
    }

    /** @test */
    public function it_doesnt_delete_any_backups_if_the_days_threshold_is_null()
    {
        $this->app['config']->set('varbox.backup.old_threshold', null);

        $this->assertEquals(4, Backup::count());

        $this->artisan('varbox:clean-backups');

        $this->assertEquals(4, Backup::count());
    }

    /** @test */
    public function it_doesnt_delete_any_backups_if_the_days_threshold_is_zero()
    {
        $this->app['config']->set('varbox.backup.old_threshold', 0);

        $this->assertEquals(4, Backup::count());

        $this->artisan('varbox:clean-backups');

        $this->assertEquals(4, Backup::count());
    }

    /**
     * @return void
     */
    protected function setUpTestingConditions()
    {
        Backup::create([
            'name' => 'Test Name',
            'disk' => 'backups',
            'path' => '/test/path',
            'date' => today(),
        ]);

        for ($i = 1; $i <= 3; $i++) {
            Backup::create([
                'name' => 'Test Name ' . $i,
                'disk' => 'backups',
                'path' => '/test/path/' . $i,
                'date' => today()->subDays(31),
            ]);
        }
    }
}
