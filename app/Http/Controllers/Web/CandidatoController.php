<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cantidato;
use App\Models\Persona;
use App\Models\TipoCandidato;
use Illuminate\Http\Request;

class CandidatoController extends Controller
{
    public function index()
    {
        return view('page.candidatos.index');
    }


    public function getListCandidatos(Request $request)
    {
        $query = $request->input('q');

        $pageSize = $request->input('pageSize', 10);
        // return $candidatos = Cantidato::all();
        $candidatos = Cantidato::select(
            'candidatos.id as candidato_id',
            'candidatos.persona_id',
            'tipo_candidato_id',
            'candidatos.provincia_id',
            'distrito',
            'coregimiento_id',
            'orden',
            'candidatos.estado',
            'personas.numero_documento',
            'personas.nombre',
            'personas.apellido_paterno',
         'personas.apellido_materno',


        )
            // ->where('persona_id', 'like', "%$query%")
            ->leftjoin('personas', 'candidatos.persona_id', '=', 'personas.id')

            ->orderBy('candidatos.id', 'desc')->paginate($pageSize);
        //    return $candidatos;


        // Actualizar la colección de resultados paginados con la colección modificada

        return response()->json($candidatos);
    }


    public function tipoCandidato(){
        $tipoCandidato = TipoCandidato::get();
        return response()->json($tipoCandidato);
    }
    public function tipoCandidatoPersonas(){
        $candidatos = Persona::get();
        return response()->json($candidatos);
    }


    public function postDeleteCandidato(Request $request)
    {
        try {
            $candidato = Cantidato::findOrFail($request->id);
            $candidato->estado = 0;
            if ($candidato->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function postActiveCandidato(Request $request)
    {
        try {
            $candidato = Cantidato::findOrFail($request->id);
            $candidato->estado = 1;
            if ($candidato->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
