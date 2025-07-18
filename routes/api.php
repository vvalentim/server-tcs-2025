<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DraftController;
use App\Http\Controllers\Api\MailController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Route::get('/usuarios', [UserController::class, 'list']);
Route::get('/usuarios', [UserController::class, 'show']);
Route::post('/usuarios', [UserController::class, 'store']);
Route::put('/usuarios', [UserController::class, 'edit']);
Route::delete('/usuarios', [UserController::class, 'destroy']);

Route::get('/rascunhos', [DraftController::class, 'list']);
Route::post('/rascunhos', [DraftController::class, 'store']);
Route::put('/rascunhos/{draftId}', [DraftController::class, 'edit']);
Route::get('/rascunhos/{draftId}', [DraftController::class, 'show']);
Route::delete('/rascunhos/{draftId}', [DraftController::class, 'destroy']);

Route::get('/emails', [MailController::class, 'list']);
Route::post('/emails', [MailController::class, 'send']);
Route::post('/emails/{draftId}', [MailController::class, 'sendFromDraft']);
Route::get('/emails/{draftId}', [MailController::class, 'read']);
