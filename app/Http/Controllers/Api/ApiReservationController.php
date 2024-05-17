<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Schedule;

class ReservationController extends Controller
{
    public function reserveTicket(Request $request)
    {
        // Validasi data yang diterima dari permintaan
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'phone_number' => 'required|string|max:20',
            'date_departure' => 'required|date',
        ]);

        // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Dapatkan pengguna yang sedang login
        $user = Auth::user();

        // Buat pemesanan baru
        $reservation = new Reservation();
        $reservation->user_id = $user->id;
        $reservation->schedule_id = $request->schedule_id;
        $reservation->name = $request->name;
        $reservation->gender = $request->gender;
        $reservation->phone_number = $request->phone_number;
        $reservation->date_departure = $request->date_departure;
        $reservation->status = 1; // Tentukan status default (misalnya: belum diproses)
        $reservation->save();

        // Kembalikan respons dengan pesan sukses
        return response()->json(['message' => 'Ticket reserved successfully'], 201);
    }

    public function getReservations()
    {
        // Dapatkan pengguna yang sedang login
        $user = Auth::user();

        // Dapatkan daftar pemesanan untuk pengguna yang sedang login
        $reservations = Reservation::where('user_id', $user->id)->get();

        // Kembalikan respons dengan daftar pemesanan
        return response()->json(['reservations' => $reservations]);
    }
}
