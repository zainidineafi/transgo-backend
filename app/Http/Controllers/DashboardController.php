<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // Tentukan $uptId berdasarkan peran pengguna
        $uptId = $user->hasRole('Upt') ? $user->id : ($user->hasRole('Admin') ? $user->id_upt : null);

        // Cek nilai $uptId untuk keperluan debugging
        // dd($uptId);


        $roles = $user->getRoleNames();

        $showDashboard = !$user->hasRole('Root');

        return view('dashboard', compact('user', 'roles', 'showDashboard'));
    }
}