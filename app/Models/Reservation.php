<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bus_id',
        'schedule_id',
        'tickets_booked',
        'status',
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan model Bus
    public function bus()
    {
        return $this->belongsTo(Buss::class);
    }

    // Relasi dengan model Schedule
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
