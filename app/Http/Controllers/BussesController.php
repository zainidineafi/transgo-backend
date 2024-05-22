<?php

namespace App\Http\Controllers;

use App\Models\Buss;
use App\Models\DriverConductorBus;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBusStation;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class BussesController extends Controller
{
    public function index()
    {
        // Pastikan pengguna telah diautentikasi
        if (Auth::check()) {
            // Ambil peran pengguna yang masuk
            $user = Auth::user();
            // Periksa apakah pengguna memiliki peran Upt atau Admin
            if ($user->hasRole('Upt') || $user->hasRole('Admin')) {
                // Tentukan ID Upt yang akan digunakan dalam kueri
                $uptId = $user->hasRole('Upt') ? $user->id : ($user->hasRole('Admin') ? $user->id_upt : null);

                $busses = DB::table('busses')
                    ->leftJoin('driver_conductor_bus', 'busses.id', '=', 'driver_conductor_bus.bus_id')
                    ->leftJoin('users as drivers', 'driver_conductor_bus.driver_id', '=', 'drivers.id')
                    ->leftJoin('users as conductors', 'driver_conductor_bus.bus_conductor_id', '=', 'conductors.id')
                    ->select(
                        'busses.*',
                        'drivers.name as driver_name',
                        'conductors.name as conductor_name'
                    )
                    ->where('busses.id_upt', $uptId) // Menambahkan kondisi untuk ID Upt
                    ->orderBy('busses.id', 'asc') // Mengatur urutan berdasarkan ID secara ascending
                    ->paginate(10);

                // Mengembalikan data tersebut ke view
                return view('busses.index', compact('busses'));
            }
        }
    }

    public function filter(Request $request)
    {
        $status = $request->query('status');

        Log::info('Filter status:', ['status' => $status]);

        $query = Buss::leftJoin('driver_conductor_bus', 'busses.id', '=', 'driver_conductor_bus.bus_id')
            ->leftJoin('users as drivers', 'driver_conductor_bus.driver_id', '=', 'drivers.id')
            ->leftJoin('users as conductors', 'driver_conductor_bus.bus_conductor_id', '=', 'conductors.id')
            ->select(
                'busses.*',
                'drivers.name as driver_name',
                'conductors.name as conductor_name'
            );

        if ($status) {
            $query->where('busses.status', $status);
        }

        $busses = $query->get();

        return response()->json(['busses' => $busses]);
    }



    public function search(Request $request)
    {
        // Pastikan pengguna telah diautentikasi
        if (Auth::check()) {
            // Ambil peran pengguna yang masuk
            $user = Auth::user();
            // Periksa apakah pengguna memiliki peran Upt atau Admin
            if ($user->hasRole('Upt') || $user->hasRole('Admin')) {
                // Tentukan ID Upt yang akan digunakan dalam kueri
                $uptId = $user->hasRole('Upt') ? $user->id : ($user->hasRole('Admin') ? $user->id_upt : null);

                $searchTerm = $request->input('search');

                $busses = Buss::where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('license_plate_number', 'like', '%' . $searchTerm . '%');
                })
                    ->where('id_upt', $uptId) // Menambahkan kondisi id_upt
                    ->paginate(10);

                return view('busses.index', compact('busses'));
            }
        }
    }

    public function create()
    {
        $userId = Auth::id();

        // Fetch all admins who have the role 'Admin' and meet certain conditions
        $drivers = User::role('Driver')
            ->whereDoesntHave('driverBus')
            ->where('id_upt', $userId)
            ->get();

        // Fetch all admins who have the role 'Admin' and meet certain conditions
        $bus_conductors = User::role('Bus_Conductor')
            ->whereDoesntHave('ConductorBus')
            ->where('id_upt', $userId)
            ->get();

        // Pass the fetched data to the view
        return view('busses.create', compact('drivers', 'bus_conductors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'license_plate_number' => [
                'required',
                Rule::unique('busses')
            ],
            'chair' => 'required',
            'class' => 'required',
            'status' => 'required',
            'drivers' => 'nullable|array',
            'bus_conductors' => 'nullable|array',
        ]);

        // Menghapus spasi dari nomor plat kendaraan
        $licensePlateNumber = str_replace(' ', '', $request->license_plate_number);

        $image = $request->file('image');

        if ($image) {
            $imageName = $image->store('avatars');
        } else {
            $imageName = 'avatars/default_bus.png';
        }

        $userId = Auth::id();

        $bus = Buss::create([
            'name' => $request->name,
            'license_plate_number' => $licensePlateNumber,
            'chair' => $request->chair,
            'class' => $request->class,
            'status' => $request->status, // Menambahkan status dari formulir
            'information' => $request->status == 4 ? $request->keterangan : null, // Menambahkan keterangan jika status adalah 4 (Terkendala)
            'images' => $imageName,
            'id_upt' => $userId, // Menambahkan id_upt dari pengguna yang sedang masuk
            'created_at' => Carbon::now(),
        ]);

        $bus->save();

        // Menyimpan relasi antara driver dan bus conductor yang dipilih dan bus yang baru dibuat
        if ($request->filled('drivers') && $request->filled('bus_conductors')) {
            foreach ($request->drivers as $driverId) {
                foreach ($request->bus_conductors as $busConductorId) {
                    DriverConductorBus::create([
                        'driver_id' => $driverId,
                        'bus_conductor_id' => $busConductorId,
                        'bus_id' => $bus->id,
                    ]);
                }
            }
        }

        return redirect()->route('busses.index')->with('message', 'Berhasil menambah data');
    }





    public function detail($id)
    {
        $user = Auth::user();
        $userId = $user->hasRole('Upt') ? $user->id : ($user->hasRole('Admin') ? $user->id_upt : null);
        //$userId = Auth::id();
        $bus = Buss::findOrFail($id);
        $driveconduc = DriverConductorBus::where('bus_id', $bus->id)->get();

        //dd($driveconduc);
        $assignedDrivers = $driveconduc->pluck('driver_id')->toArray();
        $assignedBusConductors = $driveconduc->pluck('bus_conductor_id')->toArray();
        //dd($assignedBusConductors);

        $drivers = User::role('Driver')
            ->where(function ($query) use ($bus) {
                $query->whereHas('driverBus', function ($query) use ($bus) {
                    $query->where('bus_id', $bus->id);
                })->orWhereDoesntHave('driverBus');
            })
            ->where('id_upt', $userId)
            ->get();

        $bus_conductors = User::role('Bus_Conductor')
            ->where(function ($query) use ($bus) {
                $query->whereHas('ConductorBus', function ($query) use ($bus) {
                    $query->where('bus_id', $bus->id);
                })->orWhereDoesntHave('ConductorBus');
            })
            ->where('id_upt', $userId)
            ->get();

        return view('busses.detail', [
            'bus' => $bus,
            'drivers' => $drivers,
            'bus_conductors' => $bus_conductors,
            'assignedDrivers' => $assignedDrivers,
            'assignedBusConductors' => $assignedBusConductors
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $userId = $user->hasRole('Upt') ? $user->id : ($user->hasRole('Admin') ? $user->id_upt : null);
        $bus = Buss::findOrFail($id);
        $driveconduc = DriverConductorBus::where('bus_id', $bus->id)->get();

        //dd($driveconduc);
        $assignedDrivers = $driveconduc->pluck('driver_id')->toArray();
        $assignedBusConductors = $driveconduc->pluck('bus_conductor_id')->toArray();
        //dd($assignedBusConductors);

        $drivers = User::role('Driver')
            ->where(function ($query) use ($bus) {
                $query->whereHas('driverBus', function ($query) use ($bus) {
                    $query->where('bus_id', $bus->id);
                })->orWhereDoesntHave('driverBus');
            })
            ->where('id_upt', $userId)
            ->get();

        $bus_conductors = User::role('Bus_Conductor')
            ->where(function ($query) use ($bus) {
                $query->whereHas('ConductorBus', function ($query) use ($bus) {
                    $query->where('bus_id', $bus->id);
                })->orWhereDoesntHave('ConductorBus');
            })
            ->where('id_upt', $userId)
            ->get();

        return view('busses.edit', compact('bus', 'drivers', 'bus_conductors', 'assignedDrivers', 'assignedBusConductors'));
    }


    public function update(Request $request, $id)
    {
        $bus = Buss::findOrFail($id);

        // Mencari bus dengan nomor plat kecuali bus yang sedang diupdate
        $bus_license = Buss::where('license_plate_number', $request->input('license_plate_number'))
            ->where('id', '!=', $id)
            ->first();

        // Validasi data yang diterima dari formulir
        $request->validate([
            'name' => 'required',
            'license_plate_number' => [
                'required',
                Rule::unique('busses')->ignore($bus->id),
            ],
            'chair' => 'required',
            'class' => 'required',
            'status' => 'required',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');

        if ($image) {
            $imageName = $image->store('avatars');
        } else {
            $imageName = 'avatars/default_bus.png';
        }

        $bus->name = $request->name;
        $bus->license_plate_number = $request->license_plate_number;
        $bus->chair = $request->chair;
        $bus->class = $request->class;
        $bus->status = $request->status;
        $bus->information = $request->status == 4 ? $request->keterangan : null;
        $bus->images = $imageName;

        $bus->save();

        $previousDrivers = $bus->drivers()->pluck('driver_id')->toArray();
        $previousBusConductors = $bus->busConductors()->pluck('bus_conductor_id')->toArray();
        // Update pengemudi dan kondektur yang terkait dengan bus
        if ($request->filled('drivers') && $request->filled('bus_conductors')) {
            foreach ($request->drivers as $driverId) {
                foreach ($request->bus_conductors as $busConductorId) {
                    DriverConductorBus::updateOrCreate(
                        ['driver_id' => $driverId, 'bus_id' => $bus->id],
                        ['bus_conductor_id' => $busConductorId, 'bus_id' => $bus->id]
                    );
                }
            }
        }

        // Hapus pengemudi dan kondektur yang dihapus dari select
        $removedDrivers = array_diff($previousDrivers, (array)$request->drivers);
        $removedBusConductors = array_diff($previousBusConductors, (array)$request->bus_conductors);

        foreach ($removedDrivers as $removedDriverId) {
            DriverConductorBus::where('driver_id', $removedDriverId)->where('bus_id', $bus->id)->delete();
        }

        foreach ($removedBusConductors as $removedBusConductorId) {
            DriverConductorBus::where('bus_conductor_id', $removedBusConductorId)->where('bus_id', $bus->id)->delete();
        }

        return redirect()->route('busses.index')->with('message', 'Data berhasil diperbarui');
    }



    public function destroyMulti(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'ids' => 'required|array', // Pastikan ids adalah array
            'ids.*' => 'exists:busses,id', // Pastikan setiap id ada dalam basis data Anda
        ]);

        // Lakukan penghapusan data berdasarkan ID yang diterima
        Buss::whereIn('id', $request->ids)->delete();

        // Redirect ke halaman sebelumnya atau halaman lain yang sesuai
        return redirect()->route('busses.index')->with('message', 'Berhasil menghapus data');
    }
}
