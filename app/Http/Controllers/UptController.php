<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UptController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $upts = User::role('Upt')->paginate(10); // Menentukan 10 item per halaman

        return view('upts.index', compact('upts'));
    }


    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $upts = User::role('Upt')
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('address', 'like', '%' . $searchTerm . '%');
            })
            ->paginate(10);

        return view('upts.index', compact('upts'));
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
            'phone_number' => 'required',
        ]);


        // Simpan data pengguna baru ke dalam database
        $upt = User::create([
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
        $role = Role::findByName('Upt');
        $upt->assignRole($role);

        // Redirect ke halaman daftar pengguna
        return redirect()->route('upts.index')->with('success', 'User created successfully.');
    }

    // Menampilkan form untuk mengedit pengguna
    public function edit($id)
    {
        $upt = User::findOrFail($id);
        $roles = Role::all();
        $genders = [
            'male' => 'Laki-Laki',
            'female' => 'Perempuan'
        ];
        return view('upts.edit', ['upt' => $upt, 'roles' => $roles, 'genders' => $genders]);
    }


    public function detail($id)
    {
        $upt = User::findOrFail($id);
        $roles = Role::all();
        $genders = [
            'male' => 'Laki-Laki',
            'female' => 'Perempuan'
        ];
        return view('upts.detail', ['upt' => $upt, 'roles' => $roles, 'genders' => $genders]);
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
            'phone_number' => 'required',
        ]);

        // Ambil data pengguna yang akan diupdate
        $upt = User::findOrFail($id);

        // Periksa apakah ada file gambar yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            Storage::delete($upt->images);

            // Simpan file gambar baru ke dalam penyimpanan yang diinginkan
            $imageName = $request->file('image')->store('avatars');

            // Update nama file gambar dalam database
            $upt->images = $imageName;
        }

        // Update data pengguna
        $upt->name = $request->name;
        $upt->email = $request->email;
        if ($request->filled('password')) {
            $upt->password = Hash::make($request->password);
        }
        $upt->address = $request->address;
        $upt->gender = $request->gender;
        $upt->phone_number = $request->phone_number;
        $upt->save();

        // Redirect ke halaman daftar pengguna dengan pesan sukses
        return redirect()->route('upts.index')->with('success', 'User updated successfully.');
    }


    // Menghapus pengguna dari database
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        // Redirect ke halaman daftar pengguna
        return redirect()->route('upts.index')->with('success', 'User deleted successfully.');
    }

    public function multiDelete(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'ids' => 'required|array', // Pastikan ids adalah array
            'ids.*' => 'exists:users,id', // Pastikan setiap id ada dalam basis data Anda
        ]);

        // Lakukan penghapusan data berdasarkan ID yang diterima
        User::whereIn('id', $request->ids)->delete();

        // Kirim respons sukses kembali ke klien
        return response()->json(['success' => 'Records deleted successfully']);
    }
}
