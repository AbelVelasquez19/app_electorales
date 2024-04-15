<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CodigoPais;
use App\Models\Corregimient;
use App\Models\Departament;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;

class UbigeoController extends Controller
{
    public function department(){
        $department = Departament::get();
        return response()->json($department);
    }

    public function province(){
        $province = Province::get();
        return response()->json($province);
    }

    public function district(Request $request){
        $province_id = $request->input('params');
        $district = District::
                    where(function($result) use ($province_id){
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
