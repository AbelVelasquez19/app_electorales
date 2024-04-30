<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CodigoPais;
use App\Models\Corregimient;
use App\Models\Departament;
use App\Models\District;
use App\Models\Pais;
use App\Models\Province;
use Illuminate\Http\Request;

class UbigeoController extends Controller
{
    public function pais(){
        $pais = Pais::where('estado',1)->get();
        return response()->json($pais);
    }

    public function department(Request $request){
        $pais_id = $request->input('params');
        $department = Departament::where('estado',1)->where('pais_id',$pais_id)->get();
        return response()->json($department);
    }

    public function province(Request $request){
        $department_id = $request->input('params');
        $province = Province::where('estado',1)->where('departmento_id',$department_id)->get();
        return response()->json($province);
    }

    public function district(Request $request){
        $province_id = $request->input('params');
        $district = District::where(function($result) use ($province_id){
                        $result->where('provincia_id','=',$province_id);
                    })
                    ->get();
        return response()->json($district);
    }

    public function coregimient(Request $request){
        $district_id = $request->input('params');
        $district = Corregimient::
                    where(function($result) use ($district_id){
                        $result->where('distrito_id','=',$district_id);
                    })
                    ->get();
        return response()->json($district);
    }

    public function codigoPais(){
        $codigoPais = CodigoPais::get();
        return response()->json($codigoPais);
    }
}
