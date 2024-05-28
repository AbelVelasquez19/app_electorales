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


    public function reportePartidoPolTotalVivo()
    {
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
