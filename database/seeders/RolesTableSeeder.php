<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    static $seeds = [
        [
            'name' => 'Administrator'
        ],
        [
            'name' => 'Aspirant'
        ],
        [
            'name' => 'Follower'
        ]
    ];

    public function run(): void
    {
        foreach(self::$seeds as $seed) {
            Role::create($seed);
        }
    }
}
