<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Acta;
use App\Models\Mesa;
use App\Models\PartidoPolitico;
use App\Traits\Acces;
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

    public function reportePartidoPolTotal()
    {
        $result = PartidoPolitico::select(
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

        return response()->json($modifiedResults);
    }

    public function reporteEstadoActa()
    {
        $estadoActas = Mesa::select('estado')->get();

        $conteoEstados = $estadoActas->map(function ($item) {
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

    public function reporteDistribucionVotos()
    {
        $acta = Acta::join('partido_politico', 'partido_politico.id', '=', 'acta.partida_politica_id')
            ->select(
                'partido_politico.id as partido_politico_id',
                'partido_politico.nombre',
                'acta.total_acta'
            )
            ->get();

        $agrupados = $acta->groupBy('nombre')->map(function ($group, $key) {
            if ($key === 'Voto en Nulo' || $key === 'Voto en Blanco') {
                return [
                    'nombre' => $key,
                    'total' => $group->sum('total_acta'),
                ];
            }
        })->filter();

        $totalVotosValidos = $acta->whereNotIn('nombre', ['Voto en Nulo', 'Voto en Blanco'])->sum('total_acta');

        $agrupados->push([
            'nombre' => 'Votos Validos',
            'total' => $totalVotosValidos,
        ]);

        return $agrupados->values();
    }

    public function reporteTotalVotos(){
        $result = PartidoPolitico::select(
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

        return response()->json($modifiedResults);
    }
}
