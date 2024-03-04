<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\LoginController;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Rute untuk dashboard sesuai dengan peran pengguna
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        switch ($user->type) {
            case 0: // Superadmin
                return view('superadmin.dashboard');
                break;
            case 1: // Admin
                return view('admin.dashboard');
                break;
            case 2: // Kondektur/Sopir
                return view('driver.dashboard');
                break;
            case 3: // User
                return view('user.dashboard');
                break;
            default:
                return view('dashboard');
                break;
        }
    })->name('dashboard');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
