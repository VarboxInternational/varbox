<?php

namespace Varbox\Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Country;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class CountryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Country
     */
    protected $country;

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Country::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Country::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Country::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Country::class));
    }

    /** @test */
    public function it_has_many_states()
    {
        $this->createCountry();

        for ($i = 1; $i <= 3; $i++) {
            $state = $this->country->states()->create([
                'name' => 'Test State ' . $i,
                'code' => 'TS' . $i,
            ]);

            $this->assertEquals($this->country->id, $state->country_id);
        }

        $this->assertEquals(3, $this->country->states()->count());
    }

    /** @test */
    public function it_has_many_cities()
    {
        $this->createCountry();

        for ($i = 1; $i <= 3; $i++) {
            $city = $this->country->cities()->create([
                'name' => 'Test City ' . $i,
            ]);

            $this->assertEquals($this->country->id, $city->country_id);
        }

        $this->assertEquals(3, $this->country->cities()->count());
    }

    /** @test */
    public function it_can_sort_records_alphabetically()
    {
        Country::create(['name' => 'Some country', 'code' => 'SC']);
        Country::create(['name' => 'Another country', 'code' => 'AC']);
        Country::create(['name' => 'The country', 'code' => 'TC']);

        $this->assertEquals('Another country', Country::alphabetically()->get()->first()->name);
        $this->assertEquals('The country', Country::alphabetically()->get()->last()->name);
    }

    /**
     * Set up testing conditions for roles.
     *
     * @return void
     */
    protected function createCountry()
    {
        $this->country = Country::create([
            'name' => 'Test Country Name',
            'code' => 'TCN',
        ]);
    }
}
