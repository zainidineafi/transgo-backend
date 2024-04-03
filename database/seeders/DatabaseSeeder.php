<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\RolesTableSeeder as SeedersRolesTableSeeder;
use Illuminate\Database\Seeder;
use RolesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            CreateUsersSeeder::class,
            AssignPermissionsSeeder::class,
        ]);
    }
}
