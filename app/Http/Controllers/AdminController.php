<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $admins = User::role('Admin')->paginate(10); // Menentukan 10 item per halaman

        return view('admins.index', compact('admins'));
    }


    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $admins = User::role('Admin')
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('address', 'like', '%' . $searchTerm . '%');
            })
            ->paginate(10);

        return view('admins.index', compact('admins'));
    }

    // Menampilkan form untuk membuat pengguna baru
    public function create()
    {
        $roles = Role::all();
        return view('admins.create', ['roles' => $roles]);
    }

    // Menyimpan pengguna baru ke database
    public function store(Request $request)
    {
        $image = $request->file('image');
        if ($image) {
            $imageName = $image->store('avatars');
        } else {
            $imageName = 'avatars/default.jpg';
        }
        // Validasi data yang diterima dari form
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'address' => 'required',
            'gender' => 'required',
            'phone_number' => 'required|unique:users',
        ]);


        // Simpan data pengguna baru ke dalam database
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'images' => $imageName,
            'created_at' => Carbon::now(),

        ]);

        // Beri peran 'Root' kepada pengguna baru
        $role = Role::findByName('Admin');
        $admin->assignRole($role);

        // Redirect ke halaman daftar pengguna
        return redirect()->route('admins.index')->with('message', 'Berhasil menambah data');
    }

    // Menampilkan form untuk mengedit pengguna
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        $roles = Role::all();
        $genders = [
            'male' => 'Laki-Laki',
            'female' => 'Perempuan'
        ];
        return view('admins.edit', ['admin' => $admin, 'roles' => $roles, 'genders' => $genders]);
    }


    public function detail($id)
    {
        $admin = User::findOrFail($id);
        $roles = Role::all();
        $genders = [
            'male' => 'Laki-Laki',
            'female' => 'Perempuan'
        ];
        return view('admins.detail', ['admin' => $admin, 'roles' => $roles, 'genders' => $genders]);
    }



    public function update(Request $request, $id)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'address' => 'required',
            'gender' => 'required',
            'phone_number' => 'required|unique:users,phone_number,' . $id,
        ]);


        // Ambil data pengguna yang akan diupdate
        $admin = User::findOrFail($id);

        // Periksa apakah ada file gambar yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            Storage::delete($admin->images);

            // Simpan file gambar baru ke dalam penyimpanan yang diinginkan
            $imageName = $request->file('image')->store('avatars');

            // Update nama file gambar dalam database
            $admin->images = $imageName;
        }

        // Update data pengguna
        $admin->name = $request->name;
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->address = $request->address;
        $admin->gender = $request->gender;
        $admin->phone_number = $request->phone_number;
        $admin->save();

        // Redirect ke halaman daftar pengguna dengan pesan sukses
        return redirect()->route('admins.index')->with('message', 'Berhasil mengubah data.');
    }



    public function destroyMulti(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'ids' => 'required|array', // Pastikan ids adalah array
            'ids.*' => 'exists:users,id', // Pastikan setiap id ada dalam basis data Anda
        ]);

        // Lakukan penghapusan data berdasarkan ID yang diterima
        User::whereIn('id', $request->ids)->delete();

        // Redirect ke halaman sebelumnya atau halaman lain yang sesuai
        return redirect()->route('admins.index')->with('message', 'Berhasil menghapus data');
    }
}
