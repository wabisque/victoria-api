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
            'user' => [
                'name' => 'Super Admin',
                'email' => 'sadmin@victoria.com',
                'phone_number' => '0200000000',
                'password' => '$2y$10$PxSNps6qVhECeZHfR2D1UO18H8S2DStcCHBjcJgDl/TAcDsWPKYg2' // secret2022
            ],
            'role' => 'Administrator' 
        ],
        [
            'user' => [
                'name' => 'Aspirant',
                'email' => 'asp@victoria.com',
                'phone_number' => '0200000001',
                'password' => '$2y$10$PxSNps6qVhECeZHfR2D1UO18H8S2DStcCHBjcJgDl/TAcDsWPKYg2' // secret2022
            ],
            'role' => 'Aspirant'
        ],
        [
            'user' => [
                'name' => 'Follower',
                'email' => 'flw@victoria.com',
                'phone_number' => '0200000002',
                'password' => '$2y$10$PxSNps6qVhECeZHfR2D1UO18H8S2DStcCHBjcJgDl/TAcDsWPKYg2' // secret2022
            ],
            'role' => 'Follower'
        ]
    ];

    public function run(): void
    {
        foreach(self::$seeds as $seed) {
            User::create(
                $seed['user'] + [
                    'role_id' => Role::where('name', $seed['role'])->first()->id
                ]
            );
        }
    }
}
