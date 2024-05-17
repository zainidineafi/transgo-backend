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

class ApiController extends Controller
{
    public function upts()
    {
        $upts = User::role('Upt')->get();
        return response()->json(['upts' => $upts]);
    }

    public function indexAllUsers()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }
}
