<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UptController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $upts = User::role('Upt')->get();

        return view('upts.index', compact('upts'));
    }

    public function search(Request $request)
    {
        // Ambil nilai pencarian dari input form
        $searchTerm = $request->input('search');

        // Lakukan pencarian UPT berdasarkan nama pada pengguna dengan peran 'Upt'
        $upts = User::role('Upt')
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->get();
    }

    // Menampilkan form untuk membuat pengguna baru
    public function create()
    {
        $roles = Role::all();
        return view('upts.create', ['roles' => $roles]);
    }

    // Menyimpan pengguna baru ke database
    public function store(Request $request)
    {
        // Dapatkan nilai 'images' dari input
        $images = $request->input('images');

        // Periksa apakah 'images' tidak ada atau kosong
        if (!$images) {
            $images = 'default.jpg';
        }
        // Validasi data yang diterima dari form
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'address' => 'required',
            'gender' => 'required',
            'phone_number' => 'required',

            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        // Simpan data pengguna baru ke dalam database
        $upt = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'images' => $images,
            'created_at' => Carbon::now(),

        ]);

        // Beri peran 'Root' kepada pengguna baru
        $role = Role::findByName('Upt');
        $upt->assignRole($role);

        // Redirect ke halaman daftar pengguna
        return redirect()->route('upts.index')->with('success', 'User created successfully.');
    }

    // Menampilkan form untuk mengedit pengguna
    public function edit($id)
    {
        $upt = User::findOrFail($id);
        return view('upts.edit', compact('user'));
    }

    // Menyimpan perubahan pada pengguna ke database
    public function update(Request $request, $id)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        // Temukan pengguna yang akan diperbarui dan simpan perubahan
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        // Update atribut lain jika diperlukan
        $user->save();

        // Redirect ke halaman daftar pengguna
        return redirect()->route('upts.index')->with('success', 'User updated successfully.');
    }

    // Menghapus pengguna dari database
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        // Redirect ke halaman daftar pengguna
        return redirect()->route('upts.index')->with('success', 'User deleted successfully.');
    }
}
