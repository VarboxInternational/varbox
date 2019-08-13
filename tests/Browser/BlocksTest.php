<?php

namespace Varbox\Tests\Browser;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\File;
use Mockery;
use Varbox\Commands\BlockMakeCommand;
use Varbox\Models\Block;

class BlocksTest extends TestCase
{
    /**
     * @var Block
     */
    protected $blockModel;

    /**
     * @var string
     */
    protected $blockType = 'Example';
    protected $blockName = 'Test Name';

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

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/blocks', $this->blockModel)
                ->clickEditRecordButton($this->blockName)
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertSee('Edit Block');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/blocks', $this->blockModel)
                ->clickEditRecordButton($this->blockName)
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertSee('Edit Block');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->revokePermission('blocks-edit');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/blocks', $this->blockModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Block');
        });

        $this->deleteBlock();
    }

    /**
     * @return void
     */
    protected function createBlock()
    {
        $this->createBlockFiles();

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
        $this->createBlockFiles();

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

        $this->deleteBlockFiles();
    }

    /**
     * @return void
     */
    protected function deleteBlockModified()
    {
        Block::withTrashed()->withDrafts()
            ->whereName($this->blockNameModified)
            ->first()->forceDelete();

        $this->deleteBlockFiles();
    }

    /**
     * @return void
     */
    protected function deleteDuplicatedBlock()
    {
        Block::withTrashed()->withDrafts()
            ->whereName($this->blockName . ' (1)')
            ->first()->forceDelete();

        $this->deleteBlockFiles();
    }

    /**
     * @return void
     */
    protected function createBlockFiles()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType])
            ->expectsQuestion($this->blockLocationsQuestion(), 'header content footer')
            ->expectsQuestion($this->blockDummyFieldsQuestion(), 'yes')
            ->expectsQuestion($this->blockMultipleItemsQuestion(), 'yes');
    }

    /**
     * @return void
     */
    protected function deleteBlockFiles()
    {
        File::deleteDirectory(app_path('Blocks'));
    }

    /**
     * @return string
     */
    protected function blockLocationsQuestion()
    {
        $question = [];
        $question[] = 'What are the locations this block should be available in?';
        $question[] = ' <fg=white>Please delimit the locations by using a space <fg=yellow>" "</> between them.</>';
        $question[] = ' <fg=white>If you don\'t want any locations, just hit <fg=yellow>ENTER</></>';

        return implode(PHP_EOL, $question);
    }

    /**
     * @return string
     */
    protected function blockDummyFieldsQuestion()
    {
        $question = [];
        $question[] = 'Do you want to generate dummy fields for the admin view?';
        $question[] = ' <fg=white>If you choose <fg=yellow>yes</>, the script will generate one example input field for each type available in the platform</>';

        return implode(PHP_EOL, $question);
    }

    /**
     * @return string
     */
    protected function blockMultipleItemsQuestion()
    {
        $question = [];
        $question[] = 'Do you want support for multiple items inside the admin view?';
        $question[] = ' <fg=white>If you choose <fg=yellow>yes</>, the script will generate the code needed for adding multiple items (like a list) to the block</>';

        return implode(PHP_EOL, $question);
    }
}
