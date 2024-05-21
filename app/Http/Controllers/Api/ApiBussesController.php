<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buss;
use App\Models\DriverConductorBus;
use App\Models\User;
use App\Models\BusStation;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiBussesController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        // Validasi data yang diterima dari permintaan API
        $request->validate([
            'status' => 'required|in:1,2,3,4', // Pastikan status yang diterima valid
            'information' => 'required_if:status,3|string|max:255', // Validasi untuk informasi kendala jika status 3
        ]);

        // Temukan bus berdasarkan ID yang diberikan
        $bus = Buss::findOrFail($id);

        // Periksa apakah pengguna memiliki izin untuk mengubah status bus (misalnya, hanya admin yang diizinkan)
        // Anda dapat menambahkan logika izin sesuai dengan kebutuhan aplikasi Anda

        // Update status bus
        $bus->status = $request->status;

        // Jika status adalah 3 (Kendala), tambahkan informasi tambahan
        if ($request->status == 3) {
            $bus->information = $request->information;
        } else {
            $bus->information = null;
        }

        $bus->save();

        // Berikan respons API yang sesuai
        return response()->json(['message' => 'Status bus berhasil diperbarui'], 200);
    }

    public function DataBusses()
    {
        // Mengambil semua data bus
        $busses = Buss::all();
        return response()->json(['busses' => $busses], 200);
    }

    public function BusStation()
    {
        $busStations = BusStation::all();
        return response()->json(['bus_stations' => $busStations], 200);
    }
}
