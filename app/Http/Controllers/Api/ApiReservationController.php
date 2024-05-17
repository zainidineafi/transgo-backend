<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Buss;
use App\Models\Schedule;


class ApiReservationController extends Controller
{
    public function store(Request $request)
{
    // Validasi data yang diterima dari permintaan
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'bus_id' => 'required|exists:busses,id',
        'schedule_id' => 'required|exists:schedules,id',
        'date_departure' => 'required|date',
        'name' => 'required|string|max:255',
        'gender' => 'required|in:pria,wanita',
        'phone_number' => 'required|string|max:20',
    ]);

    // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Mendapatkan bus berdasarkan bus_id
    $bus = Buss::find($request->bus_id);

    // Cek apakah jumlah kursi tersedia
    if ($bus->chair < 1) {
        return response()->json(['error' => 'No available seats'], 400);
    }

    // Buat reservasi baru
    $reservation = Reservation::create([
        'user_id' => $request->user_id,
        'bus_id' => $request->bus_id,
        'schedule_id' => $request->schedule_id,
        'status' => 1, // Status default
        'date_departure' => $request->date_departure,
        'name' => $request->name,
        'gender' => $request->gender,
        'phone_number' => $request->phone_number,
    ]);

    // Kurangi jumlah kursi di bus
    $bus->chair -= 1;
    $bus->save();

    // Kembalikan respons dengan data reservasi
    return response()->json(['reservation' => $reservation], 201);
}

public function checkTicket(Request $request, $reservationId)
{
    // Validasi data yang diterima dari permintaan
    $validator = Validator::make($request->all(), [
        'status' => 'required|in:acc',
    ]);

    // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Cari reservasi berdasarkan ID
    $reservation = Reservation::find($reservationId);
    if (!$reservation) {
        return response()->json(['error' => 'Reservation not found'], 404);
    }

    // Jika statusnya sudah di-ACC atau sudah masuk ke riwayat pemesanan, kembalikan pesan kesalahan
    if ($reservation->status === 2 || $reservation->status === 3) {
        return response()->json(['error' => 'Reservation already accepted or in past reservations'], 400);
    }

    // Jika statusnya di-ACC, kembalikan kursi bus
    if ($request->status === 'acc') {
        // Jika reservasi masih dalam kondisi 'current', tambahkan kursi di bus
        if ($reservation->status === 1) {
            // Tambahkan kursi di bus
            $bus = $reservation->bus;
            $bus->chair += 1;
            $bus->save();
        }

        // Update status reservasi
        $reservation->status = 2; // Status ACC
        $reservation->save();

        // Kirim pesan sukses jika reservasi di-ACC
        return response()->json(['message' => 'Reservation accepted'], 200);
    }

    return response()->json(['message' => 'Ticket checked and seat count updated'], 200);
}



public function currentReservations($userId)
{
    // Mendapatkan semua pemesanan yang sedang berlangsung atau belum lewat oleh user_id tertentu
    $currentReservations = Reservation::where('date_departure', '>=', now())
                                       ->where('user_id', $userId)
                                       ->where(function ($query) {
                                           // Menambahkan kondisi untuk memeriksa apakah reservasi telah di-ACC oleh Bus_Conductor
                                           $query->where('status', '<>', 2) // Status ACC
                                                 ->orWhereNull('status');
                                       })
                                       ->get();

    return response()->json([
        'current_reservations' => $currentReservations,
    ], 200);
}

public function pastReservations($userId)
{
    // Mendapatkan semua pemesanan yang sudah lewat atau sudah di-ACC oleh user_id tertentu
    $pastReservations = Reservation::where(function ($query) use ($userId) {
                                        $query->where('date_departure', '<', now())
                                              ->where('user_id', $userId);
                                    })
                                    ->orWhere(function ($query) use ($userId) {
                                        $query->where('user_id', $userId)
                                              ->where('status', 2); // Status ACC
                                    })
                                    ->get();

    return response()->json([
        'past_reservations' => $pastReservations
    ], 200);
}

public function getAllCurrentReservations()
{
    // Mendapatkan semua reservasi yang masih dalam status pemesanan (status 1)
    $currentReservations = Reservation::where('status', 1)->get();

    return response()->json([
        'current_reservations' => $currentReservations,
    ], 200);
}

public function getReservationsByPassengerName(Request $request)
{
    // Validasi data yang diterima dari permintaan
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
    ]);

    // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Cari reservasi berdasarkan nama penumpang
    $reservations = Reservation::where('name', 'like', '%' . $request->name . '%')->get();

    return response()->json([
        'reservations' => $reservations,
    ], 200);
}


}


