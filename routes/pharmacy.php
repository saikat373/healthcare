<?php

use App\Http\Controllers\Api\Phamacy\PharmacyAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:pharmacy'])->prefix('pharmacy')->group(function(){
    Route::post('/logout', [PharmacyAuthController::class, 'logout']);
    Route::post('/logout-all', [PharmacyAuthController::class, 'logoutAllDevices']);
});
