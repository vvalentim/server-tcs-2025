<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/usuarios', [UserController::class, 'show']);
// Route::get('/usuarios', [UserController::class, 'list']);
Route::post('/usuarios', [UserController::class, 'store']);
Route::put('/usuarios', [UserController::class, 'edit']);
Route::delete('/usuarios', [UserController::class, 'destroy']);
