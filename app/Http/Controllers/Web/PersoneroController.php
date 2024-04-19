<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserValidateRegister;
use App\Http\Requests\UserValidateUpdate;
use App\Models\CentroVotacion;
use App\Models\Mesa;
use App\Models\MesaPersonero;
use App\Models\Perfil;
use App\Models\Person;
use App\Models\User;
use App\Traits\Acces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PersoneroController extends Controller
{
    use Acces;
    public function index(){
        $menusPrin = $this->getMenus();
        return view('page.personero',compact('menusPrin'));
    }

    public function getListUsers(Request $request)
    {
        $query = $request->input('q');
        $pageSize = $request->input('pageSize', 10);

        $users = User::join('personas', 'personas.id', '=', 'users.persona_id')
            ->join('perfiles', 'perfiles.id', '=', 'users.perfil_id')
            ->select(
                'users.id',
                'users.isActive',
                'users.email',
                'users.numero_celular',
                'personas.id as persona_id',
                'personas.numero_documento',
                'personas.nombre as persona_nombre',
                'personas.apellido_paterno',
                'personas.apellido_materno',
                'perfiles.id as perfiles_id',
                'perfiles.nombre as perfiles_nombre'
            )
            ->where('users.sub_usuario', Auth::user()->id)
            ->where('users.perfil_id', 3);

        if (!empty($query)) {
            $users->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('personas.numero_documento', 'like', '%' . $query . '%')
                    ->orWhere('personas.nombre', 'like', '%' . $query . '%')
                    ->orWhere('personas.apellido_paterno', 'like', '%' . $query . '%')
                    ->orWhere('personas.apellido_materno', 'like', '%' . $query . '%');
            });
        }

        $users = $users->orderBy('users.id', 'desc')
            ->paginate($pageSize);
        return response()->json($users);
    }

    public function getPersona(Request $request)
    {
        $numero_documento = $request->input('params');
        $persona = Person::select('id', 'numero_documento', 'email', 'codigo_pais', 'celular', 'nombre', 'apellido_paterno', 'apellido_materno')->where('numero_documento', $numero_documento)->first();
        if ($persona) {
            return response()->json(['status' => true, 'data' => $persona]);
        } else {
            return response()->json(['status' => false, 'data' => '']);
        }
    }

    public function postShowUser(Request $request)
    {
        $query = $request->input('params');
        $user = User::join('personas', 'personas.id', '=', 'users.persona_id')
            ->join('perfiles', 'perfiles.id', '=', 'users.perfil_id')
            ->select(
                'users.id',
                'personas.nombre',
                'personas.id as personas_id',
                'personas.apellido_paterno',
                'personas.apellido_materno',
                'personas.numero_documento',
                'personas.codigo_pais',
                'personas.celular',
                'users.email',
                'personas.estado',
                'perfiles.id as perfiles_id',
                'perfiles.nombre as perfiles_nombres'
            )
            ->where('users.id', '=', $query)->first();
        return response()->json($user);
    }

    public function agregarUser(UserValidateRegister $request)
    {
        try {
            DB::beginTransaction();
            $personRes = User::where('email', '=', $request->user_name)->first();
            if ($personRes) {
                $person_id = $personRes->id;
                $person =  User::where('email', '=', $request->user_name)->first();
            } else {
                $person = new User();
            }
            $person->perfil_id = $request->profile_id;
            $person->persona_id = $request->person_id;
            $person->sub_usuario = Auth::user()->id;
            $person->email = e(strtoupper(trim($request->user_name)));
            $person->password = Hash::make($request->password);
            $person->numero_celular = e(strtoupper(trim($request->celular)));
            if ($person->save()) {
                $person_id = $person->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }

    public function postUpdateUser(UserValidateUpdate $request)
    {
        try {
            DB::beginTransaction();

            $existingPerson = User::where('email', '=', $request->user_name)
                ->where('users.id', '!=', $request->id)
                ->first();

            if (!empty($existingPerson)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Correo electrónico ya está en uso por otro usuario.'
                ]);
            } else {
                $person = User::where('id', '=', $request->id)->first();
                $person->perfil_id = $request->profile_id;
                $person->sub_usuario = Auth::user()->id;
                $person->persona_id = $request->person_id;
                $person->email = e(strtoupper(trim($request->user_name)));
                $person->password = Hash::make($request->password);
                $person->numero_celular = e(strtoupper(trim($request->celular)));
                if ($person->save()) {
                    $person_id = $person->id;
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' => 'La informacíon se guardó correctamente'
                    ]);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }

    public function postDeleteUser(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            $user->isActive = 0;
            if ($user->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function postActiveUser(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            $user->isActive = 1;
            if ($user->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getListProfile()
    {
        try {
            $profile = Perfil::where('status', '=', 1)->get();
            return response()->json($profile);
        } catch (\Throwable $th) {
            throw $th;
        }
    }




    public function getListMesasPersoneros(Request $request)
    {

        $persona_id = $request->input('persona_id');
        $provincia_id = $request->input('provincia_id');
        $distrito_id = $request->input('distrito_id');
        $centro_id = $request->input('centro_id');

        $mesas = Mesa::select('mesa.nombre', 'mesa.id')
        ->join('centro_votacion', 'centro_votacion.id', '=', 'mesa.centro_votacion_id');

        if ($provincia_id) {
            // Agregar una condición WHERE si se proporciona el parámetro persona_id
            $mesas->where('centro_votacion.provincia_id', $provincia_id);
        }
        if ($distrito_id) {
            // Agregar una condición WHERE si se proporciona el parámetro persona_id
            $mesas->where('centro_votacion.distrito', $distrito_id);
        }
        if ($centro_id) {
            // Agregar una condición WHERE si se proporciona el parámetro persona_id
            $mesas->where('mesa.centro_votacion_id', $centro_id);
        }

        // Filtrando las mesas donde la persona no esté registrada
        $mesas->leftJoin('personero_mesa', function ($join) use ($persona_id) {
            $join->on('mesa.id', '=', 'personero_mesa.mesa_id')
                ->where('personero_mesa.personero_id', '=', $persona_id);
        })
            ->whereNull('personero_mesa.mesa_id');

        $mesas = $mesas->get();

        return response()->json($mesas);
    }


    public function getListCentroVotacion(Request $request)
    {

        $provincia_id = $request->input('provincia_id');
        $distrito_id = $request->input('distrito_id');


        $centros =  CentroVotacion::query();
        if ($provincia_id) {
            // Agregar una condición WHERE si se proporciona el parámetro persona_id
            $centros->where('provincia_id', $provincia_id);
        }
        if ($distrito_id) {
            // Agregar una condición WHERE si se proporciona el parámetro persona_id
            $centros->where('distrito', $distrito_id);
        }


        $centros = $centros->get();
        return response()->json($centros);
    }

    public function agregarMesaPersonero(Request $request)
    {
        try {
            DB::beginTransaction();


            $personero_id_input = $request->input('id');
            $mesa_id_input = $request->input('mesa_id');


            $mesa_personero = new MesaPersonero();
            $mesa_personero->personero_id = $personero_id_input;
            $mesa_personero->mesa_id = $mesa_id_input;

            if ($mesa_personero->save()) {
                $mesa_personero_id = $mesa_personero->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }


    public function listMesaPersonero(Request $request)
    {

        $pageSize = $request->input('pageSize', 10);

        $persona_id = $request->input('persona_id');

        $mesa_personeros = MesaPersonero::select(
            'mesa.nombre',
            'mesa.numero',
            'mesa.centro_votacion_id',
            'personero_mesa.estado as estado_mesa_personero',
            'personero_mesa.id as id_mesa_personero',

        )
            ->leftjoin('users', 'personero_mesa.personero_id', '=', 'users.id')
            ->leftjoin('mesa', 'personero_mesa.mesa_id', '=', 'mesa.id')
            ->leftjoin('personas', 'personas.id', '=', 'users.persona_id')
            ->where('personero_mesa.personero_id', $persona_id)
            ->orderBy('personero_mesa.id', 'desc')->paginate($pageSize);

        return response()->json($mesa_personeros);
    }


    public function postDeleteMesaPersonero(Request $request)
    {
        try {
            $mesa_personero = MesaPersonero::find($request->id);
            $mesa_personero->estado = 0;
            if ($mesa_personero->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function postActiveMesaPersonero(Request $request)
    {
        try {
            $mesa_personero = MesaPersonero::findOrFail($request->id);
            $mesa_personero->estado = 1;
            if ($mesa_personero->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
