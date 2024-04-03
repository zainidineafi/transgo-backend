<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cari atau buat role 'Admin' jika belum ada
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);


        // Loop untuk membuat pengguna baru
        for ($i = 13; $i <= 20; $i++) {
            // Simpan data pengguna baru ke dalam tabel 'users'
            $userId = DB::table('users')->insertGetId([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@gmail.com',
                'password' => Hash::make('password123'),
                'address' => 'Address ' . $i,
                'gender' => $i % 2 == 0 ? 'male' : 'female',
                'phone_number' => '123456789' . $i,
                'images' => 'default.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Assign role 'Admin' ke pengguna baru
            DB::table('model_has_roles')->insert([
                'role_id' => $adminRole->id,
                'model_type' => 'App\Models\User',
                'model_id' => $userId
            ]);
        }
    }
}
