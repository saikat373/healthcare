<?php

use App\Http\Controllers\Api\Hospitals\HospitalAuthController;
use App\Http\Controllers\Api\Patients\PatientsAuthController;
use App\Http\Controllers\Api\Phamacy\PharmacyAuthController;
use App\Http\Controllers\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/admin/login', [UserApiController::class,'adminLogin']);

/* Hospital */
Route::post('/hospital/login',[HospitalAuthController::class,'hospitalLogin']);
Route::post('/hospital/register',[HospitalAuthController::class,'register']);

/* Patient */
Route::post('/patient/login',[PatientsAuthController::class,'patientLogin']);
Route::post('/patient/register',[PatientsAuthController::class,'register']);

/* Pharmacy */
Route::post('/pharmacy/login',[PharmacyAuthController::class,'pharmacyLogin']);
Route::post('/pharmacy/register',[PharmacyAuthController::class,'register']);

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'ok',
            'app' => 'running',
            'database' => 'connected',
            'timestamp' => now(),
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'app' => 'running',
            'database' => 'not connected',
            'message' => $e->getMessage(),
        ], 500);
    }
});
