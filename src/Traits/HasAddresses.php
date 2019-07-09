<?php

namespace Varbox\Traits;

use Varbox\Models\Address;

trait HasAddresses
{
    /**
     * User has many addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        $address = config('varbox.bindings.models.address_model', Address::class);

        return $this->hasMany($address, 'user_id');
    }
}
