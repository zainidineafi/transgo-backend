<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Temukan pengguna dengan ID 1
        $user = User::find(10);

        // Temukan peran 'Root' jika sudah ada
        $adminRole = Role::where('name', 'Upt')->first();

        // Pastikan peran 'Root' ditemukan sebelum menetapkannya
        if ($adminRole) {
            // Menetapkan peran 'Root' kepada pengguna dengan ID 1
            $user->assignRole($adminRole);

            // Menetapkan izin CRUD Upt kepada peran 'Root'
            $adminRole->givePermissionTo([
                'view dashboard'
            ]);
        } else {
            // Handle jika peran 'Root' tidak ditemukan
            // Misalnya, Anda dapat mencetak pesan kesalahan atau melakukan tindakan lainnya.
        }
    }
}
