<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        // Mengambil semua data reservasi beserta schedule terkait
        $reservations = Reservation::with('schedule', 'user')->get();

        // Mengirim data reservasi ke view 'reservations.index'
        return view('reservations.index', compact('reservations'));
    }
}
