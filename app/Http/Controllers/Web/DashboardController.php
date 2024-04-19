<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PartidoPolitico;
use App\Traits\Acces;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    use Acces;
    public function index(){
        $menusPrin = $this->getMenus();
        return view('page.index',compact('menusPrin'));
    }

    public function reportePartidoPolTotal(){
        $result = PartidoPolitico::select('partido_politico.nombre', 'partido_politico.partido_politico', 'partido_politico.logo', 'partido_politico.orden','partido_politico.color', 
            DB::raw('sum(acta.total_acta) as suma'))
            ->join('acta', 'partido_politico.id', '=', 'acta.partida_politica_id')
            ->orderBy('partido_politico.orden','asc')
            ->groupBy('partido_politico.nombre', 'partido_politico.partido_politico', 'partido_politico.logo', 'partido_politico.orden', 'acta.partida_politica_id','partido_politico.color')
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
