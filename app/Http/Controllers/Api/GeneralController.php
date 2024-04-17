<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListadoPartidosPoliticosResource;
use App\Models\Mesa;
use App\Models\PartidoPolitico;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    

    public function listaMesas(){
        $mesas = Mesa::get();
        return response()->json($mesas);
    }


     
    public function listadoPartidos(){


        $partidos = PartidoPolitico::where('estado',1)->get();
        return response()->json([
            'codigo' => 200,
            'mensaje' => 'Se cargaron satisfactoriamente los datos.',
            'data' =>  ListadoPartidosPoliticosResource::collection($partidos)

        ], 200);
    }
    




}
