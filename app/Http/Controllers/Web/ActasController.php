<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Acta;
use App\Models\Mesa;
use App\Models\PartidoPolitico;
use App\Models\Person;
use App\Models\PersoneroMesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class ActasController extends Controller
{
    public function index(){
        return view('page.actas');
    }

    public function getListPartidoPoliticos(Request $request)
    {
        $partido_politicos = PartidoPolitico::select(
            'id',
            'nombre',
            'partido_politico',
            'logo',
            'orden',
            'estado'
        )
        ->where('estado', 1)
        ->orderBy('orden', 'asc')
        ->get();
        
        $modifiedResults = $partido_politicos->map(function ($item) {
            if (isset($item->logo)) {
                $item->logo = Storage::url($item->logo);
            }
            return $item;
        });
        
        return response()->json($modifiedResults);
    }

    public function getUserName(Request $request){
        $id = $request->input('params');
        $result = Person::select('id','nombre','apellido_paterno','apellido_materno')->where('id',$id)->first();
        return response()->json($result);
    }

    public function getListActasRegistradas(Request $request){
        $query = $request->input('q');    
        $pageSize = $request->input('pageSize', 10);

        $mesas = DB::table('mesa as m')
        ->select('m.nombre', 'm.cantidad_votantes', 'a.personero_id', DB::raw("CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) as personero"), 'a.centro_votacion_id', 'cv.nombre as nombre_centro_votacion')
        ->join('acta as a', 'm.id', '=', 'a.mesa_id')
        ->join('users as u', 'a.personero_id', '=', 'u.id')
        ->join('personas as p', 'u.persona_id', '=', 'p.id')
        ->join('centro_votacion as cv', 'a.centro_votacion_id', '=', 'cv.id')
        ->where('m.estado',0)
        ->orWhere('m.nombre', 'like', "%$query%")
        ->groupBy('m.nombre', 'm.cantidad_votantes', 'a.personero_id', 'a.centro_votacion_id', 'personero', 'cv.nombre')
        ->paginate($pageSize );
                        
        return response()->json($mesas);
    }
    
    public function getPersonero(){
        //personero
        $personero = DB::select("select u.id as user_id,
                                u.sub_usuario,
                                p.id as persona_id, 
                                concat(p.nombre,' ',p.apellido_paterno,' ',p.apellido_materno)as personero
                        from personero_mesa pm
                        inner join users u on pm.personero_id = u .id 
                        inner join personas p on u.persona_id = p.id 
                        where u.id = ".Auth::user()->id."
                        group by u.id,u.sub_usuario,p.id,  concat(p.nombre,' ',p.apellido_paterno,' ',p.apellido_materno)");

        //supervisor                        
        $supervisor = DB::select("select u.id as user_id, p.id as persona_id, concat(p.nombre,' ',p.apellido_paterno,' ',p.apellido_materno)as supervisor from users u
                                inner join personas p on u.persona_id = p.id 
                                where u.id =".$personero[0]->sub_usuario);

        //centro de votacion                        
        $centroVotacion = DB::select("select cv.id,cvs.supervisor_id,cvs.centro_votacion_id,cv.nombre  from centro_votacion_supervisor cvs
                                inner join centro_votacion cv on cvs.centro_votacion_id = cv.id 
                                where cvs.supervisor_id =".$supervisor[0]->user_id);

        //mesa
        $mesa = DB::select("select * from mesa m where m.estado=1 and m.centro_votacion_id = ".$centroVotacion[0]->centro_votacion_id);  
        
        return response()->json([
            'personero'=>$personero,
            'supervisor'=>$supervisor,
            'centroVotacion'=>$centroVotacion,
            'mesa'=>$mesa
        ]);
    }

    public function addActas(Request $request){
       try {
            DB::beginTransaction();
            $mesa = Mesa::where('id',$request->mesa_id)->first();
            $mesa->estado = 0;
            $mesa->cantidad_votantes = $request->total;
            $mesa->save();
            foreach ($request->partidos as $value) {
                $acta = new Acta();
                $acta->partida_politica_id = $value['id'];
                $acta->mesa_id = $request->mesa_id;
                $acta->total_acta = $value['votos'];
                $acta->personero_id = $request->personero_id;
                $acta->centro_votacion_id = $request->centro_votacion_id;
                $acta->save(); 
            }
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Se registro acta correctamente',
            ]);
       } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'=>false,
                'message'=>'No se ingreso acta verificar',
            ]);
            throw $th;
       }
    }
}
