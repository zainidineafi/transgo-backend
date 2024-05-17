<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Menggunakan Spatie, Anda dapat langsung mendapatkan peran dan izin pengguna
        $roles = $user->getRoleNames();
        $permissions = $user->getPermissionNames();

        $showDashboard = !$user->hasRole('Root');

        // Ambil data reservasi untuk grafik pemesanan
        $reservations = Reservation::all();

        // Ambil data pendaftaran pengguna per hari
        $userRegistrations = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as registrations'))

            ->whereHas('roles', function ($query) {
                $query->where('name', 'Passenger');
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('dashboard', compact('user', 'roles', 'permissions', 'showDashboard', 'reservations', 'userRegistrations'));
    }
}
