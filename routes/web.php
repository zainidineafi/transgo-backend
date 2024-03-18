<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UptController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');



Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('checkRole:Admin');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('role:Upt')
    ->name('dashboard');
Route::get('/custom_dashboard', 'CustomDashboardController@index')->middleware('role:Admin');


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
Route::post('/upts/delete', [UptController::class, 'destroyMulti'])
    ->middleware('role:Root')
    ->name('upts.destroy.multi');




Route::get('/admins', [AdminController::class, 'index'])
    ->middleware('role:Upt')
    ->name('admins.index');
Route::get('/admins/search', [AdminController::class, 'search'])
    ->middleware('role:Upt')
    ->name('admins.search');
Route::get('/admins/create', [AdminController::class, 'create'])
    ->middleware('role:Upt')
    ->name('admins.create');
Route::post('/admins', [AdminController::class, 'store'])
    ->middleware('role:Upt')
    ->name('admins.store');
Route::get('/admins/{id}/edit', [AdminController::class, 'edit'])
    ->middleware('role:Upt')
    ->name('admins.edit');
Route::get('/admins/{id}/detail', [AdminController::class, 'detail'])
    ->middleware('role:Upt')
    ->name('admins.detail');
Route::put('/admins/{id}', [AdminController::class, 'update'])
    ->middleware('role:Upt')
    ->name('admins.update');
Route::delete('/admins/{id}', [AdminController::class, 'destroy'])
    ->middleware('role:Upt')
    ->name('admins.destroy');
// Route::delete('/admins/delete-all', [AdminController::class, 'multiDelete'])
//     ->middleware('role:Upt')
//     ->name('admins.delete-all');




Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
