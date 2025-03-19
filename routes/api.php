<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;



Route::post('/v1/auth/login', [AuthController::class, 'login']);
Route::middleware('auth.jwt')->get('/v1/auth/logout', [AuthController::class, 'logout']);
Route::middleware('auth.jwt')->get('/v1/auth/refresh', [AuthController::class, 'refreshToken']);
Route::post('/v1/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/v1/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
