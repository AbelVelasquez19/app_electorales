<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CentroVotacion;
use App\Models\Mesa;
use App\Models\MesaPersonero;
use App\Models\User;
use App\Traits\Acces;
use Illuminate\Support\Facades\DB;

class MesaController extends Controller
{
    use Acces;
    public function index()
    {
        $menusPrin = $this->getMenus();
        return view('page.mesa.index',compact('menusPrin'));
    }


    public function getListMesas(Request $request)
    {
        $query = $request->input('q');

        $pageSize = $request->input('pageSize', 10);
        // return $candidatos = Cantidato::all();
        $candidatos = Mesa::select(
            'mesa.id as mesa_id',
            'mesa.nombre',
            'mesa.numero',
            'mesa.centro_votacion_id',
            'mesa.cantidad_votantes',
            'mesa.estado',
            'centro_votacion.nombre as centro_votacion_nombre',
            'centro_votacion.direccion as centro_votacion_direccion'
        )
            ->leftjoin('centro_votacion', 'mesa.centro_votacion_id', '=', 'centro_votacion.id')
            ->orderBy('mesa.id', 'desc')->paginate($pageSize);

        // Actualizar la colección de resultados paginados con la colección modificada
        return response()->json($candidatos);
    }


    public function getListCentrosVotacion()
    {
        $centros_votacion = CentroVotacion::get();

        return response()->json($centros_votacion);
    }

    public function postShowMesa(Request $request)
    {
        $query = $request->input('params');
        $mesa = Mesa::select(
            'mesa.id as mesa_id',
            'mesa.nombre',
            'mesa.numero',
            'mesa.centro_votacion_id',
            'mesa.cantidad_votantes',
            'mesa.estado',
            'centro_votacion.nombre as centro_votacion_nombre',
            'centro_votacion.direccion as centro_votacion_direccion'
        )
            ->leftjoin('centro_votacion', 'mesa.centro_votacion_id', '=', 'centro_votacion.id')
            ->where('mesa.id', '=', $query)->first();
        // Modificar el logo si está definido

        return response()->json($mesa);
    }



    public function agregarMesa(Request $request)
    {
        try {
            DB::beginTransaction();

            $nombre_input = $request->input('nombre');
            $numero_input = $request->input('numero');
            $centro_votacion_id_input = $request->input('centro_votacion_id');
            $cantidad_votantes_input = $request->input('cantidad_votantes');




            $mesa = new Mesa();
            $mesa->nombre = $nombre_input;
            $mesa->numero = $numero_input;

            $mesa->centro_votacion_id = $centro_votacion_id_input;
            $mesa->cantidad_votantes = $cantidad_votantes_input;

            if ($mesa->save()) {
                $mesa_id = $mesa->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }

    public function postUpdateMesa(Request $request)
    {
        try {
            DB::beginTransaction();
            $nombre_input = $request->input('nombre');
            $numero_input = $request->input('numero');
            $centro_votacion_id_input = $request->input('centro_votacion_id');
            $cantidad_votantes_input = $request->input('cantidad_votantes');

            $mesa = Mesa::where('id', '=', $request->id)->first();
            $mesa->nombre = $nombre_input;
            $mesa->numero = $numero_input;

            $mesa->centro_votacion_id = $centro_votacion_id_input;
            $mesa->cantidad_votantes = $cantidad_votantes_input;


            if ($mesa->save()) {
                $mesa_id = $mesa->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se actualizo correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }


    public function getListPersoneros(Request $request)
    {

        $mesa_id = $request->input('mesa_id');
        $personeros =  User::join('personas', 'personas.id', '=', 'users.persona_id')
            ->join('perfiles', 'perfiles.id', '=', 'users.perfil_id')
            ->select(
                'users.id',
                'users.isActive',
                'users.email',
                'users.numero_celular',
                'personas.id as persona_id',
                'personas.numero_documento',
                'personas.nombre as persona_nombre',
                'personas.apellido_paterno',
                'personas.apellido_materno',
                'perfiles.id as perfiles_id',
                'perfiles.nombre as perfiles_nombre',
            )
            // ->where('users.perfil_id', 3)->get();

            ->where('users.perfil_id', 3)
            ->whereNotExists(function ($query) use ($mesa_id) {
                $query->select(DB::raw(1))
                    ->from('personero_mesa')
                    ->whereRaw('personero_mesa.personero_id = users.id')
                    ->where('personero_mesa.mesa_id', $mesa_id);
            })
            ->get();

            
        return response()->json($personeros);
    }

    public function agregarMesaPersonero(Request $request)
    {
        try {
            DB::beginTransaction();


            $personero_id_input = $request->input('personero_id');
            $mesa_id_input = $request->input('id');


            $mesa_personero = new MesaPersonero();
            $mesa_personero->personero_id = $personero_id_input;
            $mesa_personero->mesa_id = $mesa_id_input;

            if ($mesa_personero->save()) {
                $mesa_personero_id = $mesa_personero->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }


    public function getListMesaPersonero(Request $request)
    {

        $query = $request->input('q');
        $mesa_id = $request->input('mesa_id');

        $pageSize = $request->input('pageSize', 10);
        // return $candidatos = Cantidato::all();
        $mesa_personeros = MesaPersonero::select(
            'personas.nombre',
            'personas.apellido_paterno',
            'personas.apellido_materno',
            'personero_mesa.id',
            'personero_mesa.estado',
        )
            ->leftjoin('users', 'personero_mesa.personero_id', '=', 'users.id')
            // ->leftjoin('personas', 'personero_mesa.personero_id', '=', 'personas.id')
            ->leftjoin('personas','personas.id','=','users.persona_id')
            ->where('personero_mesa.mesa_id', $mesa_id)
            ->orderBy('personero_mesa.id', 'desc')->paginate($pageSize);
        //    return $mesa_personeros;


        // Actualizar la colección de resultados paginados con la colección modificada

        return response()->json($mesa_personeros);
    }

    public function postDeleteMesaPersonero(Request $request)
    {
        try {
            $mesa_personero = MesaPersonero::find($request->id);
            // $mesa_personero->estado = 0;
            if ($mesa_personero->delete()) {
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function postActiveMesaPersonero(Request $request)
    {
        try {
            $mesa_personero = MesaPersonero::findOrFail($request->id);
            $mesa_personero->estado = 1;
            if ($mesa_personero->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
