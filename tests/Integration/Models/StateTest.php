<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Country;
use Varbox\Models\State;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class StateTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var State
     */
    protected $state;

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

        $this->assertTrue($this->state->country() instanceof BelongsTo);
        $this->assertEquals(1, $this->state->country()->count());
        $this->assertEquals($this->country->id, $this->state->country->id);
    }

    /** @test */
    public function it_has_many_cities()
    {
        $this->createCountry();
        $this->createState();

        for ($i = 1; $i <= 3; $i++) {
            $city = $this->state->cities()->create([
                'country_id' => $this->country->id,
                'name' => 'Test City ' . $i,
            ]);

            $this->assertEquals($this->state->id, $city->state_id);
        }

        $this->assertTrue($this->state->cities() instanceof HasMany);
        $this->assertEquals(3, $this->state->cities()->count());
    }

    /** @test */
    public function it_can_filter_records_by_country_using_the_model()
    {
        $this->createCountry();
        $this->createAnotherCountry();

        $this->country->states()->create(['name' => 'Some state', 'code' => 'SS']);
        $this->country->states()->create(['name' => 'Another state', 'code' => 'AS']);
        $this->anotherCountry->states()->create(['name' => 'The state', 'code' => 'TS']);

        $this->assertEquals(2, State::fromCountry($this->country)->count());
        $this->assertEquals(1, State::fromCountry($this->anotherCountry)->count());
    }

    /** @test */
    public function it_can_filter_records_by_country_using_the_id()
    {
        $this->createCountry();
        $this->createAnotherCountry();

        $this->country->states()->create(['name' => 'Some state', 'code' => 'SS']);
        $this->country->states()->create(['name' => 'Another state', 'code' => 'AS']);
        $this->anotherCountry->states()->create(['name' => 'The state', 'code' => 'TS']);

        $this->assertEquals(2, State::fromCountry($this->country->id)->count());
        $this->assertEquals(1, State::fromCountry($this->anotherCountry->id)->count());
    }

    /** @test */
    public function it_can_sort_records_alphabetically()
    {
        $this->createCountry();

        $this->country->states()->create(['name' => 'Some state', 'code' => 'SS']);
        $this->country->states()->create(['name' => 'Another state', 'code' => 'AS']);
        $this->country->states()->create(['name' => 'The state', 'code' => 'TS']);

        $this->assertEquals('Another state', State::alphabetically()->get()->first()->name);
        $this->assertEquals('The state', State::alphabetically()->get()->last()->name);
    }

    /**
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
     * @return void
     */
    protected function createAnotherCountry()
    {
        $this->anotherCountry = Country::create([
            'name' => 'Test Another Country Name',
            'code' => 'TACN',
        ]);
    }
}
