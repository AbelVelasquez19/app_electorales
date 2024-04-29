<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AutenticacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register','verifyCode']]);
        // $this->middleware('JwtMiddleware', ['except' => ['login', 'register']]); // Reemplaza 'auth:api' con 'JwtMiddleware'

    }


   /*  public function verifyCode(Request $request){
        $credentials = $request->only('email', 'password');

        $code = rand(100000, 999999);
        $sid = getenv("TWILIO_SID");
        $token = getenv("TWILIO_TOKEN");
        $senderNumber = getenv("TWILIO_PHONE");
        $twilio = new Client($sid, $token);

        $email = $request->input('email');

        if (auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales no válidas'], 401);
        }

        $user = User::where('email',$email)->where('isActive',1)->first();
        if($user){
            $user->codigo_confirm = $code;
            if($user->save()){
                $result =$twilio->messages->create($user->numero_celular, // to
                    [
                        "body" => "Su código de confirmación ".$code,
                        "from" =>  $senderNumber
                    ]
                );
                return response()->json([
                    'status'=>true,
                    'mensaje'=>$result
                ],200);
            }
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>[
                    "message"=>"Correo electrónico no se encuentra registrado"
                ]
            ],500);
        }

    } */


    public function verifyCode(LoginRequest $request){
        $code = rand(100000, 999999);
        $sid = getenv("TWILIO_SID");
        $token = getenv("TWILIO_TOKEN");
        $senderNumber = getenv("TWILIO_PHONE");
        $twilio = new Client($sid, $token);

        $email = $request->input('email');
        $user = User::where('email',$email)->where('isActive',1)->first();
        if($user){
            $user->codigo_confirm = $code;
            if($user->save()){
                $result = $twilio->messages->create($user->numero_celular, // to
                    [
                        "body" => "Su código de confirmación ".$code,
                        "from" =>  $senderNumber
                    ]
                );
                return response()->json([
                    'status'=>true,
                    'mensaje'=>$result
                ],200);
            }
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>[
                    "message"=>"Correo electrónico no se encuentra registrado"
                ]
            ],500);
        }

    }



    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $email = $request->input('email');

        try {
            
            $codeVerify = $request->input('codeVerify');
            $user = User::where('email', $email)->where('isActive', 1)->first();
            if ($user->codigo_confirm == $codeVerify) {

                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                /* if (!$token = auth('api')->attempt($credentials)) {
                    return response()->json(['error' => 'Credenciales no válidas'], 401);
                } */

                return $this->respondWithToken($token);

            }else{
                return response()->json([
                    'status'=>false,
                    'errors'=>[
                        "message"=>"Codigo de verificacion es incorrecto"
                    ]
                ],500);
            }




            
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token caducado'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Error al procesar el token'], 500);
        }

    }


    public function me()
    {


        $user = auth('api')->user();

        return response()->json($user);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        $personas = new Persona();

        $personas->tipo_persona_id = 1;
        $personas->tipo_documento_id = 1;
        $personas->numero_documento = 1;
        $personas->nombre = 1;
        $personas->apellido_paterno = 1;
        $personas->apellido_materno = 1;
        $personas->sexo = 1;
        $personas->celular = 1;


        $personas->save();



        // Crear un nuevo usuario
        $user = User::create([
            'perfil_id' => 1,
            'persona_id' => 1,
            'numero_celular' => $request->input('numero_celular'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        // Si el usuario se crea correctamente, generar el token JWT
        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }
}
