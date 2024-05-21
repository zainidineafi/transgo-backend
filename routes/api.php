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


// Group route untuk API dengan prefix 'passenger'
Route::prefix('passenger')->group(function () {
    Route::post('/register', [ApiPassengerController::class, 'registerPassengers']);
    Route::post('/login', [ApiPassengerController::class, 'loginPassengers']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [ApiPassengerController::class, 'passengers']);
        Route::put('/update-password', [ApiPassengerController::class, 'updatePassengers']);
        Route::get('/schedules', [ApiPassengerController::class, 'schedules']);
        Route::put('/update-address', [ApiPassengerController::class, 'updateAddress']);
        Route::put('/update-phone', [ApiPassengerController::class, 'updatePhoneNumber']);
        Route::post('/update-image', [ApiPassengerController::class, 'updateImage']);
        Route::get('/by-name/{name}', [ApiPassengerController::class, 'getPassengerByName']);
        Route::get('/by-id/{id}', [ApiPassengerController::class, 'getPassengerById']);
        Route::post('/schedules/by-destination', [ApiPassengerController::class, 'searchSchedulesByDestination']);
        Route::post('/schedules/by-time', [ApiPassengerController::class, 'searchSchedulesByTimeStart']);
        Route::post('/schedules/by-from-station', [ApiPassengerController::class, 'searchSchedulesByFromStationId']);
    });
});



// Bus Conductor routes
Route::prefix('bus-conductor')->group(function () {
    Route::post('/register', [ApiBusConductorController::class, 'registerBusConductor']);
    Route::post('/login', [ApiBusConductorController::class, 'loginBusConductor']);
    Route::get('/', [ApiBusConductorController::class, 'BusConductors']);
    Route::put('/update-password', [ApiBusConductorController::class, 'updatePassword']);
    Route::put('/update-address', [ApiBusConductorController::class, 'updateAddress']);
    Route::put('/update-phone-number', [ApiBusConductorController::class, 'updatePhoneNumber']);
    Route::put('/update-image', [ApiBusConductorController::class, 'updateImage']);
});

// Driver routes
Route::prefix('driver')->group(function () {
    Route::post('/register', [ApiBusConductorController::class, 'registerDriver']);
    Route::post('/login', [ApiBusConductorController::class, 'loginDriver']);
    Route::put('/update-password', [ApiBusConductorController::class, 'updatePassword']);
    Route::put('/update-address', [ApiBusConductorController::class, 'updateAddress']);
    Route::put('/update-phone-number', [ApiBusConductorController::class, 'updatePhoneNumber']);
    Route::put('/update-image', [ApiBusConductorController::class, 'updateImage']);
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

