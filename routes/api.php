<?php

use Celysium\Launcher\Controllers\AuthController;
use Celysium\Launcher\Controllers\EnvironmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('api')->group(function () {

    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:launcher')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::name('environment')->prefix('environment')->group(function () {

            Route::get('/', [EnvironmentController::class, 'index'])->name('index');
            Route::post('/', [EnvironmentController::class, 'store'])->name('store');
            Route::get('quick', [EnvironmentController::class, 'quick'])->name('quick');
        });
    });
});