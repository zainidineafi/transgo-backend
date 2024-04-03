<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UptController;
use App\Http\Controllers\BusStationController;
use App\Models\BusStation;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');



Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('checkRole:Admin');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('role:Upt')->name('dashboard');
Route::get('/custom_dashboard', 'CustomDashboardController@index')->middleware('role:Admin');


Route::middleware(['role:Root'])->group(function () {
    Route::get('/upts', [UptController::class, 'index'])->name('upts.index');
    Route::get('/upts/search', [UptController::class, 'search'])->name('upts.search');
    Route::get('/upts/create', [UptController::class, 'create'])->name('upts.create');
    Route::post('/upts', [UptController::class, 'store'])->name('upts.store');
    Route::get('/upts/{id}/edit', [UptController::class, 'edit'])->name('upts.edit');
    Route::get('/upts/{id}/detail', [UptController::class, 'detail'])->name('upts.detail');
    Route::put('/upts/{id}', [UptController::class, 'update'])->name('upts.update');
    Route::post('/upts/delete', [UptController::class, 'destroyMulti'])->name('upts.destroy.multi');
});


Route::middleware(['role:Upt'])->group(function () {
    //route for admins
    Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
    Route::get('/admins/search', [AdminController::class, 'search'])->name('admins.search');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
    Route::get('/admins/{id}/edit', [AdminController::class, 'edit'])->name('admins.edit');
    Route::get('/admins/{id}/detail', [AdminController::class, 'detail'])->name('admins.detail');
    Route::put('/admins/{id}', [AdminController::class, 'update'])->name('admins.update');
    Route::post('/admins/delete', [AdminController::class, 'destroyMulti'])->name('admins.destroy.multi');

    // route for bus_stations
    Route::get('/bus_stations', [BusStationController::class, 'index'])->name('bus_stations.index');
    Route::get('/bus_stations/search', [BusStationController::class, 'search'])->name('bus_stations.search');
    Route::get('/bus_stations/create', [BusStationController::class, 'create'])->name('bus_stations.create');
    Route::post('/bus_stations', [BusStationController::class, 'store'])->name('bus_stations.store');
    Route::get('/bus_stations/{id}/detail', [BusStationController::class, 'detail'])->name('bus_stations.detail');
    Route::get('/bus_stations/{id}/edit', [BusStationController::class, 'edit'])->name('bus_stations.edit');
    Route::put('/bus_stations/{id}', [BusStationController::class, 'update'])->name('bus_stations.update');
    Route::post('/bus_stations/delete', [BusStationController::class, 'destroyMulti'])->name('bus_stations.destroy.multi');
});




Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
