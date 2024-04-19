<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cantidato;
use App\Models\Persona;
use App\Models\TipoCandidato;
use App\Traits\Acces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CandidatoController extends Controller
{
    use Acces;
    public function index()
    {
        $menusPrin = $this->getMenus();
        return view('page.candidatos.index',compact('menusPrin'));
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
    public function postShowCandidato(Request $request)
    {
        $query = $request->input('params');
        $partido_politico = Cantidato::select(
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
            ->where('candidatos.id', '=', $query)->first();
        // Modificar el logo si está definido
       
        return response()->json($partido_politico);
    }

    public function tipoCandidato()
    {
        $tipoCandidato = TipoCandidato::get();
        return response()->json($tipoCandidato);
    }
    public function tipoCandidatoPersonas()
    {
        $candidatos = Persona::get();
        return response()->json($candidatos);
    }
    public function agregarPartido(Request $request)
    {
        try {
            DB::beginTransaction();

            $persona_id_input = $request->input('persona_id');
            $orden_input = $request->input('orden');
            $tipo_candidato_id_input = $request->input('tipo_candidato_id');
            $provincia_id_input = $request->input('provincia_id');
            $distrito_id_input = $request->input('distrito_id');
            $corregimiento_id_input = $request->input('corregimiento_id');


            $candidato = new Cantidato();
            $candidato->persona_id = $persona_id_input;
            $candidato->tipo_candidato_id = $tipo_candidato_id_input;
            $candidato->provincia_id = $provincia_id_input;
            $candidato->distrito = $distrito_id_input;
            $candidato->coregimiento_id = $corregimiento_id_input;
            $candidato->orden = $orden_input;

            if ($candidato->save()) {
                $candidato_id = $candidato->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            // DB::rollBack();
            // return response()->json([
            //     'status' => false,
            //     'message' => 'La informacíon no se guardó'
            // ]);
            throw $th;
        }
    }

    public function postUpdateCandidato(Request $request)
    {
        try {
            DB::beginTransaction();
            $persona_id_input = $request->input('persona_id');
            $orden_input = $request->input('orden');
            $tipo_candidato_id_input = $request->input('tipo_candidato_id');
            $provincia_id_input = $request->input('provincia_id');
            $distrito_id_input = $request->input('distrito_id');
            $corregimiento_id_input = $request->input('corregimiento_id');

            $candidato = Cantidato::where('id', '=', $request->id)->first();
            $candidato->persona_id = $persona_id_input;
            $candidato->tipo_candidato_id = $tipo_candidato_id_input;
            $candidato->provincia_id = $provincia_id_input;
            $candidato->distrito = $distrito_id_input;
            $candidato->coregimiento_id = $corregimiento_id_input;
            $candidato->orden = $orden_input;


            if ($candidato->save()) {
                $candidato_id = $candidato->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se actualizo correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            // DB::rollBack();
            // return response()->json([
            //     'status' => false,
            //     'message' => 'La informacíon no se guardó'
            // ]);
            throw $th;
        }
    }






    public function postDeleteCandidato(Request $request)
    {
        try {
            $candidato = Cantidato::find($request->id);
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
