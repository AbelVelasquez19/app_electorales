<?php

namespace App\Http\Controllers\Web;

use App\Events\CierraActaEvento;
use App\Http\Controllers\Controller;
use App\Http\Requests\CentroVotacionRegister;
use App\Models\CentroVotacion;
use App\Traits\Acces;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapsController extends Controller
{
    use Acces;
    public function index()
    {
        $menusPrin = $this->getMenus();
        return view('page.maps', compact('menusPrin'));
    }

    public function getListCentroVotacion()
    {
        $result = CentroVotacion::where('estado', 1)->get();
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
        return response()->json(['status' => true, 'data' => $result]);
    }

    public function getNewCentroVotacion()
    {
        $menusPrin = $this->getMenus();
        return view('page.centroVotacionNuevo', compact('menusPrin'));
    }

    public function guardarCentroCosto(CentroVotacionRegister $request)
    {
        try {
            $centroVotacion = new CentroVotacion();
            $centroVotacion->nombre = $request->centroVotacion['nombre'];
            $centroVotacion->direccion = $request->centroVotacion['direccion'];
            $centroVotacion->provincia_id = $request->centroVotacion['provincia_id'];
            $centroVotacion->distrito = $request->centroVotacion['distrito_id'];
            $centroVotacion->departamento_id = $request->centroVotacion['departamento_id'];
            $centroVotacion->latitud = $request->latitud;
            $centroVotacion->longitud = $request->longitud;
            if ($centroVotacion->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }

    /*public function getListCentroVotacionPanel(Request $request)
    {


        try {


            $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
            $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
            $distrito_id = empty($request->districts_id) ? null : $request->districts_id;

            $result = CentroVotacion::where('estado', 1);
            if (!is_null($departamento_id)) {
                $result->where('departamento_id', $departamento_id);
            }
            if (!is_null($provincia_id)) {
                $result->where('provincia_id', $provincia_id);
            }
            if (!is_null($distrito_id)) {
                $result->where('distrito', $distrito_id);
            }

            $result->get();


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
            return response()->json(['status' => true, 'data' => $result]);
        } catch (Error $e) {
            return response()->json(['status' => true, 'data' => $e]);

        }


    }*/

    public function getListCentroVotacionPanel(Request $request)
    {
        try {
            $departamento_id =  $request->departaments_id;
            $provincia_id =  $request->provinces_id;
            $distrito_id =  $request->districts_id;

            $query = CentroVotacion::query();

            if (($departamento_id) != 'null' && $departamento_id != '0') {
                $query->where('departamento_id', $departamento_id);
            }
            if (($provincia_id) != 'null' && $provincia_id != '0') {
                $query->where('provincia_id', $provincia_id);
            }
            if (($distrito_id) != 'null' && $distrito_id != '0') {
                $query->where('distrito', $distrito_id);
            }

            $result = $query->where('estado', 1)->get();

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

            event(new CierraActaEvento);

            return response()->json(['status' => true, 'data' => $result,'per'=>$request->departaments_id]);
        } catch (Error $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()]);
        }
    }
}
