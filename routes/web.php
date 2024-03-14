<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UptController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');



Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('checkRole:Admin');


Route::get('/upts', [UptController::class, 'index'])
    ->middleware('role:Root')
    ->name('upts.index');
Route::get('/upts/search', [UptController::class, 'search'])
    ->middleware('role:Root')
    ->name('upts.search');
Route::get('/upts/create', [UptController::class, 'create'])
    ->middleware('role:Root')
    ->name('upts.create');
Route::post('/upts', [UptController::class, 'store'])
    ->middleware('role:Root')
    ->name('upts.store');
Route::get('/upts/{id}/edit', [UptController::class, 'edit'])
    ->middleware('role:Root')
    ->name('upts.edit');
Route::get('/upts/{id}/detail', [UptController::class, 'detail'])
    ->middleware('role:Root')
    ->name('upts.detail');

Route::put('/upts/{id}', [UptController::class, 'update'])
    ->middleware('role:Root')
    ->name('upts.update');
Route::delete('/upts/{id}', [UptController::class, 'destroy'])
    ->middleware('role:Root')
    ->name('upts.destroy');
Route::delete('delete-all', [UptController::class, 'multiDelete'])
    ->middleware('role:Root')
    ->name('delete-all');


Route::get('/full_dashboard', 'DashboardController@index')->middleware('role:upt');
Route::get('/custom_dashboard', 'CustomDashboardController@index')->middleware('role:admin');


Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
