<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Hospital\HospitalController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Reminders\RemindersController;
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
    Route::post('patients/register', [AuthController::class, 'register']);
    Route::post('doctors/register', [AuthController::class, 'doctorRegistration']);
    Route::post('/send-password-reset-token', [AuthController::class, 'sendToken']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/fetch-due-reminders', [RemindersController::class, 'fetchDueReminders']);

Route::middleware('auth.api')->group(function () {
    Route::prefix('patient')->group(function () {
        Route::post('/bio-data', [PatientController::class, 'bioData']);
        Route::get('/bio-data', [PatientController::class, 'fetchBioData']);
        Route::post('/set-reminder', [RemindersController::class, 'createReminderPatient']);
        Route::get('/medications', [RemindersController::class, 'getMedicationsPatient']);
    });
    Route::prefix('doctor')->group(function () {
        Route::get('/patients', [HospitalController::class, 'fetchHospitalPatients']);
        Route::get('/patients/bio-data', [HospitalController::class, 'fetchHospitalPatientsBioData']);
        Route::post('/patients/set-reminder', [RemindersController::class, 'createReminderDoctor']);
        Route::get('/patients/medications', [RemindersController::class, 'getMedicationsDoctor']);
    });
});
