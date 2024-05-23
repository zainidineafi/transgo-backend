<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ApiToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiBusConductorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['registerBusConductor', 'loginBusConductor', 'registerDriver', 'loginDriver']]);
    }

    public function BusConductors()
    {
        $BusConductors = User::role('Bus_Conductor')->get();
        return response()->json(['BusConductors' => $BusConductors]);
    }

    public function registerBusConductor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $images = 'default.jpg';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'images' => $images,
        ]);

        $role = Role::where('name', 'Bus_Conductor')->first();
        if ($role) {
            $user->assignRole($role);
        }

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function loginBusConductor(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            ApiToken::where('user_id', $user->id)->delete();
            $token = Str::random(40);
            ApiToken::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);

            return response()->json(['access_token' => $token, 'user' => $user], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function registerDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $images = 'default.jpg';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'images' => $images,
        ]);

        $role = Role::where('name', 'Driver')->first();
        if ($role) {
            $user->assignRole($role);
        }

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function loginDriver(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            ApiToken::where('user_id', $user->id)->delete();
            $token = Str::random(40);
            ApiToken::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);

            return response()->json(['access_token' => $token, 'user' => $user], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
            return response()->json(['errors' => $validator->errors()], 422);
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
            return response()->json(['errors' => $validator->errors()], 422);
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
            return response()->json(['errors' => $validator->errors()], 422);
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

    public function getBusConductorByName(Request $request, $name)
    {
        $user = User::where('name', 'LIKE', "%$name%")->role('Bus_Conductor')->get();

        if ($user->isEmpty()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['BusConductor' => $user], 200);
    }

    public function getBusConductorById($id)
    {
        $user = User::role('Bus_Conductor')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['BusConductor' => $user], 200);
    }

    public function getDriverByName(Request $request, $name)
    {
        $user = User::where('name', 'LIKE', "%$name%")->role('Driver')->get();

        if ($user->isEmpty()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['Driver' => $user], 200);
    }

    public function getDriverById($id)
    {
        $user = User::role('Driver')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['Driver' => $user], 200);
    }
}
