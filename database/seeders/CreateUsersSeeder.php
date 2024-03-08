<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'address' => 'Address ' . $i,
                'gender' => $i % 2 == 0 ? 'male' : 'female',
                'phone_number' => '123456789' . $i,
                'images' => 'default.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
