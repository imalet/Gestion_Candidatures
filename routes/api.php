<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CandidatureController;
use App\Http\Controllers\API\FormationController;
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
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::controller(AuthController::class)->group(function () {

    Route::post('/register', 'register');
    Route::post('/login', 'login');

    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', 'logout');
    });
});

Route::controller(FormationController::class)->group(function () {
    
    Route::get('/formations', 'index');
    Route::get('/formation/{id}', 'show');

    
    Route::middleware(['auth:api', 'IsAdmin'])->group(function () {
        Route::post('/formations', 'store');
        Route::patch('/formation/{id}', 'update');
        Route::delete('/formation{id}', 'destroy');
    });
});

Route::controller(CandidatureController::class)->group(function () {

    Route::middleware('IsCandidat')->group(function () {
        Route::get('/cadidature/{id}', 'store');
    });
    Route::middleware('auth:api', 'IsAdmin')->group(function () {
        Route::get('/candidatures', 'index');
        Route::get('/candidatures/accepter', 'candidatureAccepter');
        Route::get('/candidatures/refuser', 'candidatureRefuser');
        Route::patch('/candidatures/{candidature_id}/{etat}', 'acceptDenieCandidature');
    });
});
