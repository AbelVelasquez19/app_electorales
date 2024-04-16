<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CentroVotacionRegister;
use App\Models\CentroVotacion;
use Illuminate\Http\Request;

class MapsController extends Controller
{
    public function index(){
        return view('page.maps');
    }

    public function getListCentroVotacion(){
        $result = CentroVotacion::where('estado',1)->get();
        return response()->json(['status'=>true, 'data'=>$result]);
    }

    public function getNewCentroVotacion(){
        return view('page.centroVotacionNuevo');
    }

    public function guardarCentroCosto(CentroVotacionRegister $request){
        try {
            $centroVotacion = new CentroVotacion();
            $centroVotacion->nombre = $request->centroVotacion['nombre'];
            $centroVotacion->direccion = $request->centroVotacion['direccion'];
            $centroVotacion->provincia_id = $request->centroVotacion['provincia_id'];
            $centroVotacion->distrito = $request->centroVotacion['distrito_id'];
            $centroVotacion->corregimiento_id = $request->centroVotacion['corregimiento_id'];
            $centroVotacion->corregimiento_id = $request->centroVotacion['corregimiento_id'];
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
