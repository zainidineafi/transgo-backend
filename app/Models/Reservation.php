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
        'status',
        'date_departure',
        'name',
        'gender',
        'phone_number',
    ];

    public function bus()
    {
        return $this->belongsTo(Buss::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
