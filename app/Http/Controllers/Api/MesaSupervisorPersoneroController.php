<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListadoPartidosPoliticos;
use App\Http\Resources\ListadoPartidosPoliticosResource;
use App\Models\Acta;
use App\Models\MesaPersonero;
use App\Models\PartidoPolitico;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MesaSupervisorPersoneroController extends Controller
{



    public function guardarPersoneroMesa(Request $request)
    {



        $numero_documento_input = $request->input('numero_documento');
        $nombre_input = $request->input('nombre');
        $apellido_paterno_input = $request->input('apellido_paterno');
        $apellido_materno_input = $request->input('apellido_materno');
        $celular_input = $request->input('celular');
        $email_input = $request->input('email');
        $password_input = $request->input('password');
        $mesa_id_input = $request->input('mesa_id');




        $persona = new Person();
        $persona->tipo_persona_id = 1;
        $persona->numero_documento = $numero_documento_input;
        $persona->tipo_documento_id = 1;
        $persona->nombre = $nombre_input;
        $persona->apellido_paterno = $apellido_paterno_input;
        $persona->apellido_materno = $apellido_materno_input;
        $persona->sexo = 1;
        $persona->codigo_pais = '+51';
        $persona->celular = $celular_input;
        $persona->email = $email_input;
        $persona->departmento_id = 1;
        $persona->provincia_id = 1;
        $persona->distrito_id = 1;
        $persona->corregimiento_id = 1;
        $persona->save();

        $users = new User();
        $users->perfil_id = 3;
        $users->persona_id = $persona->id;
        $users->email = $email_input;
        $users->numero_celular = $celular_input;
        $users->password = Hash::make($password_input);
        $users->save();



        $mesa_personero = new MesaPersonero();
        $users->personero_id = $users->id;
        $users->mesa_id = $mesa_id_input;
        $mesa_personero->save();


        if ($mesa_personero->save()) {
            $mesa_personero_id = $mesa_personero->id;
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'La informacíon se guardo correctamente'
            ]);
        }
    }


    public function listaMesasPersonero()
    {
        $user = auth('api')->user();
        $usuario_id = $user->id;
        // $user = auth('api')->user();
        // $usuario_id = 4;
        $mesas_personero = MesaPersonero::join('mesa', 'mesa.id', '=', 'personero_mesa.mesa_id')
        ->join('centro_votacion', 'centro_votacion.id', '=', 'mesa.centro_votacion_id')
            ->select(
                'personero_mesa.id',
                'mesa.id as mesa_id',
                'mesa.nombre as mesa_nombre',
                'mesa.numero as mesa_numero',
                'centro_votacion.id as centro_votacion_id',

                'centro_votacion.nombre as centro_votacion_nombre',
                'centro_votacion.direccion as centro_votacion_direccion',


            )->where('personero_id', $usuario_id)->get();

        return response()->json($mesas_personero);
    }


    
    // public function guardarMesaActa(Request $request){


    //     $mesa_id_input = $request->input('mesa_id');
    //     $centro_votacion_id_input = $request->input('centro_votacion_id');
    //     $partida_politica_id_input = $request->input('partida_politica_id');
    //     $total_acta_input = $request->input('total_acta');
    //     $personero_id_input = $request->input('personero_id');








    // }
    public function guardarMesaActa(Request $request){
        $user = auth('api')->user();
        $usuario_id = $user->id; //personero id usuario logeado
        // Obtener todos los datos con el mismo nombre de clave
        $datos = $request->input('datos');
    
        // Recorrer los datos y procesarlos
        foreach ($datos as $dato) {
            // Crear una nueva instancia del modelo y asignar los datos
            $modelo = new Acta([
                'mesa_id' => $dato['mesa_id'],
                'centro_votacion_id' => $dato['centro_votacion_id'],
                'partida_politica_id' => $dato['partida_politica_id'],
                'total_acta' => $dato['total_acta'],
                'personero_id' => $dato['personero_id'],
                // Puedes añadir más campos aquí según sea necesario
            ]);
            // Guardar la instancia del modelo
            $modelo->save();
        }
    
        // Aquí podrías devolver una respuesta adecuada, por ejemplo, un mensaje de éxito
        return response()->json(['mensaje' => 'Datos guardados correctamente']);
    }
    
}
