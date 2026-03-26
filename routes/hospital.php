<?php

use App\Http\Controllers\Api\Hospitals\HospitalAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:hospital'])->prefix('hospital')->group(function(){

    Route::post('/logout', [HospitalAuthController::class, 'logout']);
    Route::post('/logout-all', [HospitalAuthController::class, 'logoutAllDevices']);

});
