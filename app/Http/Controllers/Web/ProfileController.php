<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileValidateAdd;
use App\Http\Requests\ProfileValidateUpdate;
use App\Models\Perfil;
use App\Models\Role;
use App\Traits\Acces;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use Acces;

    public function index(){
        $menusPrin = $this->getMenus();
        return view('page.perfil',compact('menusPrin'));
    }

    public function getListProfile(Request $request){
        $query = $request->input('q');
        $pageSize = $request->input('pageSize', 10);

        $profiles = Perfil::where('nombre', 'like', "%$query%")
            ->orderBy('id', 'desc')
            ->paginate($pageSize);
        return response()->json($profiles);
    }

    public function postShowProfile(Request $request){
        $query = $request->input('params');
        $profile = Perfil::findOrFail($query);
        return response()->json($profile);
    }

    public function postAddProfile(ProfileValidateAdd $request){
        try {
            $profile = new Perfil();
            $profile->nombre = e(trim($request->name));
            $profile->descripcion = e(trim($request->description));
            if($profile->save()){
                return response()->json([
                    'status'=>true,
                    'message'=>'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function postUpdateProfile(ProfileValidateUpdate $request){
        $profile_id = $request->input('id');
        try {
            $profile = Perfil::findOrFail($profile_id);
            $profile->nombre = e(trim($request->name));
            $profile->descripcion = e(trim($request->description));
            if($profile->save()){
                return response()->json([
                    'status'=>true,
                    'message'=>'La informacíon se actualizó correctamente'
                ]);
            }
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function postDeleteProfile(Request $request){
        try {
            $user = Perfil::findOrFail($request->id);
            $user->status = 0;
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

    public function postActiveProfile(Request $request){
        try {
            $user = Perfil::findOrFail($request->id);
            $user->status = 1;
            if($user->save()){
                return response()->json([
                    'status'=>true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    public function getListRoles(){
        try {
            $roles = Role::get();
            return response()->json([
                'status'=>true,
                'result'=>$roles
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
