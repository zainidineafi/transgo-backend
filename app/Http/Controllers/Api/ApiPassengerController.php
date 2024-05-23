<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Schedule;
use Illuminate\Support\Facades\Validator;

class ApiPassengerController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required|string',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string',
            'images' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'images' => $request->images,
        ]);

        $token = $user->createToken('ApiPassenger')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('ApiPassenger')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function updatePassword(Request $request)
{

    // Validasi data yang diterima
    $validatedData = $request->validate([
        'password' => 'required|min:6',
    ]);

    // Dapatkan pengguna yang sedang masuk
    $user = auth("api")->user();

    if ($user) {
        // Setel password baru
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    } else {
        return response()->json(['message' => 'User not found'], 404);
    }
}
public function searchSchedulesByFromStationAddress(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'from_station_address' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    // Cari jadwal berdasarkan alamat stasiun asal
    $fromStationAddress = $request->input('from_station_address');
    $schedules = Schedule::whereHas('fromStation', function ($query) use ($fromStationAddress) {
        $query->where('address', 'like', '%' . $fromStationAddress . '%');
    })->get();

    return response()->json(['schedules' => $schedules], 200);
}



    public function updateAddress(Request $request)
    {
        $user = auth("api")::user();

        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user->address = $request->address;
        $user->save();

        return response()->json(['message' => 'Address updated successfully'], 200);
    }

    public function updatePhone(Request $request)
    {
        $user = auth("api")::user();

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user->phone_number = $request->phone_number;
        $user->save();

        return response()->json(['message' => 'Phone number updated successfully'], 200);
    }

    public function updateImage(Request $request)
    {
        $user = auth("api")::user();

        $validator = Validator::make($request->all(), [
            'images' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user->images = $request->images;
        $user->save();

        return response()->json(['message' => 'Profile image updated successfully'], 200);
    }

    public function getPassengerByName(Request $request)
    {
        $name = $request->input('name');

        $passengers = User::where('name', 'like', '%' . $name . '%')->get();

        return response()->json(['passengers' => $passengers], 200);
    }

    public function getPassengerById($id)
    {
        $passenger = User::find($id);

        if (!$passenger) {
            return response()->json(['error' => 'Passenger not found'], 404);
        }

        return response()->json(['passenger' => $passenger], 200);
    }
    public function schedules()
    {
        // Ambil semua jadwal
        $schedules = Schedule::all();

        return response()->json(['schedules' => $schedules], 200);
    }

    public function searchSchedulesByDestination(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'destination' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Cari jadwal berdasarkan tujuan
        $destination = $request->input('destination');
        $schedules = Schedule::where('destination', $destination)->get();

        return response()->json(['schedules' => $schedules], 200);
    }

    public function searchSchedulesByFromStationName(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'from_station_name' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    // Cari jadwal berdasarkan nama stasiun asal
    $fromStationName = $request->input('from_station_name');
    $schedules = Schedule::whereHas('fromStation', function ($query) use ($fromStationName) {
        $query->where('name', 'like', '%' . $fromStationName . '%');
    })->get();

    return response()->json(['schedules' => $schedules], 200);
}


    public function logout(Request $request)
    {
        $user = auth('api')->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }
}

