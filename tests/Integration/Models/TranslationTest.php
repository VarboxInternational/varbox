<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Translation;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class TranslationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Translation
     */
    protected $translation;

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Translation::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Translation::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Translation::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Translation::class));
    }

    /** @test */
    public function it_stores_empty_values_as_null()
    {
        $translation = Translation::create([
            'locale' => 'en',
            'key' => 'test',
            'group' => 'test',
            'value' => '',
        ]);

        $this->assertNull($translation->value);
    }

    /** @test */
    public function it_can_filter_records_by_value()
    {
        $translation1 = Translation::create([
            'locale' => 'en',
            'key' => 'test_key_1',
            'group' => 'test_group_1',
            'value' => 'test value 1',
        ]);

        $translation2 = Translation::create([
            'locale' => 'en',
            'key' => 'test_key_2',
            'group' => 'test_group_2',
            'value' => '',
        ]);

        $this->assertEquals(1, Translation::withValue()->count());
        $this->assertEquals(1, Translation::withoutValue()->count());
        $this->assertEquals('test_key_1', Translation::withValue()->first()->key);
        $this->assertEquals('test_key_2', Translation::withoutValue()->first()->key);
    }

    /** @test */
    public function it_can_filter_records_by_group()
    {
        $translation1 = Translation::create([
            'locale' => 'en',
            'key' => 'test_key_1',
            'group' => 'test_group_1',
            'value' => 'test value 1',
        ]);

        $translation2 = Translation::create([
            'locale' => 'en',
            'key' => 'test_key_2',
            'group' => 'test_group_2',
            'value' => 'test value 2',
        ]);

        $this->assertEquals(1, Translation::withGroup('test_group_1')->count());
        $this->assertEquals(1, Translation::withGroup('test_group_2')->count());
        $this->assertEquals('test_key_1', Translation::withoutGroup('test_group_2')->first()->key);
        $this->assertEquals('test_key_2', Translation::withoutGroup('test_group_1')->first()->key);
    }
}
