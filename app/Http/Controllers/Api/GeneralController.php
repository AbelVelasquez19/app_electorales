<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListadoPartidosPoliticosResource;
use App\Models\CentroVotacionSupervisor;
use App\Models\Mesa;
use App\Models\PartidoPolitico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{


    public function listaMesas()
    {
        $mesas = Mesa::get();
        return response()->json($mesas);
    }



    public function listadoPartidos()
    {


        $partidos = PartidoPolitico::where('estado', 1)->get();
        return response()->json([
            'codigo' => 200,
            'mensaje' => 'Se cargaron satisfactoriamente los datos.',
            'data' =>  ListadoPartidosPoliticosResource::collection($partidos)

        ], 200);
    }

    public function obtenerAvanceDeMesasPorSupervisor(Request $request)
    {

        $usuario_id = $request->input('usuario_id'); //personero id usuario logeado



        $cantidad_reporte = CentroVotacionSupervisor::where('supervisor_id', $usuario_id)
            ->join('centro_votacion AS cv', 'centro_votacion_supervisor.centro_votacion_id', '=', 'cv.id')
            ->join('mesa AS m', 'm.centro_votacion_id', '=', 'cv.id')
            ->select(
                'centro_votacion_supervisor.supervisor_id',
                'cv.nombre AS centro_votacion',
                DB::raw('COUNT(m.id) AS total_mesas'),
                DB::raw('SUM(CASE WHEN m.estado = 0 THEN 1 ELSE 0 END) AS mesas_completadas'),
                DB::raw('(SUM(CASE WHEN m.estado = 0 THEN 1 ELSE 0 END) / COUNT(m.id)) * 100 AS porcentaje_avance')
            )
            ->groupBy('centro_votacion_supervisor.supervisor_id', 'cv.nombre')
            ->get();

        $mesasIncompletas = DB::table('centro_votacion_supervisor AS cvs')
            ->join('centro_votacion AS cv', 'cvs.centro_votacion_id', '=', 'cv.id')
            ->join('mesa AS m', function ($join) {
                $join->on('m.centro_votacion_id', '=', 'cv.id')
                    ->where('m.estado', '=', 1);  // Filtra las mesas que no están completas
            })
            ->leftJoin('personero_mesa AS pm', 'pm.mesa_id', '=', 'm.id')
            ->leftJoin('users AS u', 'u.id', '=', 'pm.personero_id')
            ->leftJoin('personas AS p', 'p.id', '=', 'u.persona_id')
            ->select(
                'm.id AS mesa_id',
                'm.nombre AS mesa_nombre',
                'm.numero AS mesa_numero',
                'p.id AS persona_id',
                'p.nombre AS persona_nombre',
                'p.apellido_paterno AS persona_apellido_paterno',
                'p.apellido_materno AS persona_apellido_materno',
                'u.email AS personero_email'
            )
            ->where('cvs.supervisor_id', $usuario_id)
            ->whereIn('pm.id', function ($query) {
                $query->select(DB::raw('MIN(id)'))
                    ->from('personero_mesa')
                    ->groupBy('mesa_id');
            })
            ->get();


        return response()->json([
            'codigo' => 200,
            'mensaje' => 'Se cargaron satisfactoriamente los datos.',
            'data' =>  [
                'avance' => $cantidad_reporte,
                'mesas' => $mesasIncompletas
            ]

        ], 200);
    }
}
