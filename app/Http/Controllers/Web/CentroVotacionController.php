<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CentroVotacion;
use App\Models\CentroVotacionSupervisor;
use App\Models\User;
use App\Traits\Acces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentroVotacionController extends Controller
{
    use Acces;
    public function index()
    {
        $menusPrin = $this->getMenus();
        return view('page.centroVotacion.index',compact('menusPrin'));
    }
    //ok
    public function getListCentros(Request $request)
    {
        $query = $request->input('q');
        $pageSize = $request->input('pageSize', 10);

        $centro = CentroVotacion::select(
            'centro_votacion.id as centro_votacion_id',
            'centro_votacion.nombre',
            'centro_votacion.direccion',
            'centro_votacion.provincia_id',
            'distrito.nombre as distrito',
            'centro_votacion.corregimiento_id',
            'centro_votacion.latitud',
            'centro_votacion.longitud',
            'centro_votacion.estado',
            'provincia.nombre as provincia_nombre',
            'distrito.nombre as distrito_nombre',
            'corregimiento.nombre as corregimiento_nombre'
        )
        ->join('provincia', 'centro_votacion.provincia_id', '=', 'provincia.id')
        ->join('distrito', 'centro_votacion.distrito', '=', 'distrito.id')
        ->join('corregimiento', 'centro_votacion.corregimiento_id', '=', 'corregimiento.id');

      
        if (!empty($query)) {
            $centro->where('provincia.nombre', 'like', '%' . $query . '%')
                   ->orWhere('distrito.nombre', 'like', '%' . $query . '%')
                   ->orWhere('corregimiento.nombre', 'like', '%' . $query . '%')
                   ->orWhere('centro_votacion.nombre', 'like', '%' . $query . '%');
        }
        
        $resultado = $centro->orderByDesc('centro_votacion.id')->paginate($pageSize);
        return response()->json($resultado);
    }

    //ok
    public function postShowCentroSupervisor(Request $request)
    {
        $query = $request->input('params');
        $centro = CentroVotacion::select(
            'centro_votacion.id as centro_votacion_id',
            'centro_votacion.nombre',
            'centro_votacion.direccion',
            'centro_votacion.provincia_id',
            'distrito',
            'corregimiento_id',
            'latitud',
            'longitud',
            'centro_votacion.estado',
            'provincia.nombre as provincia_nombre',
            'distrito.nombre as distrito_nombre',
            'corregimiento.nombre as corregimiento_nombre',
        )
            // ->where('persona_id', 'like', "%$query%")
            ->leftjoin('provincia', 'centro_votacion.provincia_id', '=', 'provincia.id')
            ->leftjoin('distrito', 'centro_votacion.distrito', '=', 'distrito.id')
            ->leftjoin('corregimiento', 'centro_votacion.corregimiento_id', '=', 'corregimiento.id')
            ->where('centro_votacion.id', '=', $query)->first();
        // Modificar el logo si está definido

        return response()->json($centro);
    }

    public function getListCentroSupervidores(Request $request)
    {
        $query = $request->input('q');
        $centro_id = $request->input('centro_id');
        $pageSize = $request->input('pageSize', 10);
        $candidatos_supervisores = CentroVotacionSupervisor::select(
            'personas.nombre',
            'personas.apellido_paterno',
            'personas.apellido_materno',
            'centro_votacion_supervisor.id',
            'centro_votacion_supervisor.estado',

        )
        ->leftjoin('users', 'centro_votacion_supervisor.supervisor_id', '=', 'users.id')
        ->leftjoin('personas','personas.id','=','users.persona_id')

        ->where('centro_votacion_supervisor.centro_votacion_id', $centro_id)
        ->orderBy('centro_votacion_supervisor.id', 'desc')->paginate($pageSize);

        return response()->json($candidatos_supervisores);
    }


    public function getListSupervisores(Request $request)
    {
        /*$supervisores =  User::join('personas', 'personas.id', '=', 'users.persona_id')
            ->join('perfiles', 'perfiles.id', '=', 'users.perfil_id')
            ->leftJoin('centro_votacion_supervisor', 'users.id', '=', 'centro_votacion_supervisor.supervisor_id')

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
            )->where('users.perfil_id', 2)->whereNull('centro_votacion_supervisor.user_id')

            
            ->get();*/
            $centro_id = $request->input('centro_id'); // Cambia esto al ID del centro de votación deseado

            $supervisores = User::join('personas', 'personas.id', '=', 'users.persona_id')
                ->join('perfiles', 'perfiles.id', '=', 'users.perfil_id')
                ->leftJoin('centro_votacion_supervisor', function ($join) use ($centro_id) {
                    $join->on('users.id', '=', 'centro_votacion_supervisor.supervisor_id')
                         ->where('centro_votacion_supervisor.centro_votacion_id', '=', $centro_id);
                })
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
                ->where('users.perfil_id', 2)
                ->whereNull('centro_votacion_supervisor.supervisor_id')
                ->get();
            


        return response()->json($supervisores);
    }

    public function postUpdateCentro(Request $request)
    {
        try {
            DB::beginTransaction();
            $nombre_input = $request->input('nombre');
            $direccion_input = $request->input('direccion');
            $provincia_id_input = $request->input('provincia_id');
            $distrito_id_input = $request->input('distrito_id');
            $corregimiento_id_input = $request->input('corregimiento_id');
            $latitud_input = $request->input('latitud');
            $longitud_input = $request->input('longitud');

            $centro = CentroVotacion::where('id', '=', $request->id)->first();
            $centro->nombre = $nombre_input;
            $centro->direccion = $direccion_input;
            $centro->provincia_id = $provincia_id_input;
            $centro->distrito = $distrito_id_input;
            $centro->corregimiento_id = $corregimiento_id_input;
            $centro->latitud = $latitud_input;
            $centro->longitud = $longitud_input;

            if ($centro->save()) {
                $centro_id = $centro->id;
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

    public function agregarCentro(Request $request)
    {
        try {
            DB::beginTransaction();
            $nombre_input = $request->input('nombre');
            $direccion_input = $request->input('direccion');
            $provincia_id_input = $request->input('provincia_id');
            $distrito_id_input = $request->input('distrito_id');
            $corregimiento_id_input = $request->input('corregimiento_id');
            $latitud_input = $request->input('latitud');
            $longitud_input = $request->input('longitud');
            $centro = new CentroVotacion();
            $centro->nombre = $nombre_input;
            $centro->direccion = $direccion_input;
            $centro->provincia_id = $provincia_id_input;
            $centro->distrito = $distrito_id_input;
            $centro->corregimiento_id = $corregimiento_id_input;
            $centro->latitud = $latitud_input;
            $centro->longitud = $longitud_input;

            if ($centro->save()) {
                $centro_id = $centro->id;
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

    public function agregarCentroSupervisor(Request $request)
    {
        try {
            DB::beginTransaction();
            $supervisor_id_input = $request->input('supervisor_id');
            $centro_votacion_id_input = $request->input('id');
            $centro_supervisor = new CentroVotacionSupervisor();
            $centro_supervisor->supervisor_id = $supervisor_id_input;
            $centro_supervisor->centro_votacion_id = $centro_votacion_id_input;
            if ($centro_supervisor->save()) {
                $centro_supervisor_id = $centro_supervisor->id;
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

    public function postDeleteCentroVoatacion(Request $request)
    {
        try {
            $centro_supervisor = CentroVotacion::find($request->id);
            $centro_supervisor->estado = 0;
            if ($centro_supervisor->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function postActiveCentroVoatacion(Request $request)
    {
        try {
            $centro_supervisor = CentroVotacion::findOrFail($request->id);
            $centro_supervisor->estado = 1;
            if ($centro_supervisor->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function postDeleteCentroSupervisor(Request $request)
    {
        try {
            $centro_supervisor = CentroVotacionSupervisor::findOrFail($request->id);
            $centro_supervisor->estado = 0;
            if ($centro_supervisor->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function postActiveCentroSupervisor(Request $request)
    {
        try {
            $centro_supervisor = CentroVotacionSupervisor::findOrFail($request->id);
            $centro_supervisor->estado = 1;
            if ($centro_supervisor->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


}
