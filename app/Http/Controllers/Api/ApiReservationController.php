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
    public function reserveTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'tickets_booked' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Temukan jadwal berdasarkan schedule_id
        $schedule = Schedule::find($request->input('schedule_id'));

        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        $bus = Buss::find($schedule->bus_id);

        if (!$bus) {
            return response()->json(['error' => 'Bus not found'], 404);
        }

        if ($bus->chair < $request->tickets_booked) {
            return response()->json(['error' => 'Not enough chairs available'], 400);
        }

        // Cek apakah sudah ada reservasi untuk pengguna dan jadwal yang sama
        $reservation = Reservation::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->where('status', 1)
            ->first();

        if ($reservation) {
            // Jika ada, tambahkan jumlah tiket yang dipesan
            $reservation->tickets_booked += $request->tickets_booked;
            $reservation->save();
        } else {
            // Jika tidak ada, buat reservasi baru
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'bus_id' => $bus->id,
                'schedule_id' => $schedule->id,
                'tickets_booked' => $request->tickets_booked,
                'status' => 1, // Status 1: reserved
            ]);
        }

        // Mengurangi jumlah kursi di bus
        $bus->chair -= $request->tickets_booked;
        $bus->save();

        return response()->json(['reservation' => $reservation], 201);
    }

    public function getTickets(Request $request)
    {
        $user = auth('api')->user();
        $reservations = Reservation::with('user')->where('user_id', $user->id)->where('status', 1)->get();

        return response()->json(['reservations' => $reservations], 200);
    }

    public function getTicketDetails($id)
    {
        $reservation = Reservation::find($id);
        $reservation->with('user');
        if (!$reservation) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        return response()->json(['reservation' => $reservation], 200);
    }

    public function getTicketHistory(Request $request)
    {
        $user = auth('api')->user();
        $reservations = Reservation::with('user')->where('user_id', $user->id)->where('status', 2)->get();

        return response()->json(['reservations' => $reservations], 200);
    }

    public function acceptTicket($id)
{
    $reservation = Reservation::find($id);

    if (!$reservation) {
        return response()->json(['error' => 'Ticket not found'], 404);
    }

    if ($reservation->status != 1) {
        return response()->json(['error' => 'Ticket already accepted or invalid status'], 400);
    }

    $bus = Buss::find($reservation->bus_id); // Perbaiki nama model dari Buss ke Bus
    if (!$bus) {
        return response()->json(['error' => 'Bus not found'], 404);
    }

    $bus->chair += $reservation->tickets_booked; // Mengembalikan jumlah kursi
    $bus->save();

    $reservation->status = 2; // Status 2: accepted
    $reservation->save();

    return response()->json(['reservation' => $reservation], 200);
}

}


