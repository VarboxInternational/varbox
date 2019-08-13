<?php

namespace Varbox\Tests\Browser;

use Illuminate\Contracts\Foundation\Application;
use Varbox\Models\Block;
use Varbox\Models\Email;

class BlocksTest extends TestCase
{
    /**
     * @var Email
     */
    protected $blockModel;

    /**
     * @var string
     */
    protected $blockName = 'Test Name';
    protected $blockType = 'TestType';

    /**
     * @var string
     */
    protected $blockNameModified = 'Test Name Modified';

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.blocks.types', [
            $this->blockType => [
                'label' => 'Test Block',
                'composer_class' => "App\Blocks\{$this->blockType}\Composer",
                'views_path' => "app/Blocks/{$this->blockType}/Views",
                'preview_image' => 'vendor/varbox/images/blocks/example.jpg',
            ]
        ]);
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->assertPathIs('/admin/blocks')
                ->assertSee('Blocks');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->assertPathIs('/admin/blocks')
                ->assertSee('Blocks');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('blocks-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->assertSee('Unauthorized')
                ->assertDontSee('Blocks');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickLink('Add New')
                ->assertPathIs('/admin/blocks/create')
                ->assertSee('Add Block');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickLink('Add New')
                ->assertPathIs('/admin/blocks/create')
                ->assertSee('Add Block');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->revokePermission('blocks-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->assertDontSee('Add New')
                ->visit('/admin/blocks/create')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Block');
        });
    }

    /**
     * @return void
     */
    protected function createBlock()
    {
        $this->blockModel = Block::create([
            'name' => $this->blockName,
            'type' => $this->blockType,
        ]);
    }

    /**
     * @return void
     */
    protected function updateBlock()
    {
        $this->blockModel->fresh()->update([
            'name' => $this->blockNameModified
        ]);
    }

    /**
     * @return void
     */
    protected function createBlockModified()
    {
        $this->blockModel = Block::create([
            'name' => $this->blockNameModified,
            'type' => $this->blockType,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteBlock()
    {
        Block::withTrashed()->withDrafts()
            ->whereName($this->blockName)
            ->first()->forceDelete();
    }

    /**
     * @return void
     */
    protected function deleteBlockModified()
    {
        Block::withTrashed()->withDrafts()
            ->whereName($this->blockNameModified)
            ->first()->forceDelete();
    }

    /**
     * @return void
     */
    protected function deleteDuplicatedBlock()
    {
        Block::withTrashed()->withDrafts()
            ->whereName($this->blockName . ' (1)')
            ->first()->forceDelete();
    }
}
