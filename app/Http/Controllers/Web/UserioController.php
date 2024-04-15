<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserValidateRegister;
use App\Http\Requests\UserValidateUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserioController extends Controller
{
    public function index(){
        return view('page.user');
    }

    public function getListUsers(Request $request){
        $query = $request->input('q');
        
        $pageSize = $request->input('pageSize', 10);

        $users = User::join('personas','personas.id','=','users.persona_id')
        ->join('perfiles','perfiles.id','=','users.perfil_id')
        ->select('users.id as user_id',
                 'users.isActive',
                 'users.email',
                 'users.numero_celular',
                 'personas.id as persona_id',
                 'personas.numero_documento',
                 'personas.nombre as persona_nombre',
                 'personas.apellido_paterno',
                 'personas.apellido_paterno',
                 'perfiles.id as perfiles_id',
                 'perfiles.nombre as perfiles_nombre',
        )
            ->where('personas.numero_documento', 'like', "%$query%")
            ->orWhere('personas.nombre', 'like', "%$query%")
            ->orWhere('personas.apellido_paterno', 'like', "%$query%")
            ->orWhere('personas.apellido_materno', 'like', "%$query%")
            ->orderBy('users.id', 'desc')
            ->paginate($pageSize);
        return response()->json($users);
    }

    public function postShowUser(Request $request){
        $query = $request->input('params');
        $user = User::
                    join('personas','personas.id','=','users.persona_id')
                    ->join('perfiles','perfiles.id','=','users.perfil_id')
                    ->select('users.id',
                        'personas.nombre',
                        'personas.id as personas_id',
                        'personas.apellido_paterno',
                        'personas.apellido_materno',
                        'personas.numero_documento',
                        'personas.codigo_pais',
                        'personas.celular',
                        'personas.email',
                        'personas.estado',
                        'perfiles.id as perfiles_id',
                        'perfiles.nombre as perfiles_nombres'
            )
        ->where('users.id','=',$query)->first();
        return response()->json($user);
    }

    public function agregarUser(UserValidateRegister $request){
        try {
            DB::beginTransaction();
            $personRes = User::where('email','=',$request->email)->first();
            if($personRes){
                $person_id = $personRes->id;
                $person =  User::where('email','=',$request->email)->first();
            }else{
                $person = new User();
            }
                $person->perfil_id=$request->perfil_id;
                $person->persona_id=$request->persona_id;
                $person->email = e(strtoupper(trim($request->email)));
                $person->password = Hash::make($request->password);
                $person->numero_celular = e(strtoupper(trim($request->numero_celular)));
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

    public function postUpdateUser(UserValidateUpdate $request){
        try {
            DB::beginTransaction();

            $existingPerson = User::where('email', '=', $request->email)
            ->where('users.id', '!=', $request->id)
            ->first();
            
            if (!empty($existingPerson)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Correo electrónico ya está en uso por otro usuario.'
                ]);
            }else{
                $person = User::where('id','=',$request->id)->first();
                $person->perfil_id=$request->perfil_id;
                $person->persona_id=$request->persona_id;
                $person->email = e(strtoupper(trim($request->email)));
                $person->password = Hash::make($request->password);
                $person->numero_celular = e(strtoupper(trim($request->numero_celular)));
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
            $user = User::findOrFail($request->id);
            $user->isActive = 0;
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
            $user = User::findOrFail($request->id);
            $user->isActive = 1;
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
