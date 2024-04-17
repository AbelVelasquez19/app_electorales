<?php

use App\Http\Controllers\Web\CandidatoController;
use App\Http\Controllers\Web\CentroVotacionController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\MesaController;
use App\Http\Controllers\Web\PartidoPoliticoController;
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
            Route::get('/province', 'province');
            Route::post('/district', 'district');
            Route::post('/corregimient', 'coregimient');
            Route::get('/codigo-pais', 'codigoPais');
        });
    });
































   
    Route::prefix('partido-politico')->group(function () {
        Route::controller(PartidoPoliticoController::class)->group(function () {
            Route::get('/', 'index')->name('partido-politico.index');
            Route::post('/add', 'agregarPartido');
            Route::get('/list', 'getListPartidoPoliticos');
            Route::post('/show', 'postShowPartido');
            Route::post('/update', 'postUpdatePartido');
            Route::post('/delete', 'postDeletePartido');
            Route::post('/active', 'postActivePartido');
        });
    });


    Route::prefix('candidato')->group(function () {
        Route::controller(CandidatoController::class)->group(function () {
            Route::get('/', 'index')->name('candidato.index');
            Route::post('/add', 'agregarPartido');
            Route::get('/list', 'getListCandidatos');
            Route::post('/show', 'postShowCandidato');
            Route::post('/update', 'postUpdateCandidato');
            Route::post('/delete', 'postDeleteCandidato');
            Route::post('/active', 'postActiveCandidato');
            Route::get('/tipoCandidato', 'tipoCandidato');
            Route::get('/tipoCandidatoPersonas', 'tipoCandidatoPersonas');

        });
    });

    Route::prefix('centro-votacion')->group(function () {
        Route::controller(CentroVotacionController::class)->group(function () {
            Route::get('/', 'index')->name('centro-votacion.index');
            Route::post('/add', 'agregarCentro');
            Route::post('/add-supervisor', 'agregarCentroSupervisor');

            Route::get('/list', 'getListCentros');
            Route::get('/list-supervisores', 'getListSupervisores');
            Route::get('/list-supervisores-centro', 'getListCentroSupervidores');

            Route::post('/show', 'postShowCentro');
            Route::post('/update', 'postUpdateCentro');
            Route::post('/delete', 'postDeleteCandidato');
            Route::post('/active', 'postActiveCandidato');
            Route::get('/tipoCandidato', 'tipoCandidato');
            Route::get('/tipoCandidatoPersonas', 'tipoCandidatoPersonas');

            Route::post('/delete-supervisor', 'postDeleteCandidatoSupervisor');
            Route::post('/active-supervisor', 'postActiveCandidatoSupervisor');



        });
    });



    Route::prefix('mesa')->group(function () {
        Route::controller(MesaController::class)->group(function () {
            Route::get('/', 'index')->name('mesa.index');
            Route::get('/list', 'getListMesas');
            Route::get('/list-centros-votacion', 'getListCentrosVotacion');
            Route::post('/show', 'postShowMesa');
            Route::post('/add', 'agregarMesa');
            Route::post('/update', 'postUpdateMesa');
            Route::get('/list-personeros', 'getListPersoneros');

            Route::get('/list-personeros-mesa', 'getListMesaPersonero');

            Route::post('/add-personero', 'agregarMesaPersonero');


            Route::post('/delete', 'postDeleteCandidato');
            Route::post('/active', 'postActiveCandidato');
            Route::get('/tipoCandidato', 'tipoCandidato');
            Route::get('/tipoCandidatoPersonas', 'tipoCandidatoPersonas');

            Route::post('/delete-personero', 'postDeleteMesaPersonero');
            Route::post('/active-personero', 'postActiveMesaPersonero');



        });
    });
});

