<?php

namespace App\Http\Controllers;

use App\Models\Buss;
use App\Models\DriverConductorBus;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBusStation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class BussesController extends Controller
{
    // Menampilkan daftar bus milik pengguna yang sedang login
    public function index()
    {
        // Mendapatkan ID pengguna yang sedang login
        $userId = Auth::id();

        // Mengambil semua data bus yang dimiliki oleh pengguna yang sedang login
        $busses = Buss::where('id_upt', $userId)->paginate(10);

        // Mengembalikan data tersebut ke view
        return view('busses.index', compact('busses'));
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
        $image = $request->file('image');
        if ($image) {
            $imageName = $image->store('avatars');
        } else {
            $imageName = 'avatars/default.jpg';
        }

        // Validasi data yang diterima dari formulir
        $request->validate([
            'name' => 'required',
            'license_plate_number' => 'required',
            'chair' => 'required',
            'class' => 'required',
            'price' => 'required',
            'status' => 'required', // Menambahkan validasi untuk status
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'drivers' => 'nullable|array',
            'bus_conductors' => 'nullable|array',
        ]);

        $userId = Auth::id();

        $bus = Buss::create([
            'name' => $request->name,
            'license_plate_number' => $request->license_plate_number,
            'chair' => $request->chair,
            'class' => $request->class,
            'price' => $request->price,
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


    public function search(Request $request)
    {
        $userId = Auth::id();
        $searchTerm = $request->input('search');

        $busses = Buss::where(function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('license_plate_number', 'like', '%' . $searchTerm . '%');
        })
            ->where('id_upt', $userId) // Menambahkan kondisi id_upt
            ->paginate(10);

        return view('busses.index', compact('busses'));
    }



    public function edit($id)
    {
        $userId = Auth::id();
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

        // Validasi data yang diterima dari formulir
        $request->validate([
            'name' => 'required',
            'license_plate_number' => 'required',
            'chair' => 'required',
            'class' => 'required',
            'price' => 'required',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        $image = $request->file('image');
        if ($image) {
            $imageName = $image->store('avatars');
            $bus->images = $imageName;
        }

        $bus->name = $request->name;
        $bus->license_plate_number = $request->license_plate_number;
        $bus->chair = $request->chair;
        $bus->class = $request->class;
        $bus->price = $request->price;
        $bus->status = $request->status;
        $bus->information = $request->status == 4 ? $request->keterangan : null;

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
