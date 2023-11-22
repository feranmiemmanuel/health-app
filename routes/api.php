<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Hospital\HospitalController;
use App\Http\Controllers\Patient\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/get-something', [HospitalController::class, 'fetchHospital']);

Route::middleware('auth.api')->group(function () {
    Route::prefix('patient')->group(function () {
        Route::post('/bio-data', [PatientController::class, 'bioData']);
        Route::get('/bio-data', [PatientController::class, 'fetchBioData']);
    });
});
