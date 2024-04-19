<?php

namespace App\Http\Controllers\Web;

use App\Enums\MensageResult;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonValidateRegister;
use App\Http\Requests\PersonValidateUpdate;
use App\Models\Person;
use App\Traits\Acces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    use Acces;
    public function index(){
        $menusPrin = $this->getMenus();
        return view('page.persona', compact('menusPrin'));
    }

    public function getListUsers(Request $request){
        $query = $request->input('q');
        
        $pageSize = $request->input('pageSize', 10);

        $users = Person::select('id',
                     'nombre',
                     'apellido_paterno',
                     'apellido_materno',
                     'numero_documento',
                     'estado'
            )
            ->where('numero_documento', 'like', "%$query%")
            ->orWhere('nombre', 'like', "%$query%")
            ->orWhere('apellido_paterno', 'like', "%$query%")
            ->orWhere('apellido_materno', 'like', "%$query%")
            ->orderBy('id', 'desc')
            ->paginate($pageSize);
        return response()->json($users);
    }

    public function postShowUser(Request $request){
        $query = $request->input('params');
        $user = Person::select('id',
                        'nombre',
                        'apellido_paterno',
                        'apellido_materno',
                        'numero_documento',
                        'sexo',
                        'codigo_pais',
                        'celular',
                        'email',
                        'direccion',
                        'provincia_id',
                        'distrito_id', 
                        'corregimiento_id',
                        'estado'
            )
        ->where('id','=',$query)->first();
        return response()->json($user);
    }

    public function agregarPersona(PersonValidateRegister $request){
        try {
            DB::beginTransaction();
            $personRes = Person::where('numero_documento','=',$request->numero_documento)->first();
            if($personRes){
                $person_id = $personRes->id;
                $person =  Person::where('numero_documento','=',$request->numero_documento)->first();
            }else{
                $person = new Person();
            }
                $person->tipo_persona_id=1;
                $person->tipo_documento_id=1;
                $person->numero_documento = e(strtoupper(trim($request->numero_documento)));
                $person->nombre = e(strtoupper(trim($request->nombre)));
                $person->apellido_paterno = e(strtoupper(trim($request->apellido_paterno)));
                $person->apellido_materno = e(strtoupper(trim($request->apellido_materno)));
                $person->sexo = e(strtoupper(trim($request->sexo)));
                $person->codigo_pais = e(strtoupper('+'.trim($request->codigo_pais_id)));
                $person->celular = e(strtoupper(trim($request->celular)));
                $person->email = e(strtoupper(trim($request->email)));
                $person->direccion = e(strtoupper(trim($request->direccion)));
                $person->provincia_id = e(strtoupper(trim($request->provincia_id)));
                $person->distrito_id = e(strtoupper(trim($request->distrito_id)));
                $person->corregimiento_id = e(strtoupper(trim($request->corregimiento_id)));
                $person->user_id = Auth::user()->id ;
                if($person->save()){
                    $person_id = $person->id;
                    DB::commit();
                    return response()->json([
                        'status'=>true,
                        'message'=>'La informacíon se guardó correctamente'
                    ]);
                }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'=>false,
                'message'=>'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }

    public function postUpdateUser(PersonValidateUpdate $request){
        try {
            DB::beginTransaction();

            $existingPerson = Person::where('numero_documento', '=', $request->numero_documento)
            ->where('personas.id', '!=', $request->id)
            ->first();
            
            if (!empty($existingPerson)) {
                return response()->json([
                    'status' => false,
                    'message' => 'El número de documento ya está en uso por otro usuario.'
                ]);
            }else{
                $person = Person::where('id','=',$request->id)->first();
                $person->tipo_persona_id=1;
                $person->tipo_documento_id=1;
                $person->numero_documento = e(strtoupper(trim($request->numero_documento)));
                $person->nombre = e(strtoupper(trim($request->nombre)));
                $person->apellido_paterno = e(strtoupper(trim($request->apellido_paterno)));
                $person->apellido_materno = e(strtoupper(trim($request->apellido_materno)));
                $person->sexo = e(strtoupper(trim($request->sexo)));
                $person->codigo_pais = e(strtoupper('+'.trim($request->codigo_pais_id)));
                $person->celular = e(strtoupper(trim($request->celular)));
                $person->email = e(strtoupper(trim($request->email)));
                $person->direccion = e(strtoupper(trim($request->direccion)));
                $person->provincia_id = e(strtoupper(trim($request->provincia_id)));
                $person->distrito_id = e(strtoupper(trim($request->distrito_id)));
                $person->corregimiento_id = e(strtoupper(trim($request->corregimiento_id)));
                $person->user_id = Auth::user()->id ;
                if($person->save()){
                    $person_id = $person->id;
                    DB::commit();
                    return response()->json([
                        'status'=>true,
                        'message'=>'La informacíon se guardó correctamente'
                    ]);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'=>false,
                'message'=>'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }

    public function postDeleteUser(Request $request){
        try {
            $user = Person::findOrFail($request->id);
            $user->estado = 0;
            if($user->save()){
                return response()->json([
                    'status'=>true,
                    'message'=>'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function postActiveUser(Request $request){
        try {
            $user = Person::findOrFail($request->id);
            $user->estado = 1;
            if($user->save()){
                return response()->json([
                    'status'=>true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
