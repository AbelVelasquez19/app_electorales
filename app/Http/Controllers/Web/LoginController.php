<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
class LoginController extends Controller
{
    public function index(){
        return view('login.index');
    }
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

    }
    public function login(LoginRequest $request){
        $credentials = $request->only('email', 'password');
        $email = $request->input('email');
        $codeVerify = $request->input('codeVerify');
        $user = User::where('email',$email)->where('isActive',1)->first();
        if($user->codigo_confirm==$codeVerify){
            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status'=>true,
                    'user'=>Auth::user()
                ],200);
            } else {
                return response()->json([
                    'status'=>false,
                    'errors'=>[
                        "message"=>"Usuario y/o password incorrecta .!"
                    ]
                ],500);
            }
        }else{
            return response()->json([
                'status'=>false,
                'errors'=>[
                    "message"=>"Codigo de verificacion es incorrecto"
                ]
            ],500);
        }
    }

    public function logout()
    {
        Auth::logout();  
        return redirect('/');
    }
}
