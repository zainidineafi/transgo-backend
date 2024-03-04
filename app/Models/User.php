<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'gender',
        'phone_number',
        'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's gender.
     *
     * @param  string  $value
     * @return string
     */
    public function getGenderAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Set the user's gender.
     *
     * @param  string  $value
     * @return void
     */
    public function setGenderAttribute($value)
    {
        $this->attributes['gender'] = strtolower($value);
    }

    /**
     * Get the user's type.
     *
     * @param  int  $value
     * @return string
     */
    public function getTypeAttribute($value)
    {
        $types = [
            0 => 'Superadmin',
            1 => 'Admin',
            2 => 'Kondektur/Sopir',
            3 => 'User',
        ];

        return $types[$value] ?? 'Unknown';
    }
}
