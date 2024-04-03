<?php

namespace App\Http\Controllers;

use App\Models\AdminBusStation;
use App\Models\BusStation;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\UserBusStation;


class BusStationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $userBusStations = UserBusStation::with('busStation')->where('user_id', $userId)->get();
        return view('bus_stations.index', compact('userBusStations'));
    }

    public function create()
    {
        return view('bus_stations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
        ]);
        $userId = Auth::id();
        $busStation = BusStation::create([
            'name' => $request->name,
            'address' => $request->address,
        ]);
        UserBusStation::create([
            'user_id' => $userId,
            'bus_station_id' => $busStation->id,
        ]);
        return redirect()->route('bus_stations.index')->with('message', 'Berhasil menambah data');
    }

    public function detail($id)
    {
        $userId = Auth::id();
        $busStation = BusStation::findOrFail($id);
        $adminBusStations = AdminBusStation::where('bus_station_id', $busStation->id)->get();
        $selectedAdmins = $adminBusStations->pluck('user_id')->toArray();
        $admins = User::role('Admin')
            ->whereHas('adminBusStations', function ($query) use ($busStation) {
                $query->where('bus_station_id', $busStation->id);
            })
            ->orWhereDoesntHave('adminBusStations')
            ->where('id_upt', $userId) // Menambahkan kondisi di sini
            ->get();
        return view('bus_stations.detail', compact('busStation', 'admins', 'selectedAdmins'));
    }
    public function edit($id)
    {
        $busStation = BusStation::findOrFail($id);
        return view('bus_stations.edit', compact('busStation'));
    }

    public function update(Request $request, $id)
    {
        $busStation = BusStation::findOrFail($id);
        $request->validate([
            // Tentukan aturan validasi di sini sesuai kebutuhan Anda
        ]);

        $previousAdmins = $busStation->admins()->pluck('user_id')->toArray();
        if ($request->has('id_admin')) {
            foreach ($request->id_admin as $adminId) {
                AdminBusStation::updateOrCreate(
                    ['user_id' => $adminId, 'bus_station_id' => $busStation->id],
                    ['user_id' => $adminId, 'bus_station_id' => $busStation->id]
                );
            }
        }

        // Hapus admin yang dihapus dari select
        $removedAdmins = array_diff($previousAdmins, (array)$request->id_admin);
        AdminBusStation::whereIn('user_id', $removedAdmins)->where('bus_station_id', $busStation->id)->delete();

        return redirect()->route('bus_stations.index')->with('success', 'Admin berhasil ditambahkan ke stasiun bus.');
    }

    public function destroyMulti(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'ids' => 'required|array', // Pastikan ids adalah array
            'ids.*' => 'exists:bus_stations,id', // Pastikan setiap id ada dalam basis data Anda
        ]);

        // Lakukan penghapusan data berdasarkan ID yang diterima
        BusStation::whereIn('id', $request->ids)->delete();

        // Redirect ke halaman sebelumnya atau halaman lain yang sesuai
        return redirect()->route('bus_stations.index')->with('message', 'Berhasil menghapus data');
    }
}
