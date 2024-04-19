<?php

use App\Http\Controllers\Api\AutenticacionController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\MesaSupervisorPersoneroController;
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


Route::get('lista-mesas', [GeneralController::class, 'listaMesas']);

Route::get('lista-partidos', [GeneralController::class, 'listadoPartidos']);

Route::get('lista-mesas-personero', [MesaSupervisorPersoneroController::class, 'listaMesasPersonero']);
Route::post('registrar-personero', [MesaSupervisorPersoneroController::class, 'guardarPersoneroMesa']);

Route::post('guardar-mesa-acta', [MesaSupervisorPersoneroController::class, 'guardarMesaActa']);

Route::post('guardar-documento-acta', [MesaSupervisorPersoneroController::class, 'guardarFormatoActa']);
