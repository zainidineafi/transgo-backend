<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        // Mengambil ID pengguna yang sedang login
        $userId = Auth::id();

        // Mengambil data pengguna berdasarkan ID
        $userProfile = User::findOrFail($userId);
        $genders = [
            'male' => 'Laki-Laki',
            'female' => 'Perempuan'
        ];
        $roles = Role::all();

        // Kirim data profil pengguna ke view
        return view('profile', compact('userProfile', 'genders', 'roles'));
    }

    public function updateImage(Request $request)
    {
        // Validasi permintaan
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Sesuaikan dengan kebutuhan
        ]);

        // Mengambil ID pengguna yang sedang login
        $userId = Auth::id();

        // Mengambil data pengguna berdasarkan ID
        $userProfile = User::findOrFail($userId);

        // Simpan gambar yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($userProfile->images) {
                Storage::delete($userProfile->images);
            }

            // Simpan gambar di storage dengan nama acak dalam direktori 'avatars'
            $imageName = $request->file('image')->store('avatars');

            // Update kolom gambar pada model pengguna
            $userProfile->images = $imageName;
            $userProfile->save();
        }

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->route('profile')->with('success', 'Gambar profil berhasil diperbarui.');
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


        // Mengambil ID pengguna yang sedang login
        $userId = Auth::id();

        // Mengambil data pengguna berdasarkan ID
        $userProfile = User::findOrFail($userId);


        // Update data pengguna
        $userProfile->name = $request->name;
        $userProfile->email = $request->email;
        if ($request->filled('password')) {
            $userProfile->password = Hash::make($request->password);
            Auth::logout();
            return redirect()->route('login');
        }
        $userProfile->address = $request->address;
        $userProfile->gender = $request->gender;
        $userProfile->phone_number = $request->phone_number;
        $userProfile->save();

        // Redirect ke halaman daftar pengguna dengan pesan sukses
        return redirect()->route('profile')->with('message', 'Berhasil mengubah data.');
    }
}
