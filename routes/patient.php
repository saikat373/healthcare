<?php

use App\Http\Controllers\Patients\PatientsAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:patient'])->prefix('patient')->group(function(){
    Route::post('/logout', [PatientsAuthController::class, 'logout']);
    Route::post('/logout-all', [PatientsAuthController::class, 'logoutAllDevices']);
});
