<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketBooking;
use Illuminate\Support\Facades\Auth;
use App\Models\Buss;
use Illuminate\Support\Facades\Validator;

class ApiTicketBookingController extends Controller
{
    public function index()
    {
        $bookings = TicketBooking::with('bus', 'user')->where('user_id', Auth::id())->get();
        return response()->json(['bookings' => $bookings]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|exists:busses,id',
            'seat_count' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bus = Buss::findOrFail($request->bus_id);

        if ($bus->chair < $request->seat_count) {
            return response()->json(['message' => 'Not enough seats available'], 400);
        }

        $booking = TicketBooking::create([
            'bus_id' => $request->bus_id,
            'user_id' => Auth::id(),
            'seat_count' => $request->seat_count,
        ]);

        $bus->chair -= $request->seat_count;
        $bus->save();

        return response()->json(['message' => 'Booking successful', 'booking' => $booking], 201);
    }

    public function checkTicket($id)
    {
        $booking = TicketBooking::findOrFail($id);

        if ($booking->checked) {
            return response()->json(['message' => 'Ticket already checked'], 400);
        }

        $booking->checked = true;
        $booking->save();

        return response()->json(['message' => 'Ticket checked successfully']);
    }

    public function cancelCheckTicket($id)
    {
        $booking = TicketBooking::findOrFail($id);

        if (!$booking->checked) {
            return response()->json(['message' => 'Ticket not checked yet'], 400);
        }

        $booking->checked = false;
        $booking->save();

        return response()->json(['message' => 'Ticket unchecked successfully']);
    }

    public function destroy($id)
    {
        $booking = TicketBooking::findOrFail($id);
        $bus = $booking->bus;

        $bus->chair += $booking->seat_count;
        $bus->save();

        $booking->delete();

        return response()->json(['message' => 'Booking cancelled and seats restored']);
    }
}
