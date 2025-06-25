<?php

use App\Http\Controllers\Web\ActiveUsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ActiveUsersController::class, 'index']);
