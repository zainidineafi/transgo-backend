<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class BusConductorController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        // Mendapatkan ID pengguna yang sedang masuk
        $userId = Auth::id();
        $bus_conductors = User::role('Bus_Conductor')->where('id_upt', $userId)->paginate(10);
        return view('bus_conductors.index', compact('bus_conductors'));
    }


    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $bus_conductors = User::role('Bus_Conductor')
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('address', 'like', '%' . $searchTerm . '%');
            })
            ->paginate(10);

        return view('bus_conductors.index', compact('bus_conductors'));
    }

    // Menampilkan form untuk membuat pengguna baru
    public function create()
    {
        $roles = Role::all();
        return view('bus_conductors.create', ['roles' => $roles]);
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

        $userId = Auth::id();

        // Simpan data pengguna baru ke dalam database
        $bus_conductor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'images' => $imageName,
            'id_upt' => $userId, // Menambahkan id_upt dari pengguna yang sedang masuk
            'created_at' => Carbon::now(),
        ]);

        // Cetak variabel $admin
        //dd($admin);

        // Beri peran 'Root' kepada pengguna baru
        $role = Role::findByName('Bus_Conductor');
        $bus_conductor->assignRole($role);

        // Redirect ke halaman daftar pengguna
        return redirect()->route('bus_conductors.index')->with('message', 'Berhasil menambah data');
    }


    // Menampilkan form untuk mengedit pengguna
    public function edit($id)
    {
        $bus_conductor = User::findOrFail($id);
        $roles = Role::all();
        $genders = [
            'male' => 'Laki-Laki',
            'female' => 'Perempuan'
        ];
        return view('bus_conductors.edit', ['bus_conductor' => $bus_conductor, 'roles' => $roles, 'genders' => $genders]);
    }


    public function detail($id)
    {
        $bus_conductor = User::findOrFail($id);
        $roles = Role::all();
        $genders = [
            'male' => 'Laki-Laki',
            'female' => 'Perempuan'
        ];
        return view('bus_conductors.detail', ['bus_conductor' => $bus_conductor, 'roles' => $roles, 'genders' => $genders]);
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
        $bus_conductor = User::findOrFail($id);

        // Periksa apakah ada file gambar yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            Storage::delete($bus_conductor->images);

            // Simpan file gambar baru ke dalam penyimpanan yang diinginkan
            $imageName = $request->file('image')->store('avatars');

            // Update nama file gambar dalam database
            $bus_conductor->images = $imageName;
        }

        // Update data pengguna
        $bus_conductor->name = $request->name;
        $bus_conductor->email = $request->email;
        if ($request->filled('password')) {
            $bus_conductor->password = Hash::make($request->password);
        }
        $bus_conductor->address = $request->address;
        $bus_conductor->gender = $request->gender;
        $bus_conductor->phone_number = $request->phone_number;
        $bus_conductor->save();

        // Redirect ke halaman daftar pengguna dengan pesan sukses
        return redirect()->route('bus_conductors.index')->with('message', 'Berhasil mengubah data.');
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
        return redirect()->route('bus_conductor.index')->with('message', 'Berhasil menghapus data');
    }
}
