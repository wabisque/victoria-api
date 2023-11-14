<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    static $seeds = [
        [
            'name' => 'Super Admin',
            'email' => 'sadmin@victoria.com',
            'phone_number' => '0200000000',
            'password' => '$2y$10$PxSNps6qVhECeZHfR2D1UO18H8S2DStcCHBjcJgDl/TAcDsWPKYg2' // secret2022
        ]
    ];

    public function run(): void
    {
        $role = Role::where('name', 'Administrator')->first();

        foreach(self::$seeds as $seed) {
            User::create(
                $seed + [
                    'role_id' => $role->id
                ]
            );
        }
    }
}
