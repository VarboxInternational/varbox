<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Exceptions\CrudException;
use Varbox\Models\Language;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class LanguageTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Language
     */
    protected $english;

    /**
     * @var Language
     */
    protected $romanian;

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Language::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Language::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Language::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Language::class));
    }

    /** @test */
    public function it_doesnt_allow_saving_without_a_default_language()
    {
        $this->createLanguage();
        $this->expectException(CrudException::class);
        $this->expectExceptionMessage('A default language is required at all times!');

        $this->english->update([
            'default' => false
        ]);
    }

    /** @test */
    public function it_doesnt_allow_deleting_the_default()
    {
        $this->createLanguage();
        $this->expectException(CrudException::class);
        $this->expectExceptionMessage('Deleting the default language is restricted!');

        $this->english->delete();
    }

    /** @test */
    public function it_syncs_the_default_for_a_single_language()
    {
        $this->createLanguage();

        $this->assertTrue($this->english->fresh()->default);

        Language::create([
            'name' => 'Test',
            'code' => 'te',
            'default' => true,
        ]);

        $this->assertFalse($this->english->fresh()->default);
    }

    /** @test */
    public function it_can_filter_records_by_default()
    {
        $this->createLanguage();
        $this->createAnotherLanguage();

        $this->assertEquals(1, Language::onlyDefault()->count());
        $this->assertEquals($this->english->name, Language::onlyDefault()->first()->name);

        $this->assertEquals(1, Language::excludingDefault()->count());
        $this->assertEquals($this->romanian->name, Language::excludingDefault()->first()->name);
    }

    /** @test */
    public function it_can_filter_records_by_active()
    {
        $this->createLanguage();
        $this->createAnotherLanguage();

        $this->assertEquals(1, Language::onlyActive()->count());
        $this->assertEquals($this->english->name, Language::onlyActive()->first()->name);

        $this->assertEquals(1, Language::onlyInactive()->count());
        $this->assertEquals($this->romanian->name, Language::onlyInactive()->first()->name);
    }

    /** @test */
    public function it_can_sort_records_alphabetically()
    {
        Language::create(['name' => 'Some language', 'code' => 'SL']);
        Language::create(['name' => 'Another language', 'code' => 'AL']);
        Language::create(['name' => 'The language', 'code' => 'TL']);

        $this->assertEquals('Another language', Language::alphabetically()->get()->first()->name);
        $this->assertEquals('The language', Language::alphabetically()->get()->last()->name);
    }

    /**
     * @return void
     */
    protected function createLanguage()
    {
        $this->english = Language::create([
            'name' => 'English',
            'code' => 'en',
            'default' => true,
            'active' => true,
        ]);
    }

    /**
     * @return void
     */
    protected function createAnotherLanguage()
    {
        $this->romanian = Language::create([
            'name' => 'Romanian',
            'code' => 'ro',
            'default' => false,
            'active' => false,
        ]);
    }
}
