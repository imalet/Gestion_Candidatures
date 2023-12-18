<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FormationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});





Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::middleware('api')->group(function () {
        Route::get('/logout', 'logout');
    });
});

Route::controller(FormationController::class)->group(function(){
    Route::get('/formation/lister', 'index');
    Route::get('/formation/detail/{id_formation}', 'show');
    Route::post('/formation/ajouter', 'store');
    Route::post('/formation/modifier/{id_formation}', 'update');
    Route::get('/formation/supprimer/{id_formation}', 'destroy');
});