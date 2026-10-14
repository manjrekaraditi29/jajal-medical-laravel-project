<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiCaseController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::apiResource('cases', ApiCaseController::class);
});