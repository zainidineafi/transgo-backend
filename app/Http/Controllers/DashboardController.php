<?php

namespace App\Http\Controllers;

use App\Models\Buss;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $uptId = $user->hasRole('Upt') ? $user->id : ($user->hasRole('Admin') ? $user->id_upt : null);

        $roles = $user->getRoleNames();
        $permissions = $user->getPermissionNames();
        $showDashboard = !$user->hasRole('Root');

        // Mendapatkan tanggal sekarang dan 7 hari yang lalu
        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(6); // Subtract 6 days to include today as the 7th day

        // Mengambil data pemesanan dari 7 hari terakhir
        $userRegistrations = DB::table('reservations')
            ->select(DB::raw('DATE(date_departure) as date'), DB::raw('COUNT(*) as reservations'))
            ->whereBetween('date_departure', [$sevenDaysAgo->format('Y-m-d'), $now->format('Y-m-d')])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dates = $userRegistrations->pluck('date');
        $reservationsCount = $userRegistrations->pluck('reservations');

        //dd($sevenDaysAgo);

        $totalAdmins = User::role('Admin')->count();
        $totalBusStations = DB::table('bus_stations')->count();
        $totalBusses = DB::table('busses')->count();

        $status = $request->input('status');
        $query = DB::table('busses')
            ->leftJoin('driver_conductor_bus', 'busses.id', '=', 'driver_conductor_bus.bus_id')
            ->leftJoin('users as drivers', 'driver_conductor_bus.driver_id', '=', 'drivers.id')
            ->leftJoin('users as conductors', 'driver_conductor_bus.bus_conductor_id', '=', 'conductors.id')
            ->select(
                'busses.*',
                'drivers.name as driver_name',
                'conductors.name as conductor_name'
            )
            ->when($uptId, function ($query, $uptId) {
                return $query->where('busses.id_upt', $uptId);
            });

        if ($status) {
            $query->where('busses.status', $status);
        }

        $busses = $query->orderBy('busses.id', 'asc')->get();

        if ($request->ajax()) {
            return view('partials.bus_table_body', compact('busses'))->render();
        }

        return view('dashboard', compact(
            'user',
            'roles',
            'permissions',
            'showDashboard',
            'dates',
            'reservationsCount',
            'totalAdmins',
            'totalBusStations',
            'totalBusses',
            'busses'
        ));
    }
}
