<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class ApiBusConductorController extends Controller
{
    public function registerBusConductor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required|string',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string',
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
        ]);

        // Assign role 'Bus_Conductor' to the new user
        $user->assignRole('Bus_Conductor');

        $token = $user->createToken('ApiBusConductor')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function loginBusConductor(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user has the 'Bus_Conductor' role
            if (!$user->hasRole('Bus_Conductor')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $token = $user->createToken('ApiBusConductor')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function registerDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required|string',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string',
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
        ]);

        // Assign role 'Driver' to the new user
        $user->assignRole('Driver');

        $token = $user->createToken('ApiDriver')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function loginDriver(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user has the 'Driver' role
            if (!$user->hasRole('Driver')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $token = $user->createToken('ApiDriver')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();
        $user->address = $request->address;
        $user->save();

        return response()->json(['message' => 'Address updated successfully'], 200);
    }

    public function updatePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();
        $user->phone_number = $request->phone_number;
        $user->save();

        return response()->json(['message' => 'Phone number updated successfully'], 200);
    }

    public function updateImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

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

    public function getBusConductorByName(Request $request)
    {
        $name = $request->input('name');
        $users = User::where('name', 'LIKE', "%$name%")->role('Bus_Conductor')->get();

        return response()->json(['BusConductors' => $users], 200);
    }

    public function getBusConductorById($id)
    {
        $user = User::role('Bus_Conductor')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['BusConductor' => $user], 200);
    }

    public function getDriverByName(Request $request)
    {
        $name = $request->input('name');
        $users = User::where('name', 'LIKE', "%$name%")->role('Driver')->get();

        return response()->json(['Drivers' => $users], 200);
    }

    public function getDriverById($id)
    {
        $user = User::role('Driver')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['Driver' => $user], 200);
    }

    public function BusConductors()
    {
        $BusConductors = User::role('Bus_Conductor')->get();
        return response()->json(['BusConductors' => $BusConductors], 200);
    }

    public function Driver()
    {
        $BusConductors = User::role('Driver')->get();
        return response()->json(['BusConductors' => $BusConductors], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }
}
