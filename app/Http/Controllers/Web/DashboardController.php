<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Acta;
use App\Models\Mesa;
use App\Models\PartidoPolitico;
use App\Traits\Acces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    use Acces;
    public function index()
    {
        $menusPrin = $this->getMenus();
        return view('page.index', compact('menusPrin'));
    }

    public function reportePartidoPolTotal(Request $request)
    {
        $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
        $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
        $distrito_id = empty($request->districts_id) ? null : $request->districts_id;

        $query = PartidoPolitico::select(
            'partido_politico.nombre',
            'partido_politico.partido_politico',
            'partido_politico.logo',
            'partido_politico.orden',
            'partido_politico.color',
            DB::raw('SUM(acta.total_acta) as suma')
        )
            ->join('acta', 'partido_politico.id', '=', 'acta.partida_politica_id')
            ->join('centro_votacion', 'acta.centro_votacion_id', '=', 'centro_votacion.id')
            ->where('partido_politico.estado', 1);
        if (!is_null($departamento_id)) {
            $query->where('centro_votacion.departamento_id', $departamento_id);
        }
        if (!is_null($provincia_id)) {
            $query->where('centro_votacion.provincia_id', $provincia_id);
        }
        if (!is_null($distrito_id)) {
            $query->where('centro_votacion.distrito', $distrito_id);
        }
        $result = $query->groupBy(
            'partido_politico.nombre',
            'partido_politico.partido_politico',
            'partido_politico.logo',
            'partido_politico.orden',
            'acta.partida_politica_id',
            'partido_politico.color'
        )
            ->orderBy('partido_politico.orden', 'ASC')
            ->get();

        $modifiedResults = $result->map(function ($item) {
            if (isset($item->logo)) {
                $item->logo = Storage::url($item->logo);
            }
            return $item;
        });
        return response()->json($modifiedResults);
    }

    public function reporteEstadoActa(Request $request)
    {
        $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
        $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
        $distrito_id = empty($request->districts_id) ? null : $request->districts_id;

        $estadoActas = Mesa::join('centro_votacion', 'centro_votacion.id', '=', 'mesa.centro_votacion_id')
            ->select('mesa.estado');
        if (!is_null($departamento_id)) {
            $estadoActas->where('centro_votacion.departamento_id', $departamento_id);
        }
        if (!is_null($provincia_id)) {
            $estadoActas->where('centro_votacion.provincia_id', $provincia_id);
        }
        if (!is_null($distrito_id)) {
            $estadoActas->where('centro_votacion.distrito', $distrito_id);
        }
        $results = $estadoActas->get();

        $conteoEstados = $results->map(function ($item) {
            $nombre = $item->estado == 0 ? 'POR PROCESAR' : 'PROCESADAS';
            return [
                'nombre' => $nombre,
                'estado' => $item->estado,
            ];
        });

        $agrupados = $conteoEstados->groupBy('nombre')->map(function ($group) {
            return [
                'nombre' => $group->first()['nombre'],
                'total' => $group->count(),
            ];
        });

        return $agrupados->values();
    }

    public function reporteDistribucionVotos(Request $request)
    {
        $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
        $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
        $distrito_id = empty($request->districts_id) ? null : $request->districts_id;

        $acta = Acta::join('partido_politico', 'partido_politico.id', '=', 'acta.partida_politica_id')
            ->join('centro_votacion', 'centro_votacion.id', '=', 'acta.centro_votacion_id')
            ->select(
                'partido_politico.id as partido_politico_id',
                'partido_politico.nombre',
                'acta.total_acta'
            );
        if (!is_null($departamento_id)) {
            $acta->where('centro_votacion.departamento_id', $departamento_id);
        }
        if (!is_null($provincia_id)) {
            $acta->where('centro_votacion.provincia_id', $provincia_id);
        }
        if (!is_null($distrito_id)) {
            $acta->where('centro_votacion.distrito', $distrito_id);
        }
        $results = $acta->get();
        $agrupados = $results->groupBy('nombre')->map(function ($group, $key) {
            if ($key === 'Voto en Nulo' || $key === 'Voto en Blanco') {
                return [
                    'nombre' => $key,
                    'total' => $group->sum('total_acta'),
                ];
            }
        })->filter();

        $totalVotosValidos = $results->whereNotIn('nombre', ['Voto en Nulo', 'Voto en Blanco'])->sum('total_acta');

        $agrupados->push([
            'nombre' => 'Votos Validos',
            'total' => $totalVotosValidos,
        ]);

        return $agrupados->values();
    }

    public function reporteTotalVotos(Request $request)
    {
        /*  $result = PartidoPolitico::select(
            'partido_politico.nombre',
            'partido_politico.partido_politico',
            'partido_politico.logo',
            'partido_politico.orden',
            'partido_politico.color',
            DB::raw('sum(acta.total_acta) as suma')
        )
            ->join('acta', 'partido_politico.id', '=', 'acta.partida_politica_id')
            ->orderBy('partido_politico.orden', 'asc')
            ->groupBy('partido_politico.nombre', 'partido_politico.partido_politico', 'partido_politico.logo', 'partido_politico.orden', 'acta.partida_politica_id', 'partido_politico.color')
            ->where('partido_politico.estado', 1)
            ->get();

        $modifiedResults = $result->map(function ($item) {
            if (isset($item->logo)) {
                $item->logo = Storage::url($item->logo);
            }
            return $item;
        });

        return response()->json($modifiedResults); */



        $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
        $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
        $distrito_id = empty($request->districts_id) ? null : $request->districts_id;

        $query = PartidoPolitico::select(
            'partido_politico.nombre',
            'partido_politico.partido_politico',
            'partido_politico.logo',
            'partido_politico.orden',
            'partido_politico.color',
            'partido_politico.id',
            DB::raw('SUM(acta.total_acta) as suma'),
            DB::raw('SUM(CASE WHEN partido_politico.id NOT IN (4, 5) THEN acta.total_acta ELSE 0 END) as suma_excluyendo_4_5'),
           
            DB::raw("(SELECT SUM(acta.total_acta)
                 FROM acta
                 JOIN partido_politico pp ON acta.partida_politica_id = pp.id
                 JOIN centro_votacion cv ON acta.centro_votacion_id = cv.id
                 WHERE pp.estado = 1
                   AND pp.id NOT IN (4, 5)
                   AND (cv.departamento_id = '$departamento_id' OR '$departamento_id' IS NULL OR '$departamento_id' = '')
                   AND (cv.provincia_id = '$provincia_id' OR '$provincia_id' IS NULL OR '$provincia_id' = '')
                   AND (cv.distrito = '$distrito_id' OR '$distrito_id' IS NULL OR '$distrito_id' = '')
                ) AS suma_excluyendo_4_5"),
                DB::raw("(SELECT SUM(acta.total_acta)
                FROM acta
                JOIN partido_politico pp ON acta.partida_politica_id = pp.id
                JOIN centro_votacion cv ON acta.centro_votacion_id = cv.id
                WHERE pp.estado = 1
                  
                  AND (cv.departamento_id = '$departamento_id' OR '$departamento_id' IS NULL OR '$departamento_id' = '')
                  AND (cv.provincia_id = '$provincia_id' OR '$provincia_id' IS NULL OR '$provincia_id' = '')
                  AND (cv.distrito = '$distrito_id' OR '$distrito_id' IS NULL OR '$distrito_id' = '')
               ) AS suma_total"),

        )



            ->join('acta', 'partido_politico.id', '=', 'acta.partida_politica_id')
            ->join('centro_votacion', 'acta.centro_votacion_id', '=', 'centro_votacion.id')
            ->where('partido_politico.estado', 1);
        if (!is_null($departamento_id)) {
            $query->where('centro_votacion.departamento_id', $departamento_id);
        }
        if (!is_null($provincia_id)) {
            $query->where('centro_votacion.provincia_id', $provincia_id);
        }
        if (!is_null($distrito_id)) {
            $query->where('centro_votacion.distrito', $distrito_id);
        }
        $result = $query->groupBy(
            'partido_politico.nombre',
            'partido_politico.partido_politico',
            'partido_politico.logo',
            'partido_politico.orden',
            'acta.partida_politica_id',
            'partido_politico.color',
            'partido_politico.id'

        )
            ->orderBy('partido_politico.orden', 'ASC')
            ->get();

        $modifiedResults = $result->map(function ($item) {
            if (isset($item->logo)) {
                $item->logo = Storage::url($item->logo);
            }
            return $item;
        });
        return response()->json($modifiedResults);
    }

    public function reporteVivo()
    {
        //[)G=T3,nm6M*
        $menusPrin = $this->getMenus();
        return view('page/dashboard.reporteVivo', compact('menusPrin'));
    }


    public function sumReporteTotalVotosPanel(Request $request)
    {

        $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
        $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
        $distrito_id = empty($request->districts_id) ? null : $request->districts_id;

        //  $query = PartidoPolitico::select(
        //      'partido_politico.nombre',
        //      'partido_politico.partido_politico',
        //      'partido_politico.logo',
        //      'partido_politico.orden',
        //      'partido_politico.color',
        //      DB::raw('SUM(acta.total_acta) as suma')
        //  )
        //  ->join('acta', 'partido_politico.id', '=', 'acta.partida_politica_id')
        //  ->join('centro_votacion', 'acta.centro_votacion_id', '=', 'centro_votacion.id')
        //  ->where('partido_politico.estado', 1);
        //  if (!is_null($departamento_id)) {
        //      $query->where('centro_votacion.departamento_id', $departamento_id);
        //  }
        //  if (!is_null($provincia_id)) {
        //      $query->where('centro_votacion.provincia_id', $provincia_id);
        //  }
        //  if (!is_null($distrito_id)) {
        //      $query->where('centro_votacion.distrito', $distrito_id);
        //  }
        //  $result = $query->groupBy('partido_politico.nombre', 
        //                              'partido_politico.partido_politico',
        //                              'partido_politico.logo', 
        //                              'partido_politico.orden', 
        //                              'acta.partida_politica_id', 
        //                              'partido_politico.color')
        //      ->orderBy('partido_politico.orden', 'ASC')
        //      ->get();

        $query = PartidoPolitico::select(
            DB::raw('SUM(acta.total_acta) as suma')
        )
            ->join('acta', 'partido_politico.id', '=', 'acta.partida_politica_id')
            ->join('centro_votacion', 'acta.centro_votacion_id', '=', 'centro_votacion.id')
            ->where('partido_politico.estado', 1);

        if (!is_null($departamento_id)) {
            $query->where('centro_votacion.departamento_id', $departamento_id);
        }
        if (!is_null($provincia_id)) {
            $query->where('centro_votacion.provincia_id', $provincia_id);
        }
        if (!is_null($distrito_id)) {
            $query->where('centro_votacion.distrito', $distrito_id);
        }

        $result = $query->first();

        return response()->json($result);
    }


    public function sumReporteTotalActasPanel(Request $request)
    {

        $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
        $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
        $distrito_id = empty($request->districts_id) ? null : $request->districts_id;


        $query = Mesa::select(
            DB::raw('COUNT(mesa.id) as cantidad_actas')
        )

            ->join('centro_votacion', 'centro_votacion.id', '=', 'mesa.centro_votacion_id')
            // ->select('mesa.estado')
            // ->join('acta', 'partido_politico.id', '=', 'acta.partida_politica_id')
            // ->join('centro_votacion', 'acta.centro_votacion_id', '=', 'centro_votacion.id')
            // ->where('mesa.estado', 1)
        ;

        if (!is_null($departamento_id)) {
            $query->where('centro_votacion.departamento_id', $departamento_id);
        }
        if (!is_null($provincia_id)) {
            $query->where('centro_votacion.provincia_id', $provincia_id);
        }
        if (!is_null($distrito_id)) {
            $query->where('centro_votacion.distrito', $distrito_id);
        }

        $result = $query->first();

        return response()->json($result);
    }


    public function sumReporteTotalActasProcesadasPanel(Request $request)
    {

        $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
        $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
        $distrito_id = empty($request->districts_id) ? null : $request->districts_id;


        $query = Mesa::select(
            DB::raw('COUNT(mesa.id) as cantidad_actas')
        )

            ->join('centro_votacion', 'centro_votacion.id', '=', 'mesa.centro_votacion_id')
            // ->select('mesa.estado')
            // ->join('acta', 'partido_politico.id', '=', 'acta.partida_politica_id')
            // ->join('centro_votacion', 'acta.centro_votacion_id', '=', 'centro_votacion.id')
            ->where('mesa.estado', 1);

        if (!is_null($departamento_id)) {
            $query->where('centro_votacion.departamento_id', $departamento_id);
        }
        if (!is_null($provincia_id)) {
            $query->where('centro_votacion.provincia_id', $provincia_id);
        }
        if (!is_null($distrito_id)) {
            $query->where('centro_votacion.distrito', $distrito_id);
        }

        $result = $query->first();

        return response()->json($result);
    }
}
