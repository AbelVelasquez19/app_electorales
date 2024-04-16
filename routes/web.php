<?php

use App\Http\Controllers\Web\ActasController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\MapsController;
use App\Http\Controllers\Web\PersonaController;
use App\Http\Controllers\Web\UbigeoController;
use App\Http\Controllers\Web\UserioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/', [LoginController::class,'index'])->name('home');
    Route::post('/code-verify', [LoginController::class,'verifyCode'])->name('code_verify');
    Route::post('/login', [LoginController::class,'login'])->name('login');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::prefix('dashboard')->group(function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard');
        });
    });

    Route::prefix('persona')->group(function () {
        Route::controller(PersonaController::class)->group(function () {
            Route::get('/', 'index')->name('persona');
            Route::post('/add', 'agregarPersona');
            Route::get('/list', 'getListUsers');
            Route::post('/show', 'postShowUser');
            Route::post('/update', 'postUpdateUser');
            Route::post('/delete', 'postDeleteUser');
            Route::post('/active', 'postActiveUser');
        });
    });

    Route::prefix('user')->group(function () {
        Route::controller(UserioController::class)->group(function () {
            Route::get('/', 'index')->name('user');
            Route::post('/add', 'agregarUser');
            Route::get('/list', 'getListUsers');
            Route::post('/show', 'postShowUser');
            Route::post('/update', 'postUpdateUser');
            Route::post('/delete', 'postDeleteUser');
            Route::post('/active', 'postActiveUser');
            Route::get('/profile', 'getListProfile');
            Route::post('/person', 'getPersona');
        });
    });

    Route::prefix('ubigeus')->group(function () {
        Route::controller(UbigeoController::class)->group(function () {
            Route::get('/department', 'department');
            Route::get('/province', 'province')->name('province');
            Route::post('/district', 'district')->name('district');;
            Route::post('/corregimient', 'coregimient')->name('coregimient');;
            Route::get('/codigo-pais', 'codigoPais');
        });
    });

    Route::prefix('mapas')->group(function () {
        Route::controller(MapsController::class)->group(function () {
            Route::get('/', 'index')->name('maps');
            Route::get('/centro-votacion', 'getListCentroVotacion');
            Route::get('/nuevo-centro-votacion', 'getNewCentroVotacion');
            Route::post('/guardar', 'guardarCentroCosto')->name('guardarCentroCosto');
        });
    });

    Route::prefix('actas')->group(function () {
        Route::controller(ActasController::class)->group(function () {
            Route::get('/', 'index')->name('actas');
            Route::get('/centro-votacion', 'getListCentroVotacion');
            Route::get('/nuevo-centro-votacion', 'getNewCentroVotacion');
            Route::post('/guardar', 'guardarCentroCosto')->name('guardarCentroCosto');
            Route::get('/codigo-pais', 'codigoPais');
        });
    });
});

