<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\CONTROLLERS\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/sendEmail', [AuthController::class, 'sendAgain']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/verify', [AuthController::class, 'verify']);

Route::post('/loadUser', [AuthController::class, 'loadUser']);

Route::post('/login', [AuthController::class, 'login']);
