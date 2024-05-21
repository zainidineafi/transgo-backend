<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Models\ApiToken;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiPassengerController extends Controller
{
    // Mendaftarkan Passenger baru
    public function registerPassengers(Request $request)
    {
        // Validasi data yang diterima dari permintaan
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'phone_number' => 'required|string|max:20',
        ]);

        // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Default image
        $images = 'default.jpg';

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

        // Beri peran 'Passenger' kepada pengguna baru
        $role = Role::where('name', 'Passenger')->first();
        if ($role) {
            $user->assignRole($role);
        }

        // Buat token pendek
        $token = Str::random(40); // Panjang token yang lebih aman

        // Simpan token di database
        ApiToken::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        // Kembalikan respons dengan token pendek dan data pengguna
        return response()->json([
            'access_token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'address' => $user->address,
                'gender' => $user->gender,
                'phone' => $user->phone_number,
                'updated_at' => $user->updated_at->toISOString(),
                'created_at' => $user->created_at->toISOString(),
                'id' => $user->id
            ]
        ], 201);
    }

    // Login Passenger menggunakan token tetap
    public function loginPassengers(Request $request)
    {
        // Validasi data yang diterima dari permintaan
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Coba melakukan proses login
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Jika proses login berhasil, dapatkan pengguna yang diautentikasi
            $user = Auth::user();

            // Ambil token yang ada atau buat token baru
            $token = $user->apiTokens()->firstOrCreate(
                ['user_id' => $user->id],
                ['token' => Str::random(40)]
            );

            // Kembalikan respons dengan token tetap dan data pengguna
            return response()->json([
                'access_token' => $token->token,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'address' => $user->address,
                    'gender' => $user->gender,
                    'phone' => $user->phone_number,
                    'updated_at' => $user->updated_at->toISOString(),
                    'created_at' => $user->created_at->toISOString(),
                    'id' => $user->id
                ]
            ], 200);
        } else {
            // Jika proses login gagal, kembalikan respons dengan pesan kesalahan
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    // Middleware untuk memastikan token di setiap permintaan
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['registerPassengers', 'loginPassengers']]);
    }

    // Mengambil data Passenger yang sedang login
    public function passengers(Request $request)
    {
        // Mengambil semua data pengguna dengan peran 'Passenger'
        $passengers = User::role('Passenger')->get();
        return response()->json(['passengers' => $passengers], 200);
    }

// Memperbarui password Passenger

public function updatePassengers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }
    // Memperbarui nomor telepon Passenger
    public function updatePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->phone_number = $request->phone_number;
        $user->save();

        return response()->json(['message' => 'Phone number updated successfully'], 200);
    }

    // Memperbarui gambar Passenger
    public function updateImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $user->images = $name;
        }

        $user->save();

        return response()->json(['message' => 'Image updated successfully'], 200);
    }

    // Mendapatkan data Passenger berdasarkan nama
    public function getPassengerByName(Request $request, $name)
    {
        // Temukan pengguna berdasarkan nama
        $user = User::where('name', 'LIKE', "%$name%")->role('Passenger')->get();

        // Jika pengguna tidak ditemukan
        if ($user->isEmpty()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Kembalikan data pengguna dalam format JSON
        return response()->json(['passenger' => $user], 200);
    }

    // Mendapatkan data Passenger berdasarkan ID
    public function getPassengerById($id)
    {
        // Temukan pengguna berdasarkan ID
        $user = User::role('Passenger')->find($id);

        // Jika pengguna tidak ditemukan
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Kembalikan data pengguna dalam format JSON
        return response()->json(['passenger' => $user], 200);
    }

    public function searchSchedulesByDestination(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'destination' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedules = Schedule::where('destination', $request->destination)->get();

        return response()->json(['schedules' => $schedules], 200);
    }

    // Pencarian jadwal bus berdasarkan waktu mulai
    public function searchSchedulesByTimeStart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_start' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedules = Schedule::where('time_start', $request->time_start)->get();

        return response()->json(['schedules' => $schedules], 200);
    }

    // Pencarian jadwal bus berdasarkan ID stasiun asal
    public function searchSchedulesByFromStationId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_station_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $schedules = Schedule::where('from_station_id', $request->from_station_id)->get();

        return response()->json(['schedules' => $schedules], 200);
    }
}
