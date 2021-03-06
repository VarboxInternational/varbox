<?php

namespace Varbox\Tests\Browser;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\File;
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
    protected $blockLabel = 'Example Block';
    protected $blockName = 'Test Name';

    /**
     * @var string
     */
    protected $blockNameModified = 'Test Name Modified';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(function () {
            $this->artisan('varbox:make-block', ['type' => $this->blockType])
                ->expectsQuestion($this->blockLocationsQuestion(), 'header content footer')
                ->expectsQuestion($this->blockDummyFieldsQuestion(), 'yes')
                ->expectsQuestion($this->blockMultipleItemsQuestion(), 'yes');
        });

        $this->beforeApplicationDestroyed(function () {
            File::deleteDirectory(app_path('Blocks'));
        });
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.blocks.types', [
            $this->blockType => [
                'label' => $this->blockLabel,
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
                ->assertSee('401')
                ->assertDontSee('Blocks');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_is_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_export_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->revokePermission('blocks-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->assertSourceMissing('button-export');
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
                ->assertSee('401')
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
                ->assertSee('401')
                ->assertDontSee('Edit Block');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_create_a_block()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickLink('Add New')
                ->typeSelect2('#type-input', $this->blockLabel)
                ->clickLink('Continue')
                ->assertPathIs('/admin/blocks/create/' . $this->blockType)
                ->type('#name-input', $this->blockName)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/blocks')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/blocks/', new Block)
                ->assertSee($this->blockName)
                ->assertSee($this->blockType);
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_create_a_block_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickLink('Add New')
                ->typeSelect2('#type-input', $this->blockLabel)
                ->clickLink('Continue')
                ->assertPathIs('/admin/blocks/create/' . $this->blockType)
                ->type('#name-input', $this->blockName)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/blocks/create/' . $this->blockType)
                ->assertSee('The record was successfully created!');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_create_a_block_and_continue_editing_it()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-add');
        $this->admin->grantPermission('blocks-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickLink('Add New')
                ->typeSelect2('#type-input', $this->blockLabel)
                ->clickLink('Continue')
                ->assertPathIs('/admin/blocks/create/' . $this->blockType)
                ->type('#name-input', $this->blockName)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/blocks/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->blockName);
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_update_a_block()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/blocks', $this->blockModel)
                ->clickEditRecordButton($this->blockName)
                ->type('#name-input', $this->blockNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/blocks')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/blocks', $this->blockModel)
                ->assertSee($this->blockNameModified);
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_can_update_a_block_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/blocks', $this->blockModel)
                ->clickEditRecordButton($this->blockName)
                ->type('#name-input', $this->blockNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->blockNameModified);
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_can_delete_a_block_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-delete');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/blocks/', $this->blockModel)
                ->assertSee($this->blockName)
                ->clickDeleteRecordButton($this->blockName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/blocks/', $this->blockModel)
                ->assertDontSee($this->blockName);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_block_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->revokePermission('blocks-delete');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->assertSourceMissing('button-delete');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_filter_blocks_by_keyword()
    {
        $this->admin->grantPermission('blocks-list');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->filterRecordsByText('#search-input', $this->blockName)
                ->assertQueryStringHas('search', $this->blockName)
                ->assertSee($this->blockName)
                ->assertRecordsCount(1)
                ->visit('/admin/blocks')
                ->filterRecordsByText('#search-input', $this->blockNameModified)
                ->assertQueryStringHas('search', $this->blockNameModified)
                ->assertDontSee($this->blockName)
                ->assertSee('No records found');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_filter_blocks_by_type()
    {
        $this->admin->grantPermission('blocks-list');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->filterRecordsBySelect('#type-input', $this->blockLabel)
                ->assertQueryStringHas('type', $this->blockType)
                ->assertRecordsCount(1)
                ->assertSee($this->blockName);
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_filter_blocks_by_published()
    {
        $this->admin->grantPermission('blocks-list');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->filterRecordsBySelect('#drafted-input', 'Yes')
                ->assertQueryStringHas('drafted', 1)
                ->assertRecordsCount(1)
                ->assertSee($this->blockName)
                ->visit('/admin/blocks')
                ->filterRecordsBySelect('#drafted-input', 'No')
                ->assertQueryStringHas('drafted', 2)
                ->assertSee('No records found');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_filter_blocks_by_start_date()
    {
        $this->admin->grantPermission('blocks-list');

        $this->createBlock();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->visitLastPage('/admin/blocks', $this->blockModel)
                ->assertSee($this->blockName)
                ->visit('/admin/blocks')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_filter_blocks_by_end_date()
    {
        $this->admin->grantPermission('blocks-list');

        $this->createBlock();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/blocks')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/blocks', $this->blockModel)
                ->assertSee($this->blockName);
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_clear_block_filters()
    {
        $this->admin->grantPermission('blocks-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks/?search=list&type=test&drafted=1&trashed=2&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('type')
                ->assertQueryStringHas('drafted')
                ->assertQueryStringHas('trashed')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/blocks/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('type')
                ->assertQueryStringMissing('drafted')
                ->assertQueryStringMissing('trashed')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_block()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks/create/' . $this->blockType)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_unique_name_when_creating_a_block()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-add');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks/create/' . $this->blockType)
                ->type('#name-input', $this->blockName)
                ->press('Save')
                ->waitForText('The name has already been taken')
                ->assertSee('The name has already been taken');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_block()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->type('#name-input', '')
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function it_requires_a_unique_name_when_updating_a_block()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');

        $this->createBlock();
        $this->createBlockModified();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->type('#name-input', $this->blockNameModified)
                ->press('Save')
                ->waitForText('The name has already been taken')
                ->assertSee('The name has already been taken');
        });

        $this->deleteBlockModified();
        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_create_a_drafted_block_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks/create/' . $this->blockType)
                ->type('#name-input', $this->blockName)
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathBeginsWith('/admin/blocks/edit')
                ->assertSee('The draft was successfully created!')
                ->assertInputValue('#name-input', $this->blockName)
                ->assertSee('This record is currently drafted');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_create_a_drafted_block_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-add');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('blocks-draft');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks/create/' . $this->blockType)
                ->type('#name-input', $this->blockName)
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathBeginsWith('/admin/blocks/edit')
                ->assertSee('The draft was successfully created!')
                ->assertInputValue('#name-input', $this->blockName)
                ->assertSee('This record is currently drafted');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_cannot_create_a_drafted_block_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-add');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->revokePermission('blocks-draft');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks/create/' . $this->blockType)
                ->assertDontSee('Save As Draft');
        });
    }

    /** @test */
    public function an_admin_can_save_a_block_as_draft_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->type('#name-input', $this->blockNameModified)
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertSee('The draft was successfully updated!')
                ->assertInputValue('#name-input', $this->blockNameModified)
                ->assertSee('This record is currently drafted');
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_can_save_a_block_as_draft_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('blocks-draft');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->type('#name-input', $this->blockNameModified)
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertSee('The draft was successfully updated!')
                ->assertInputValue('#name-input', $this->blockNameModified)
                ->assertSee('This record is currently drafted');
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_cannot_save_a_block_as_draft_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->revokePermission('blocks-draft');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->assertDontSee('Save As Draft');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_publish_a_drafted_block_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBlock();

        $this->blockModel = $this->blockModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->clickPublishRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertSee('The draft was successfully published!')
                ->assertDontSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_publish_a_drafted_block_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('blocks-publish');

        $this->createBlock();

        $this->blockModel = $this->blockModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->clickPublishRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertSee('The draft was successfully published!')
                ->assertDontSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_cannot_publish_a_drafted_block_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->revokePermission('blocks-publish');

        $this->createBlock();

        $this->blockModel = $this->blockModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->assertSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_duplicate_a_block_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->clickDuplicateRecordButton()
                ->pause(500)
                ->assertPathIsNot('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertPathBeginsWith('/admin/blocks/edit')
                ->assertSee('The record was successfully duplicated')
                ->assertInputValue('#name-input', $this->blockName . ' (1)');
        });

        $this->deleteBlock();
        $this->deleteDuplicatedBlock();
    }

    /** @test */
    public function an_admin_can_duplicate_a_block_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('blocks-duplicate');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->clickDuplicateRecordButton()
                ->pause(500)
                ->assertPathIsNot('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertPathBeginsWith('/admin/blocks/edit')
                ->assertSee('The record was successfully duplicated')
                ->assertInputValue('#name-input', $this->blockName . ' (1)');
        });

        $this->deleteBlock();
        $this->deleteDuplicatedBlock();
    }

    /** @test */
    public function an_admin_cannot_duplicate_a_block_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->revokePermission('blocks-duplicate');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->assertDontSee('Duplicate');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_see_block_revisions_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('No User')
                ->assertSourceHas('button-view-revision')
                ->assertSourceHas('button-rollback-revision')
                ->assertSourceHas('button-delete-revision');
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_can_see_block_revisions_if_it_is_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('revisions-list');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('No User')
                ->assertDontSee('There are no revisions for this record');
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_cannot_see_block_revisions_if_it_is_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->revokePermission('revisions-list');

        $this->createBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockName)
                ->assertDontSee('Revisions Info');
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_view_a_block_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->pause(500)
                ->clickViewRevisionButton()
                ->assertPathBeginsWith('/admin/blocks/revision')
                ->assertSee('You are currently viewing a revision of the record')
                ->assertSee('Block Revision')
                ->assertInputValue('#name-input', $this->blockName);
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_can_view_a_block_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('revisions-list');

        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->pause(500)
                ->clickViewRevisionButton()
                ->assertPathBeginsWith('/admin/blocks/revision')
                ->assertSee('You are currently viewing a revision of the record')
                ->assertSee('Block Revision')
                ->assertInputValue('#name-input', $this->blockName);
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_can_rollback_a_block_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->clickRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertInputValue('#name-input', $this->blockName);
        });

        $this->deleteBlock();
        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->pressRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertInputValue('#name-input', $this->blockName);
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_can_rollback_a_block_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->grantPermission('revisions-rollback');

        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->clickRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertInputValue('#name-input', $this->blockName);
        });

        $this->deleteBlock();
        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->pressRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/blocks/edit/' . $this->blockModel->id)
                ->assertInputValue('#name-input', $this->blockName);
        });

        $this->deleteBlock();
    }

    /** @test */
    public function an_admin_cannot_rollback_a_block_revision_if_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->revokePermission('revisions-rollback');

        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->assertSourceMissing('class="button-rollback-revision');
        });

        $this->deleteBlockModified();
        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->assertDontSee('Rollback Revision')
                ->assertSourceMissing('class="button-rollback-revision');
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_can_delete_a_block_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->clickDeleteRevisionButton()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_can_delete_a_block_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->grantPermission('revisions-delete');

        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->clickDeleteRevisionButton()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->deleteBlockModified();
    }

    /** @test */
    public function an_admin_cannot_delete_a_block_revision_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('blocks-list');
        $this->admin->grantPermission('blocks-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->revokePermission('revisions-delete');

        $this->createBlock();
        $this->updateBlock();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/blocks')
                ->clickEditRecordButton($this->blockNameModified)
                ->openRevisionsContainer()
                ->assertSourceMissing('class="button-delete-revision');
        });

        $this->deleteBlockModified();
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
        Block::withDrafts()
            ->whereName($this->blockName)
            ->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteBlockModified()
    {
        Block::withDrafts()
            ->whereName($this->blockNameModified)
            ->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteDuplicatedBlock()
    {
        Block::withDrafts()
            ->whereName($this->blockName . ' (1)')
            ->first()->delete();
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
