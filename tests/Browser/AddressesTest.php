<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Address;
use Varbox\Models\City;
use Varbox\Models\Country;
use Varbox\Models\State;
use Varbox\Models\User;

class AddressesTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Address
     */
    protected $addressModel;

    /**
     * @var Country
     */
    protected $countryModel;

    /**
     * @var State
     */
    protected $stateModel;

    /**
     * @var City
     */
    protected $cityModel;

    /**
     * @var string
     */
    protected $addressAddress = 'Test Address';

    /**
     * @var string
     */
    protected $countryName = 'Test Country Name';
    protected $countryCode = 'TCC';

    /**
     * @var string
     */
    protected $stateName = 'Test State Name';
    protected $stateCode = 'TSC';

    /**
     * @var string
     */
    protected $cityName = 'Test City Name';

    protected $addressAddressModified = 'Test Address Modified';
    protected $countryNameModified = 'Test Country Name Modified';
    protected $stateNameModified = 'Test State Name Modified';
    protected $cityNameModified = 'Test City Name Modified';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(function () {
            $this->user = User::create([
                'email' => 'test-user@mail.com',
                'password' => bcrypt('test_password'),
            ]);
        });

        $this->beforeApplicationDestroyed(function () {
            $this->user->forceDelete();
        });
    }

    /** @test */
    public function an_admin_can_see_the_addresses_button_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->assertSee('View User\'s Addresses');
        });
    }

    /** @test */
    public function an_admin_can_see_the_addresses_button_if_it_has_permission()
    {
        $this->admin->grantPermission('users-edit');
        $this->admin->grantPermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->assertSee('View User\'s Addresses');
        });
    }

    /** @test */
    public function an_admin_cannot_see_the_addresses_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('users-edit');
        $this->admin->revokePermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->assertDontSee('View User\'s Addresses');
        });
    }

    /** @test */
    public function an_admin_can_access_user_addresses_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->clickLink('View User\'s Addresses')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Addresses');
        });
    }

    /** @test */
    public function an_admin_can_access_user_addresses_if_it_has_permission()
    {
        $this->admin->grantPermission('users-edit');
        $this->admin->grantPermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->clickLink('View User\'s Addresses')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Addresses');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Addresses')
                ->assertSee('You are currently viewing the addresses for user: ' . $this->user->email);
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Addresses')
                ->assertSee('You are currently viewing the addresses for user: ' . $this->user->email);
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Unauthorized')
                ->assertDontSee('Addresses');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/create')
                ->assertSee('Add Address')
                ->assertSee('You are currently adding an address for user: ' . $this->user->email);
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/create')
                ->assertSee('Add Address')
                ->assertSee('You are currently adding an address for user: ' . $this->user->email);
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->revokePermission('addresses-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Address');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->clickEditButton($this->addressAddress)
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/edit/' . $this->addressModel->id)
                ->assertSee('Edit Address')
                ->assertSee('You are currently editing an address of user: ' . $this->user->email);
        });

        $this->deleteAddress();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-edit');

        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->clickEditButton($this->addressAddress)
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/edit/' . $this->addressModel->id)
                ->assertSee('Edit Address')
                ->assertSee('You are currently editing an address of user: ' . $this->user->email);
        });

        $this->deleteAddress();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->revokePermission('addresses-edit');

        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->clickEditButton($this->addressAddress)
                ->assertSee('Unauthorized');
        });

        $this->deleteAddress();
    }

    /** @test */
    public function an_admin_can_create_an_address()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-add');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->type('#address-input', $this->addressAddress)
                ->select2('#country_id-input', $this->countryName)
                ->select2('#state_id-input', $this->stateName)
                ->select2('#city_id-input', $this->cityName)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', new Address)
                ->assertSee($this->addressAddress)
                ->assertSee($this->countryName)
                ->assertSee($this->stateName)
                ->assertSee($this->cityName);
        });

        $this->deleteAddress();
        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_an_address_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-add');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->type('#address-input', $this->addressAddress)
                ->select2('#country_id-input', $this->countryName)
                ->select2('#state_id-input', $this->stateName)
                ->select2('#city_id-input', $this->cityName)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteAddress();
        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_an_address_and_continue_editing_it()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-add');
        $this->admin->grantPermission('addresses-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->type('#address-input', $this->addressAddress)
                ->select2('#country_id-input', $this->countryName)
                ->select2('#state_id-input', $this->stateName)
                ->select2('#city_id-input', $this->cityName)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/users/' . $this->user->id . '/addresses/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#address-input', $this->addressAddress)
                ->assertSee($this->countryName)
                ->assertSee($this->stateName)
                ->assertSee($this->cityName);
        });

        $this->deleteAddress();
        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_update_an_address()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();
        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->clickEditButton($this->addressAddress)
                ->type('#address-input', $this->addressAddressModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->assertSee($this->addressAddressModified);
        });

        $this->deleteAddressModified();
        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_update_an_address_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();
        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->clickEditButton($this->addressAddress)
                ->type('#address-input', $this->addressAddressModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/edit/' . $this->addressModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#address-input', $this->addressAddressModified);
        });

        $this->deleteAddressModified();
        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_delete_an_address_if_it_has_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-delete');

        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->assertSee($this->addressAddress)
                ->deleteRecord($this->addressAddress)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->assertDontSee($this->addressAddress);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_an_address_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->revokePermission('addresses-delete');

        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });

        $this->deleteAddress();
    }

    /** @test */
    public function an_admin_can_filter_addresses_by_keyword()
    {
        $this->admin->grantPermission('addresses-list');

        $this->createCountry();
        $this->createState();
        $this->createCity();
        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#search-input', $this->addressAddress)
                ->assertQueryStringHas('search', $this->addressAddress)
                ->assertSee($this->addressAddress)
                ->assertRecordsCount(1)
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#search-input', $this->addressAddressModified)
                ->assertQueryStringHas('search', $this->addressAddressModified)
                ->assertSee('No records found')


                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#search-input', $this->countryName)
                ->assertQueryStringHas('search', $this->countryName)
                ->assertSee($this->countryName)
                ->assertRecordsCount(1)
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#search-input', $this->countryNameModified)
                ->assertQueryStringHas('search', $this->countryNameModified)
                ->assertSee('No records found')


                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#search-input', $this->stateName)
                ->assertQueryStringHas('search', $this->stateName)
                ->assertSee($this->stateName)
                ->assertRecordsCount(1)
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#search-input', $this->stateNameModified)
                ->assertQueryStringHas('search', $this->stateNameModified)
                ->assertSee('No records found')


                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#search-input', $this->cityName)
                ->assertQueryStringHas('search', $this->cityName)
                ->assertSee($this->cityName)
                ->assertRecordsCount(1)
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#search-input', $this->cityNameModified)
                ->assertQueryStringHas('search', $this->cityNameModified)
                ->assertSee('No records found');
        });

        $this->deleteAddress();
        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_addresses_by_start_date()
    {
        $this->admin->grantPermission('addresses-list');

        $this->createAddress();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->assertSee($this->addressAddress)
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteAddress();
    }

    /** @test */
    public function an_admin_can_filter_addresses_by_end_date()
    {
        $this->admin->grantPermission('addresses-list');

        $this->createAddress();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->assertSee($this->addressAddress);
        });

        $this->deleteAddress();
    }

    /** @test */
    public function an_admin_can_clear_address_filters()
    {
        $this->admin->grantPermission('cities-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses?search=something&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_an_address_when_creating_an_address()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-add');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->select2('#country_id-input', $this->countryName)
                ->select2('#state_id-input', $this->stateName)
                ->select2('#city_id-input', $this->cityName)
                ->press('Save')
                ->waitForText('The address field is required')
                ->assertSee('The address field is required');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_an_address_when_updating_an_address()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();
        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickEditButton($this->addressAddress)
                ->type('#address-input', '')
                ->select2('#country_id-input', $this->countryName)
                ->select2('#state_id-input', $this->stateName)
                ->select2('#city_id-input', $this->cityName)
                ->press('Save')
                ->waitForText('The address field is required')
                ->assertSee('The address field is required');
        });

        $this->deleteAddress();
        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /**
     * @return void
     */
    protected function createAddress()
    {
        $this->addressModel = $this->user->addresses()->create([
            'country_id' => optional($this->countryModel)->id ?: null,
            'state_id' => optional($this->stateModel)->id ?: null,
            'city_id' => optional($this->cityModel)->id ?: null,
            'address' => $this->addressAddress,
        ]);
    }

    /**
     * @return void
     */
    protected function createCountry()
    {
        $this->countryModel = Country::create([
            'name' => $this->countryName,
            'code' => $this->countryCode,
        ]);
    }

    /**
     * @return void
     */
    protected function createState()
    {
        $this->stateModel = State::create([
            'country_id' => $this->countryModel->id,
            'name' => $this->stateName,
            'code' => $this->stateCode,
        ]);
    }

    /**
     * @return void
     */
    protected function createCity()
    {
        $this->cityModel = City::create([
            'country_id' => $this->countryModel->id,
            'state_id' => $this->stateModel->id,
            'name' => $this->cityName,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteAddress()
    {
        Address::whereAddress($this->addressAddress)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteAddressModified()
    {
        Address::whereAddress($this->addressAddressModified)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteCountry()
    {
        Country::whereName($this->countryName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteState()
    {
        State::whereName($this->stateName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteCity()
    {
        City::whereName($this->cityName)->first()->delete();
    }
}
