<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CentroVotacionRegister;
use App\Models\CentroVotacion;
use App\Traits\Acces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapsController extends Controller
{
    use Acces;
    public function index(){
        $menusPrin = $this->getMenus();
        return view('page.maps',compact('menusPrin'));
    }

    public function getListCentroVotacion(){
        $result = CentroVotacion::where('estado',1)->get();
        $result->map(function ($centro) {
            $total_mesa_habilitado = DB::select("SELECT COUNT(id) AS total_mesa_habilitado FROM mesa WHERE centro_votacion_id = ? AND estado = 1", [$centro->id])[0]->total_mesa_habilitado;
            $total_mesa_cerrado = DB::select("SELECT COUNT(id) AS total_mesa_cerrado FROM mesa WHERE centro_votacion_id = ? AND estado = 0", [$centro->id])[0]->total_mesa_cerrado;
        
            $total_mesa = $total_mesa_habilitado + $total_mesa_cerrado;
        
            if ($total_mesa > 0) {
                $centro->total_mesa_habilitado = $total_mesa_habilitado;
                $centro->total_mesa_cerrado = $total_mesa_cerrado;
                $centro->porcentaje_mesa_habilitado = round(($total_mesa_habilitado / $total_mesa) * 100);
                $centro->porcentaje_mesa_cerrado = round(($total_mesa_cerrado / $total_mesa) * 100, 2);
            } else {
                $centro->total_mesa_habilitado = 0;
                $centro->total_mesa_cerrado = 0;
                $centro->porcentaje_mesa_habilitado = 0;
                $centro->porcentaje_mesa_cerrado = 0;
            }
        
            return $centro;
        });
        return response()->json(['status'=>true, 'data'=>$result]);
    }

    public function getNewCentroVotacion(){
        $menusPrin = $this->getMenus();
        return view('page.centroVotacionNuevo',compact('menusPrin'));
    }

    public function guardarCentroCosto(CentroVotacionRegister $request){
        try {
            $centroVotacion = new CentroVotacion();
            $centroVotacion->nombre = $request->centroVotacion['nombre'];
            $centroVotacion->direccion = $request->centroVotacion['direccion'];
            $centroVotacion->provincia_id = $request->centroVotacion['provincia_id'];
            $centroVotacion->distrito = $request->centroVotacion['distrito_id'];
            $centroVotacion->departamento_id = $request->centroVotacion['departamento_id'];
            $centroVotacion->latitud = $request->latitud;
            $centroVotacion->longitud = $request->longitud;
            if($centroVotacion->save()){
                    return response()->json([
                        'status'=>true,
                        'message'=>'La informacíon se guardó correctamente'
                    ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }
}
