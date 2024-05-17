<?php

use App\Http\Controllers\Api\ApiBussesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\UptController;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\JsonResponseMiddleware;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiPassengerController;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\ApiBusConductorController;
use App\Http\Controllers\Api\ApiTicketBookingController;
use App\Http\Controllers\Api\ApiBusStatusController;
use App\Http\Controllers\Api\ApiReservationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/upts', [ApiController::class, 'upts']);
Route::get('/users', [ApiController::class, 'users']);

//Passenger
Route::post('/registerPassengers', [ApiPassengerController::class, 'registerPassengers']);
Route::get('/passengers', [ApiPassengerController::class, 'passengers']);
Route::post('/loginPassengers', [ApiPassengerController::class, 'loginPassengers']);
Route::put('/updatePassengers', [ApiPassengerController::class, 'updatePassengers']);
Route::get('/schedules', [ApiPassengerController::class, 'schedules']);

//BusConductors
Route::get('/BusConductors', [ApiBusConductorController::class, 'BusConductors']);
Route::post('/loginBusConductor', [ApiBusConductorController::class, 'loginBusConductor']);
Route::post('/registerBusConductor', [ApiBusConductorController::class, 'registerBusConductor']);
Route::post('/registerDriver', [ApiBusConductorController::class, 'registerDriver']);

//status dan data bus
Route::put('/busses/{id}/status', [ApiBussesController::class, 'updateStatus']);
Route::get('/busses', [ApiBussesController::class, 'DataBusses']);


//pemesanan tiket
Route::post('/reservations', [ApiReservationController::class, 'store']);
Route::put('/reservations/check/{reservationId}', [ApiReservationController::class, 'checkTicket']);
Route::get('/reservations/current/{user_id}', [ApiReservationController::class, 'currentReservations']);
Route::get('/reservations/past/{user_id}', [ApiReservationController::class, 'pastReservations']);
Route::get('/reservations/current', [ApiReservationController::class, 'getAllCurrentReservations']);
Route::get('/reservations/by-passenger-name', [ApiReservationController::class, 'getReservationsByPassengerName']);
