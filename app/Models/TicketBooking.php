<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'user_id',
        'seat_count',
        'checked',
    ];

    // Relasi dengan model Bus
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

