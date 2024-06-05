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
        $result = $query->groupBy('partido_politico.nombre', 
                                    'partido_politico.partido_politico',
                                    'partido_politico.logo', 
                                    'partido_politico.orden', 
                                    'acta.partida_politica_id', 
                                    'partido_politico.color')
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

        $estadoActas = Mesa::join('centro_votacion','centro_votacion.id','=','mesa.centro_votacion_id')
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

    public function reporteDistribucionVotos(Request $request){
        $departamento_id = empty($request->departaments_id) ? null : $request->departaments_id;
        $provincia_id = empty($request->provinces_id) ? null : $request->provinces_id;
        $distrito_id = empty($request->districts_id) ? null : $request->districts_id;

        $acta = Acta::join('partido_politico', 'partido_politico.id', '=', 'acta.partida_politica_id')
            ->join('centro_votacion','centro_votacion.id','=','acta.centro_votacion_id')
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

    public function reporteTotalVotos(Request $request){
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
        $result = $query->groupBy('partido_politico.nombre', 
                                    'partido_politico.partido_politico',
                                    'partido_politico.logo', 
                                    'partido_politico.orden', 
                                    'acta.partida_politica_id', 
                                    'partido_politico.color')
            ->orderBy('partido_politico.orden', 'ASC')
            ->get();

        $modifiedResults = $result->map(function ($item) {
            if (isset($item->logo)) {
                $item->logo = Storage::url($item->logo);
            }
            return $item;
        });

        $filteredResults = $modifiedResults->filter(function ($item) {
            return $item->nombre !== 'Voto en Nulo' && $item->nombre !== 'Voto en Blanco';
        });

        $filteredResultsBlnco = $modifiedResults->filter(function ($item) {
            return $item->nombre === 'Voto en Blanco';
        });

        $filteredResultsNulos = $modifiedResults->filter(function ($item) {
            return $item->nombre === 'Voto en Nulo';
        });

        $totalSum = $filteredResults->sum('suma');
        $totalSumBlanco = $filteredResultsBlnco->sum('suma');
        $totalSumNulo = $filteredResultsNulos->sum('suma');
        $total_votos_emitidos = ($totalSum+$totalSumBlanco+$totalSumNulo);

        $modifiedResults = $filteredResults->map(function ($item) use ($totalSum, $total_votos_emitidos) {
            $item->porcentaje_validos = ($totalSum > 0) ? number_format(($item->suma / $totalSum) * 100,3) : 0;
            $item->porcentaje_emitidos = ($total_votos_emitidos > 0) ? number_format(($item->suma / $total_votos_emitidos) * 100,3) : 0;
            return $item;
        });

        // Calcular la suma de los porcentajes de votos válidos
        $totalPorcentajeValidos = $filteredResults->sum('porcentaje_validos');
        $totalPorcentajeEmitidos = ($total_votos_emitidos > 0) ? number_format(($totalSum / $total_votos_emitidos) * 100, 3) : 0;

        // Agregar el item adicional
        $totalVotosValidos = [
            'nombre' => 'Total votos válidos',
            'partido_politico' => 'Total votos válidos',
            'logo' => '',
            'orden' => 998,
            'color' => '',
            'suma' => $totalSum,
            'porcentaje_validos' => number_format($totalPorcentajeValidos,3),
            'porcentaje_emitidos' => number_format($totalPorcentajeEmitidos,3)
        ];

        $modifiedResults->push((object)$totalVotosValidos);

         // Calcular la suma de los porcentajes de votos blanco
         $totalPorcentajeblanco = ($total_votos_emitidos > 0) ? number_format(($totalSumBlanco / $total_votos_emitidos) * 100, 3) : 0;
 
        $totalVotosBlanco = [
            'nombre' => 'total votos blanco',
            'partido_politico' => 'total votos blanco',
            'logo' => '',
            'orden' => 999,
            'color' => '',
            'suma' => $totalSumBlanco,
            'porcentaje_validos' => number_format(0,3),
            'porcentaje_emitidos' => number_format($totalPorcentajeblanco,3)
        ];

        $modifiedResults->push((object)$totalVotosBlanco);

         // Calcular la suma de los porcentajes de votos nulo
         $totalPorcentajeNulo = ($total_votos_emitidos > 0) ? number_format(($totalSumNulo / $total_votos_emitidos) * 100, 3) : 0;
 
        $totalVotosNulo = [
            'nombre' => 'total votos nulo',
            'partido_politico' => 'total votos nulo',
            'logo' => '',
            'orden' => 1000,
            'color' => '',
            'suma' => $totalSumNulo,
            'porcentaje_validos' => number_format(0,3),
            'porcentaje_emitidos' => number_format($totalPorcentajeNulo,3)
        ];

        $modifiedResults->push((object)$totalVotosNulo);

        $totalPorcentajeEmitidos = 0;
        foreach ($modifiedResults as  $resultado) {
            if($resultado->nombre!=='Total votos válidos'){
                $totalPorcentajeEmitidos += $resultado->porcentaje_emitidos;
            }
        }

        $totalVotosEmitidos = [
            'nombre' => 'total votos emitidos',
            'partido_politico' => 'total votos emitidos',
            'logo' => '',
            'orden' => 10001,
            'color' => '',
            'suma' => $total_votos_emitidos,
            'porcentaje_validos' => number_format(0,3),
            'porcentaje_emitidos' => number_format($totalPorcentajeEmitidos,3)
        ];

        $modifiedResults->push((object)$totalVotosEmitidos);

        return response()->json($modifiedResults);
    }

    public function reporteGeneral(){
        try {
            $total_votantes = Mesa::sum('total_votantes');
            $participacion_ciudadana = Mesa::sum('cantidad_votantes');
            $porcentaje_participacion_ciudadana = number_format($participacion_ciudadana/$total_votantes*100,3);
            $query = Mesa::select('id','estado')->get();
            $res = $query->map(function($item){
                return [
                    'nombre' => $item->id,
                    'estado' => $item->estado,
                ];
            });
            $groupBy =  $res->groupBy('estado')->map(function($group){
                return [
                    'total'=>$group->count()
                ];
            });
            $total =  (isset($groupBy[0]['total']) ? (int)$groupBy[0]['total'] : 0) + (isset($groupBy[1]['total']) ? (int)$groupBy[1]['total']: 0);
            $actas_procesadas = number_format((isset($groupBy[0]['total']) ? $groupBy[0]['total']:0) /  $total * 100,3);
            return response()->json([
                'status'=>true,
                'total_votantes'=>$total_votantes,
                'participacion_ciudadana'=>$participacion_ciudadana,
                'porcentaje_participacion_ciudadana'=>$porcentaje_participacion_ciudadana,
                'actas_procesadas'=>$actas_procesadas,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //funciones para reporte 2
    public function reporteVivo(){
        //[)G=T3,nm6M*
        $menusPrin = $this->getMenus();
        return view('page/dashboard.reporteVivo',compact('menusPrin'));
    }

    public function electoralesMesasEscrutadas(){
        $mesasEscrutadas = Mesa::get();
        $conteoEstados = $mesasEscrutadas->map(function ($item) {
            $nombre = $item->estado == 0 ? 'TOTAL_VOTOS_ESCRUTADOS' : 'PROCESADAS';
        });
    }

    public function votosEmitidosValidosBlancoNulo(){
        try {
            $total_votantes = Mesa::sum('total_votantes');
            $votos_emitidos = Mesa::sum('cantidad_votantes');
            $votos_valitos = Acta::join('partido_politico', 'acta.partida_politica_id', '=', 'partido_politico.id')
                        ->whereNotIn('partido_politico.partido_politico', ['Voto en Blanco', 'Voto en Nulo'])
                        ->sum('acta.total_acta');
            $votos_blancos = Acta::join('partido_politico', 'acta.partida_politica_id', '=', 'partido_politico.id')
                        ->whereIn('partido_politico.partido_politico', ['Voto en Blanco'])
                        ->sum('acta.total_acta');
            $votos_nulo = Acta::join('partido_politico', 'acta.partida_politica_id', '=', 'partido_politico.id')
                        ->whereIn('partido_politico.partido_politico', ['Voto en Nulo'])
                        ->sum('acta.total_acta');
    
            $votos_emitidos_procentaje = ($votos_emitidos / $total_votantes) * 100;
            $votos_validos_procentaje = ($votos_valitos / $votos_emitidos) * 100;
            $votos_blancos_procentaje = ($votos_blancos / $votos_emitidos) * 100;
            $votos_nulos_procentaje = ($votos_nulo / $votos_emitidos) * 100;
            $for_votos_emitidos_procentaje = number_format($votos_emitidos_procentaje, 2, '.', ',');
            $for_votos_validos_procentaje = number_format($votos_validos_procentaje, 2, '.', ',');
            $for_votos_blancos_procentaje = number_format($votos_blancos_procentaje, 2, '.', ',');
            $for_votos_nulos_procentaje = number_format($votos_nulos_procentaje, 2, '.', ',');
            return response()->json([
                'status'=>true,
                'total_votantes'=>$total_votantes,
                'votos_emitidos'=>$votos_emitidos,
                'for_votos_emitidos_procentaje'=>$for_votos_emitidos_procentaje,
                'votos_valitos'=>$votos_valitos,
                'for_votos_validos_procentaje'=>$for_votos_validos_procentaje,
                'votos_blancos'=>$votos_blancos,
                'for_votos_blancos_procentaje'=>$for_votos_blancos_procentaje,
                'votos_nulo'=>$votos_nulo,
                'for_votos_nulos_procentaje'=>$for_votos_nulos_procentaje,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function reportePartidoPolTotalVivo() {
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
        ->where('partido_politico.estado', 1)
        ->whereNotIn('partido_politico.partido_politico', ['Voto en Blanco', 'Voto en Nulo']);
        $result = $query->groupBy('partido_politico.nombre', 
                                    'partido_politico.partido_politico',
                                    'partido_politico.logo', 
                                    'partido_politico.orden', 
                                    'acta.partida_politica_id', 
                                    'partido_politico.color')
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
}
