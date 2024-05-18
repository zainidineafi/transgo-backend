<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePassengerRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Periksa apakah pengguna terotentikasi dan memiliki peran 'Passenger'
        if ($user && $user->hasRole('Passenger')) {
            return $next($request);
        }

        // Jika tidak, kembalikan respons unauthorized
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
