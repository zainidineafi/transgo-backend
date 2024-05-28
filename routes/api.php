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

Route::get('passengers', [ApiPassengerController::class, 'getAllPassengers']);

Route::post('passenger/register', [ApiPassengerController::class, 'register']);
Route::post('passenger/login', [ApiPassengerController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/passenger/update-password', [ApiPassengerController::class, 'updatePassword']);
    Route::post('passenger/update-address', [ApiPassengerController::class, 'updateAddress']);
    Route::post('passenger/update-phone', [ApiPassengerController::class, 'updatePhone']);
    Route::post('passenger/update-image', [ApiPassengerController::class, 'updateImage']);
    Route::get('passenger/passenger-by-name', [ApiPassengerController::class, 'getPassengerByName']);
    Route::get('passenger/passenger/{id}', [ApiPassengerController::class, 'getPassengerById']);
    Route::get('passenger/schedules', [ApiPassengerController::class, 'schedules']);
    Route::get('passenger/search-schedules-by-destination', [ApiPassengerController::class, 'searchSchedulesByDestination']);
    Route::get('passenger/searchSchedulesByFromStationAddress', [ApiPassengerController::class, 'searchSchedulesByFromStationAddress']);
    Route::get('passenger/searchSchedulesByFromStationName', [ApiPassengerController::class, 'searchSchedulesByFromStationName']);
    Route::post('passenger/logout', [ApiPassengerController::class, 'logout']);

    Route::post('/reserve', [ApiReservationController::class, 'reserveTicket']);
    Route::get('/tickets', [ApiReservationController::class, 'getTickets']);
    Route::get('/ticket/{id}', [ApiReservationController::class, 'getTicketDetails']);
    Route::get('/ticket-history', [ApiReservationController::class, 'getTicketHistory']);
    Route::post('/accept-ticket/{id}', [ApiReservationController::class, 'acceptTicket']);
});


// Bus Conductor routes
Route::get('bus-conductor', [ApiBusConductorController::class, 'BusConductors']);

// Routes for Bus Conductors
Route::prefix('bus-conductor')->group(function () {
    Route::post('register', [ApiBusConductorController::class, 'registerBusConductor']);
    Route::post('login', [ApiBusConductorController::class, 'loginBusConductor']);
    Route::get('name', [ApiBusConductorController::class, 'getBusConductorByName'])->middleware('auth:sanctum');
    Route::get('id/{id}', [ApiBusConductorController::class, 'getBusConductorById'])->middleware('auth:sanctum');
});

Route::get('Driver', [ApiBusConductorController::class, 'Driver']);

// Routes for Drivers
Route::prefix('driver')->group(function () {
    Route::post('register', [ApiBusConductorController::class, 'registerDriver']);
    Route::post('login', [ApiBusConductorController::class, 'loginDriver']);
    Route::get('name', [ApiBusConductorController::class, 'getDriverByName'])->middleware('auth:sanctum');
    Route::get('id/{id}', [ApiBusConductorController::class, 'getDriverById'])->middleware('auth:sanctum');
});

// Common Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [ApiBusConductorController::class, 'logout']);
    Route::post('update-password', [ApiBusConductorController::class, 'updatePassword']);
    Route::post('update-address', [ApiBusConductorController::class, 'updateAddress']);
    Route::post('update-phone-number', [ApiBusConductorController::class, 'updatePhoneNumber']);
    Route::post('update-image', [ApiBusConductorController::class, 'updateImage']);
});

//status dan data bus
Route::put('/busses/status/{id}', [ApiBussesController::class, 'updateStatus']);
Route::get('/busses', [ApiBussesController::class, 'DataBusses']);
Route::get('/BusStation', [ApiBussesController::class, 'BusStation']);


//pemesanan tiket
Route::prefix('reservations')->group(function () {
Route::post('/', [ApiReservationController::class, 'store']);
Route::put('/check/{reservationId}', [ApiReservationController::class, 'checkTicket']);
Route::get('/current/{user_id}', [ApiReservationController::class, 'currentReservations']);
Route::get('/past/{user_id}', [ApiReservationController::class, 'pastReservations']);
Route::get('/current', [ApiReservationController::class, 'getAllCurrentReservations']);
Route::get('/by-passenger-name', [ApiReservationController::class, 'getReservationsByPassengerName']);
});

Route::get('/tickets/all', [ApiReservationController::class, 'getAllTickets']);
