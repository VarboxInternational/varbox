<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Address;
use Varbox\Models\City;
use Varbox\Models\Country;
use Varbox\Models\State;
use Varbox\Models\User;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class AddressTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $anotherUser;

    /**
     * @var City
     */
    protected $city;

    /**
     * @var City
     */
    protected $anotherCity;

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
    public function it_belongs_to_a_user()
    {
        $this->createUser();

        $this->address = $this->user->addresses()->create([
            'address' => 'Test Address',
        ]);

        $this->assertTrue($this->address->user() instanceof BelongsTo);
        $this->assertEquals(1, $this->address->user()->count());
        $this->assertEquals($this->user->id, $this->address->user->id);
    }

    /** @test */
    public function it_belongs_to_a_country()
    {
        $this->createUser();
        $this->createCountry();

        $this->address = $this->user->addresses()->create([
            'country_id' => $this->country->id,
            'address' => 'Test Address',
        ]);

        $this->assertTrue($this->address->country() instanceof BelongsTo);
        $this->assertEquals(1, $this->address->country()->count());
        $this->assertEquals($this->country->id, $this->address->country->id);
    }

    /** @test */
    public function it_belongs_to_a_state()
    {
        $this->createUser();
        $this->createCountry();
        $this->createState();

        $this->address = $this->user->addresses()->create([
            'state_id' => $this->state->id,
            'address' => 'Test Address',
        ]);

        $this->assertTrue($this->address->state() instanceof BelongsTo);
        $this->assertEquals(1, $this->address->state()->count());
        $this->assertEquals($this->state->id, $this->address->state->id);
    }

    /** @test */
    public function it_belongs_to_a_city()
    {
        $this->createUser();
        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->address = $this->user->addresses()->create([
            'city_id' => $this->city->id,
            'address' => 'Test Address',
        ]);

        $this->assertTrue($this->address->city() instanceof BelongsTo);
        $this->assertEquals(1, $this->address->city()->count());
        $this->assertEquals($this->city->id, $this->address->city->id);
    }

    /** @test */
    public function it_can_filter_records_by_user_using_the_model()
    {
        $this->createUser();
        $this->createAnotherUser();

        $this->user->addresses()->create(['address' => 'Some address']);
        $this->user->addresses()->create(['address' => 'Another address']);
        $this->anotherUser->addresses()->create(['address' => 'The address']);

        $this->assertEquals(2, Address::ofUser($this->user)->count());
        $this->assertEquals(1, Address::ofUser($this->anotherUser)->count());
    }

    /** @test */
    public function it_can_filter_records_by_user_using_the_id()
    {
        $this->createUser();
        $this->createAnotherUser();

        $this->user->addresses()->create(['address' => 'Some address']);
        $this->user->addresses()->create(['address' => 'Another address']);
        $this->anotherUser->addresses()->create(['address' => 'The address']);

        $this->assertEquals(2, Address::ofUser($this->user->id)->count());
        $this->assertEquals(1, Address::ofUser($this->anotherUser->id)->count());
    }

    /** @test */
    public function it_can_filter_records_by_country_using_the_model()
    {
        $this->createUser();
        $this->createCountry();
        $this->createAnotherCountry();

        $this->user->addresses()->create(['country_id' => $this->country->id, 'address' => 'Some address']);
        $this->user->addresses()->create(['country_id' => $this->country->id, 'address' => 'Another address']);
        $this->user->addresses()->create(['country_id' => $this->anotherCountry->id, 'address' => 'The address']);

        $this->assertEquals(2, Address::fromCountry($this->country)->count());
        $this->assertEquals(1, Address::fromCountry($this->anotherCountry)->count());
    }

    /** @test */
    public function it_can_filter_records_by_country_using_the_id()
    {
        $this->createUser();
        $this->createCountry();
        $this->createAnotherCountry();

        $this->user->addresses()->create(['country_id' => $this->country->id, 'address' => 'Some address']);
        $this->user->addresses()->create(['country_id' => $this->country->id, 'address' => 'Another address']);
        $this->user->addresses()->create(['country_id' => $this->anotherCountry->id, 'address' => 'The address']);

        $this->assertEquals(2, Address::fromCountry($this->country->id)->count());
        $this->assertEquals(1, Address::fromCountry($this->anotherCountry->id)->count());
    }

    /** @test */
    public function it_can_filter_records_by_state_using_the_model()
    {
        $this->createUser();
        $this->createCountry();
        $this->createAnotherCountry();
        $this->createState();
        $this->createAnotherState();

        $this->user->addresses()->create(['state_id' => $this->state->id, 'address' => 'Some address']);
        $this->user->addresses()->create(['state_id' => $this->state->id, 'address' => 'Another address']);
        $this->user->addresses()->create(['state_id' => $this->anotherState->id, 'address' => 'The address']);

        $this->assertEquals(2, Address::fromState($this->state)->count());
        $this->assertEquals(1, Address::fromState($this->anotherState)->count());
    }

    /** @test */
    public function it_can_filter_records_by_state_using_the_id()
    {
        $this->createUser();
        $this->createCountry();
        $this->createAnotherCountry();
        $this->createState();
        $this->createAnotherState();

        $this->user->addresses()->create(['state_id' => $this->state->id, 'address' => 'Some address']);
        $this->user->addresses()->create(['state_id' => $this->state->id, 'address' => 'Another address']);
        $this->user->addresses()->create(['state_id' => $this->anotherState->id, 'address' => 'The address']);

        $this->assertEquals(2, Address::fromState($this->state->id)->count());
        $this->assertEquals(1, Address::fromState($this->anotherState->id)->count());
    }

    /** @test */
    public function it_can_filter_records_by_city_using_the_model()
    {
        $this->createUser();
        $this->createCountry();
        $this->createAnotherCountry();
        $this->createState();
        $this->createAnotherState();
        $this->createCity();
        $this->createAnotherCity();

        $this->user->addresses()->create(['city_id' => $this->city->id, 'address' => 'Some address']);
        $this->user->addresses()->create(['city_id' => $this->city->id, 'address' => 'Another address']);
        $this->user->addresses()->create(['city_id' => $this->anotherCity->id, 'address' => 'The address']);

        $this->assertEquals(2, Address::fromCity($this->city)->count());
        $this->assertEquals(1, Address::fromCity($this->anotherCity)->count());
    }

    /** @test */
    public function it_can_filter_records_by_city_using_the_id()
    {
        $this->createUser();
        $this->createCountry();
        $this->createAnotherCountry();
        $this->createState();
        $this->createAnotherState();
        $this->createCity();
        $this->createAnotherCity();

        $this->user->addresses()->create(['city_id' => $this->city->id, 'address' => 'Some address']);
        $this->user->addresses()->create(['city_id' => $this->city->id, 'address' => 'Another address']);
        $this->user->addresses()->create(['city_id' => $this->anotherCity->id, 'address' => 'The address']);

        $this->assertEquals(2, Address::fromCity($this->city->id)->count());
        $this->assertEquals(1, Address::fromCity($this->anotherState->id)->count());
    }
    
    /**
     * @return void
     */
    protected function createAddress()
    {
        $this->address = Address::create([
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'address' => 'Test Address',
        ]);
    }

    /**
     * @return void
     */
    protected function createUser()
    {
        $this->user = User::create([
            'email' => 'test-user@mail.com',
            'password' => 'test_password',
        ]);
    }

    /**
     * @return void
     */
    protected function createAnotherUser()
    {
        $this->anotherUser = User::create([
            'email' => 'test-another-user@mail.com',
            'password' => 'test_another_password',
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
    protected function createAnotherState()
    {
        $this->anotherState = State::create([
            'country_id' => $this->anotherCountry->id,
            'name' => 'Test Another State Name',
            'code' => 'TASN',
        ]);
    }

    /**
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
     * @return void
     */
    protected function createAnotherCity()
    {
        $this->anotherCity = City::create([
            'country_id' => $this->anotherCountry->id,
            'state_id' => $this->anotherState->id,
            'name' => 'Test Another City Name',
        ]);
    }
}
