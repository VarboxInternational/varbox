<?php

namespace Varbox\Tests\Integration\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Varbox\Models\Language;
use Varbox\Models\Translation;
use Varbox\Services\TranslationService;
use Varbox\Tests\Integration\TestCase;

class TranslationServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Language
     */
    protected $language;

    /**
     * @var string
     */
    protected $locale = 'test';

    /**
     * @var string
     */
    protected $phpFile = 'test.php';

    /**
     * @var string
     */
    protected $jsonFile = 'test.json';

    /**
     * @var array
     */
    protected $fileTranslations = [
        'test_translation_1' => 'Testing file translation one',
        'test_translation_2' => 'Testing file translation two',
    ];

    /**
     * @var array
     */
    protected $jsonTranslations = [
        'Test json translation 1' => 'Testing json translation one',
        'Test json translation 2' => 'Testing json translation two',
    ];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->language = Language::create([
            'name' => 'Test language',
            'code' => $this->locale,
            'active' => true,
        ]);

        $this->afterApplicationCreated(function () {
            $fileOutput = "<?php\n\nreturn " . var_export($this->fileTranslations, true) . ";\n";
            $jsonOutput = json_encode($this->jsonTranslations, true);

            File::makeDirectory($this->app['path.lang'] . '/' . $this->locale);

            File::put($this->app['path.lang'] . '/' . $this->locale . '/' . $this->phpFile, $fileOutput);
            File::put($this->app['path.lang'] . '/' . $this->jsonFile, $jsonOutput);
        });

        $this->beforeApplicationDestroyed(function () {
            File::deleteDirectory($this->app['path.lang'] . '/' . $this->locale);
            File::delete($this->app['path.lang'] . '/' . $this->jsonFile);
        });
    }

    /** @test */
    public function it_can_import_static_file_translations_into_the_database()
    {
        $this->assertEquals(0, Translation::count());

        app(TranslationService::class)->importTranslations();

        $fileTranslations = Translation::withGroup('test')->get();
        $jsonTranslations = Translation::withGroup('_json')->get();

        $this->assertEquals(4, Translation::count());
        $this->assertEquals(2, $fileTranslations->count());
        $this->assertEquals(2, $jsonTranslations->count());

        $this->assertEquals('test_translation_1', $fileTranslations->first()->key);
        $this->assertEquals('Testing file translation one', $fileTranslations->first()->value);
        $this->assertEquals('test', $fileTranslations->first()->group);
        $this->assertEquals($this->locale, $fileTranslations->first()->locale);

        $this->assertEquals('test_translation_2', $fileTranslations->last()->key);
        $this->assertEquals('Testing file translation two', $fileTranslations->last()->value);
        $this->assertEquals('test', $fileTranslations->last()->group);
        $this->assertEquals($this->locale, $fileTranslations->last()->locale);

        $this->assertEquals('Test json translation 1', $jsonTranslations->first()->key);
        $this->assertEquals('Testing json translation one', $jsonTranslations->first()->value);
        $this->assertEquals('_json', $jsonTranslations->first()->group);
        $this->assertEquals($this->locale, $jsonTranslations->first()->locale);

        $this->assertEquals('Test json translation 2', $jsonTranslations->last()->key);
        $this->assertEquals('Testing json translation two', $jsonTranslations->last()->value);
        $this->assertEquals('_json', $jsonTranslations->last()->group);
        $this->assertEquals($this->locale, $jsonTranslations->last()->locale);
    }

    /** @test */
    public function it_can_export_static_translations_to_their_files()
    {
        app(TranslationService::class)->importTranslations();

        Translation::where('key', 'test_translation_1')->update([
            'value' => 'Testing translation one modified'
        ]);

        Translation::where('key', 'test_translation_2')->update([
            'value' => 'Testing translation two modified'
        ]);

        Translation::where('key', 'Test json translation 1')->update([
            'value' => 'Testing json translation one modified'
        ]);

        Translation::where('key', 'Test json translation 2')->update([
            'value' => 'Testing json translation two modified'
        ]);

        app(TranslationService::class)->exportTranslations();

        $fileContents = File::get($this->app['path.lang'] . '/' . $this->locale . '/' . $this->phpFile);
        $jsonContents = File::get($this->app['path.lang'] . '/' . $this->jsonFile);

        $this->assertStringContainsString('Testing translation one modified', $fileContents);
        $this->assertStringContainsString('Testing translation two modified', $fileContents);

        $this->assertStringContainsString('Testing json translation one modified', $jsonContents);
        $this->assertStringContainsString('Testing json translation two modified', $jsonContents);
    }
}
