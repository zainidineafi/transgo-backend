<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Superadmin',
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'), // Anda dapat menggunakan bcrypt() untuk mengenkripsi password
                'address' => 'Superadmin Address',
                'gender' => 'male', // Atau 'female', sesuai dengan pilihan enum
                'phone_number' => '1234567890',
                'type' => 0, // Superadmin
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'address' => 'Admin Address',
                'gender' => 'female',
                'phone_number' => '0987654321',
                'type' => 1, // Admin
            ],
            [
                'name' => 'Kondektur/Sopir',
                'email' => 'driver@example.com',
                'password' => bcrypt('password'),
                'address' => 'Driver Address',
                'gender' => 'male',
                'phone_number' => '5432167890',
                'type' => 2, // Kondektur/Sopir
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'),
                'address' => 'User Address',
                'gender' => 'female',
                'phone_number' => '0987654321',
                'type' => 3, // User
            ],
        ];

        // Insert data into the users table
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
