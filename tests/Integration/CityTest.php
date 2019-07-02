<?php

namespace Varbox\Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\City;
use Varbox\Models\Country;
use Varbox\Models\State;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class CityTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var City
     */
    protected $city;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var State
     */
    protected $anotherState;

    /**
     * @var Country
     */
    protected $country;

    /**
     * @var Country
     */
    protected $anotherCountry;

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
    public function it_belongs_to_a_country()
    {
        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->assertEquals(1, $this->city->country()->count());
        $this->assertEquals($this->country->id, $this->city->country->id);
    }

    /** @test */
    public function it_belongs_to_a_state()
    {
        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->assertEquals(1, $this->city->state()->count());
        $this->assertEquals($this->state->id, $this->city->state->id);
    }

    /** @test */
    public function it_can_filter_records_by_country_using_the_model()
    {
        $this->createCountry();
        $this->createAnotherCountry();

        $this->country->cities()->create(['name' => 'Some state', 'code' => 'SS']);
        $this->country->cities()->create(['name' => 'Another state', 'code' => 'AS']);
        $this->anotherCountry->cities()->create(['name' => 'The state', 'code' => 'TS']);

        $this->assertEquals(2, City::fromCountry($this->country)->count());
        $this->assertEquals(1, City::fromCountry($this->anotherCountry)->count());
    }

    /** @test */
    public function it_can_filter_records_by_country_using_the_id()
    {
        $this->createCountry();
        $this->createAnotherCountry();

        $this->country->cities()->create(['name' => 'Some city', 'code' => 'SS']);
        $this->country->cities()->create(['name' => 'Another city', 'code' => 'AS']);
        $this->anotherCountry->cities()->create(['name' => 'The city', 'code' => 'TS']);

        $this->assertEquals(2, City::fromCountry($this->country->id)->count());
        $this->assertEquals(1, City::fromCountry($this->anotherCountry->id)->count());
    }

    /** @test */
    public function it_can_filter_records_by_state_using_the_model()
    {
        $this->createCountry();
        $this->createAnotherCountry();
        $this->createState();
        $this->createAnotherState();

        $this->state->cities()->create([
            'country_id' => $this->country->id, 'name' => 'Some city', 'code' => 'SS'
        ]);

        $this->state->cities()->create([
            'country_id' => $this->country->id, 'name' => 'Another city', 'code' => 'AS'
        ]);

        $this->anotherState->cities()->create([
            'country_id' => $this->anotherCountry->id, 'name' => 'The city', 'code' => 'TS'
        ]);

        $this->assertEquals(2, City::fromState($this->state)->count());
        $this->assertEquals(1, City::fromState($this->anotherState)->count());
    }

    /** @test */
    public function it_can_filter_records_by_state_using_the_id()
    {
        $this->createCountry();
        $this->createAnotherCountry();
        $this->createState();
        $this->createAnotherState();

        $this->state->cities()->create([
            'country_id' => $this->country->id, 'name' => 'Some city', 'code' => 'SS'
        ]);

        $this->state->cities()->create([
            'country_id' => $this->country->id, 'name' => 'Another city', 'code' => 'AS'
        ]);

        $this->anotherState->cities()->create([
            'country_id' => $this->anotherCountry->id, 'name' => 'The city', 'code' => 'TS'
        ]);

        $this->assertEquals(2, City::fromState($this->state->id)->count());
        $this->assertEquals(1, City::fromState($this->anotherState->id)->count());
    }

    /** @test */
    public function it_can_sort_records_alphabetically()
    {
        $this->createCountry();

        $this->country->cities()->create(['name' => 'Some city', 'code' => 'SS']);
        $this->country->cities()->create(['name' => 'Another city', 'code' => 'AS']);
        $this->country->cities()->create(['name' => 'The city', 'code' => 'TS']);

        $this->assertEquals('Another city', City::alphabetically()->get()->first()->name);
        $this->assertEquals('The city', City::alphabetically()->get()->last()->name);
    }

    /**
     * Create a state for testing purposes.
     *
     * @return void
     */
    protected function createCity()
    {
        $this->city = City::create([
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'name' => 'Test City Name',
        ]);
    }

    /**
     * Create a country for testing purposes.
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

    /**
     * Create a country for testing purposes.
     *
     * @return void
     */
    protected function createAnotherCountry()
    {
        $this->anotherCountry = Country::create([
            'name' => 'Test Another Country Name',
            'code' => 'TACN',
        ]);
    }

    /**
     * Create a state for testing purposes.
     *
     * @return void
     */
    protected function createState()
    {
        $this->state = State::create([
            'country_id' => $this->country->id,
            'name' => 'Test State Name',
            'code' => 'TSN',
        ]);
    }

    /**
     * Create a state for testing purposes.
     *
     * @return void
     */
    protected function createAnotherState()
    {
        $this->anotherState = State::create([
            'country_id' => $this->anotherCountry->id,
            'name' => 'Test Another State Name',
            'code' => 'TASN',
        ]);
    }
}
