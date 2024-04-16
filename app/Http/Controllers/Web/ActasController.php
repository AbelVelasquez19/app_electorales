<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PartidoPolitico;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
}
