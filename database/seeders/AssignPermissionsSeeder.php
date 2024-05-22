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
        $root = User::find(1);

        // Temukan peran 'Root' jika sudah ada
        $rootRole = Role::where('name', 'Root')->first();

        // rootROle peran 'Root' ditemukan sebelum menetapkannya
        if ($rootRole) {
            // Menetapkan peran 'Root' kepada pengguna dengan ID 1
            $root->assignRole($rootRole);
        } else {
            // Handle jika peran 'Root' tidak ditemukan
            // Misalnya, Anda dapat mencetak pesan kesalahan atau melakukan tindakan lainnya.
        }
    }
}
