<?php

namespace Varbox\Tests\Integration\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Varbox\Tests\Integration\TestCase;

class BlockMakeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var string
     */
    protected $blockType;

    /**
     * @var string
     */
    protected $blockLabel;

    /**
     * @var string
     */
    protected $blockClass;

    /**
     * @var string
     */
    protected $blockAdminView;

    /**
     * @var string
     */
    protected $blockFrontView;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->blockType = 'TestType';
        $this->blockLabel = 'Test Type Block';
        $this->blockClass = app_path('Blocks/' . $this->blockType . '/Composer.php');
        $this->blockAdminView = app_path('Blocks/' . $this->blockType . '/Views/admin.blade.php');
        $this->blockFrontView = app_path('Blocks/' . $this->blockType . '/Views/front.blade.php');

        $this->app['config']->set('varbox.blocks.types', [
            $this->blockType => [
                'label' => $this->blockLabel,
                'composer_class' => $this->blockClass,
                'views_path' => 'app/Blocks/' . $this->blockType . '/Views',
            ]
        ]);

        $this->beforeApplicationDestroyed(function () {
            File::deleteDirectory(app_path('Blocks'));
        });
    }

    /** @test */
    public function it_creates_the_necessary_block_files()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType, '--no-interaction' => true])
            ->expectsOutput('Block created successfully inside the "app/Blocks/' . $this->blockType . '/" directory!')
            ->assertExitCode(0);

        File::exists($this->blockClass);
        File::exists($this->blockAdminView);
        File::exists($this->blockFrontView);
    }

    /** @test */
    public function it_fails_if_the_block_files_for_the_specified_type_already_exists()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType, '--no-interaction' => true])
            ->assertExitCode(0);

        $this->artisan('varbox:make-block', ['type' => $this->blockType, '--no-interaction' => true])
            ->expectsOutput('There is already a block with the name of "' . $this->blockType . '".')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_populate_the_block_class_with_the_asked_locations()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType])
            ->expectsQuestion($this->blockLocationsQuestion(), 'header content footer')
            ->expectsQuestion($this->blockDummyFieldsQuestion(), 'no')
            ->expectsQuestion($this->blockMultipleItemsQuestion(), 'no')
            ->assertExitCode(0);

        $this->assertTrue(Str::contains(File::get($this->blockClass), "'header'"));
        $this->assertTrue(Str::contains(File::get($this->blockClass), "'content'"));
        $this->assertTrue(Str::contains(File::get($this->blockClass), "'footer'"));
    }

    /** @test */
    public function it_can_populate_the_block_admin_view_with_dummy_fields()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType])
            ->expectsQuestion($this->blockLocationsQuestion(), null)
            ->expectsQuestion($this->blockDummyFieldsQuestion(), 'yes')
            ->expectsQuestion($this->blockMultipleItemsQuestion(), 'no')
            ->assertExitCode(0);

        $this->assertTrue(Str::contains(File::get($this->blockAdminView), "Details Info"));
    }

    /** @test */
    public function it_can_ignore_populating_the_block_admin_view_with_dummy_fields()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType])
            ->expectsQuestion($this->blockLocationsQuestion(), null)
            ->expectsQuestion($this->blockDummyFieldsQuestion(), 'no')
            ->expectsQuestion($this->blockMultipleItemsQuestion(), 'no')
            ->assertExitCode(0);

        $this->assertEquals('', File::get($this->blockAdminView));
    }

    /** @test */
    public function it_can_populate_the_block_admin_view_with_multiple_items()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType])
            ->expectsQuestion($this->blockLocationsQuestion(), null)
            ->expectsQuestion($this->blockDummyFieldsQuestion(), 'no')
            ->expectsQuestion($this->blockMultipleItemsQuestion(), 'yes')
            ->assertExitCode(0);

        $this->assertTrue(Str::contains(File::get($this->blockAdminView), [
            'js-MultipleItemAdd',
            'js-MultipleItemContainer',
            'js-MultipleItem',
            'js-MultipleItemButtons',
            'js-MultipleItemMoveUp',
            'js-MultipleItemMoveDown',
            'js-MultipleItemDelete',
            'js-MultipleItemTemplate'
        ]));
    }

    /** @test */
    public function it_can_ignore_populating_the_block_admin_view_with_multiple_items()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType])
            ->expectsQuestion($this->blockLocationsQuestion(), null)
            ->expectsQuestion($this->blockDummyFieldsQuestion(), 'no')
            ->expectsQuestion($this->blockMultipleItemsQuestion(), 'no')
            ->assertExitCode(0);

        $this->assertEquals('', File::get($this->blockAdminView));
    }

    /** @test */
    public function it_can_generate_a_block_with_all_combined_questions_answered_positively()
    {
        $this->artisan('varbox:make-block', ['type' => $this->blockType])
            ->expectsQuestion($this->blockLocationsQuestion(), 'header content footer')
            ->expectsQuestion($this->blockDummyFieldsQuestion(), 'yes')
            ->expectsQuestion($this->blockMultipleItemsQuestion(), 'yes')
            ->assertExitCode(0);

        $this->assertTrue(Str::contains(File::get($this->blockClass), "'header'"));
        $this->assertTrue(Str::contains(File::get($this->blockClass), "'content'"));
        $this->assertTrue(Str::contains(File::get($this->blockClass), "'footer'"));

        $this->assertTrue(Str::contains(File::get($this->blockAdminView), "Details Info"));

        $this->assertTrue(Str::contains(File::get($this->blockAdminView), [
            'js-MultipleItemAdd',
            'js-MultipleItemContainer',
            'js-MultipleItem',
            'js-MultipleItemButtons',
            'js-MultipleItemMoveUp',
            'js-MultipleItemMoveDown',
            'js-MultipleItemDelete',
            'js-MultipleItemTemplate'
        ]));
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
