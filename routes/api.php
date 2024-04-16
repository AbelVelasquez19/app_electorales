<?php

use App\Http\Controllers\Api\AutenticacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AutenticacionController::class, 'login']);
    Route::post('logout', [AutenticacionController::class, 'logout']);
    Route::post('refresh', [AutenticacionController::class, 'refresh']);
    Route::post('me', [AutenticacionController::class, 'me']);
    Route::post('register', [AutenticacionController::class, 'register']);
    Route::post('verificar-codigo', [AutenticacionController::class, 'verifyCode']);

});

