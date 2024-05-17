<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ApiBusConductorController extends Controller
{
    public function BusConductors()
    {
        $BusConductors = User::role('Bus_Conductor')->get();
        return response()->json(['BusConductors' => $BusConductors]);
    }

    public function registerBusConductor(Request $request)
    {
        // Validasi data yang diterima dari permintaan
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'phone_number' => 'required|string|max:20',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $images = 'default.jpg'; // Gunakan nilai default untuk 'images'

        // Buat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'images' => $images,
        ]);

        // Beri peran kepada pengguna baru
        $role = Role::where('name', 'Bus_Conductor')->first();
        if ($role) {
            $user->assignRole($role);
        }

        // Return response with success message
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function loginBusConductor(Request $request)
    {
        // Validasi data yang diterima dari permintaan
        $credentials = $request->only('email', 'password');

        // Coba melakukan proses login menggunakan Passport
        if (Auth::attempt($credentials)) {
            // Jika proses login berhasil, dapatkan pengguna yang diautentikasi
            $user = Auth::user();
            // Buat akses token untuk pengguna
            $accessToken = $user->createToken('authToken')->accessToken;
            // Kembalikan respons dengan akses token
            return response()->json(['access_token' => $accessToken], 200);
        } else {
            // Jika proses login gagal, kembalikan respons dengan pesan kesalahan
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function registerDriver(Request $request)
    {
        // Validasi data yang diterima dari permintaan
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'phone_number' => 'required|string|max:20',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $images = 'default.jpg'; // Gunakan nilai default untuk 'images'

        // Buat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'images' => $images,
        ]);

        // Beri peran kepada pengguna baru
        $role = Role::where('name', 'Driver')->first();
        if ($role) {
            $user->assignRole($role);
        }

        // Return response with success message
        return response()->json(['message' => 'User registered successfully'], 201);
    }
}
