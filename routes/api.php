<?php

use App\Http\Controllers\API\Authentication\GoogleRoleController;
use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Authentication;
use App\Http\Controllers\Api\Authentication\AuthController;
use App\Http\Controllers\Api\Authentication\socialiteAuthenticationController;
use App\Http\Controllers\Api\Doctor\AppointmentController;




Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware(['auth:sanctum','role:client,admin,doctor']);
});
Route::middleware(['auth:sanctum','role:client,doctor,admin'])->prefix('profile')->group(function () {
    Route::get('/show', [ProfileController::class, 'show']);
    Route::put('/update', [ProfileController::class, 'update']);
    Route::put('/changePassword', [ProfileController::class, 'changePassword']);
});
Route::controller(socialiteAuthenticationController::class)->prefix('auth')->group(function () {
    Route::get('/google',action: 'redirectToGoogle');
    Route::get('/google/callback','handleGoogleCallback');
    Route::post('/google/CompleteRegister',GoogleRoleController::class)->middleware(['auth:sanctum','role:deactivated']);

    
});
Route::middleware(['role:doctor','auth:sanctum'])->prefix('doctor/appointments')->group(function () {
    Route::post('/store', action: [AppointmentController::class, 'store']);
    Route::get('/show', [AppointmentController::class, 'show']);
    Route::put('/update/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/destroy/{appointment}', [AppointmentController::class, 'destroy']);
    Route::patch('/deactivate/{appointment}', [AppointmentController::class, 'deactivate']);
});
