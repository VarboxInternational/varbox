<?php

namespace Varbox\Tests\Browser\Seeders;

use Varbox\Contracts\RoleModelContract;

class RolesSeeder
{
    /**
     * Mapping structure of admin roles.
     *
     * @var array
     */
    protected static $roles = [
        'Admin' => [
            'name' => 'Admin',
            'guard' => 'admin',
        ],
        'Super' => [
            'name' => 'Super',
            'guard' => 'admin',
        ],
    ];

    /**
     * @return void
     */
    public static function seed()
    {
        $role = app(RoleModelContract::class);

        foreach (self::$roles as $label => $data) {
            if ($role->where('name', $data['name'])->count() == 0) {
                $role->doNotLogActivity()->create($data);
            }
        }
    }
}
