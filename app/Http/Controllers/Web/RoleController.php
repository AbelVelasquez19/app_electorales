<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleValidateAdd;
use App\Http\Requests\RoleValidateUpdate;
use App\Http\Requests\SubMenuValidateAdd;
use App\Models\Permiso;
use App\Models\Role;
use App\Traits\Acces;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use Acces;
    public function index(){
        $menusPrin = $this->getMenus();
        return view('page.roles',compact('menusPrin'));
    }

    public function ListRoles($query = null, $pageSize = null,$id=0, $status = 1){
        $profiles = Role::select(
                'roles.id as roles_id',
                'roles.sub_role as roles_sub_role',
                'roles.url as roles_url',
                'roles.nombre as roles_name',
                'roles.descripcion as roles_description',
                'roles.orden_roles as roles_order_role',
                'roles.estado as roles_status',
                'roles.orden_sub_roles as roles_order_sub_role',
                'roles.estado_sub_menu as roles_status_children'
            );
            if ($status==1) {
                $profiles->where('roles.sub_role', 0)
                    ->where('roles.nombre', 'like', "%$query%")
                    ->orderBy('roles.id', 'desc');
                    return $profiles->paginate($pageSize);
            }else if($status==2) {
                $profiles->where('roles.sub_role', $id)
                    ->orderBy('roles.id', 'asc');
                    return $profiles->get();
            }else if($status==3) {
                    $profiles->where('roles.id',$id)
                    ->orderBy('roles.id', 'asc');
                    return $profiles->get();
            }
    }

    public function getListRole(Request $request){
        $query = $request->input('q');
        $pageSize = $request->input('pageSize', 10);
        $profiles = $this->ListRoles($query,$pageSize,0,1);
        return response()->json($profiles);
    }

    public function getListSubRole(Request $request){
        $role_id = $request->input('params');
        $profiles = $this->ListRoles(null,null,$role_id,2);
        return response()->json($profiles);
    }

    public function postShowRole(Request $request){
        $role_id = $request->input('params');
        $profiles = $this->ListRoles(null,null,$role_id,3);
        return response()->json($profiles);
    }

    public function postAddRole(RoleValidateAdd $request){
        $role = new Role();
        $role->sub_role = 0;
        $role->url = $request->role['url'];
        $role->nombre = e(trim($request->role['name']));
        $role->descripcion = e(strtoupper(trim($request->role['description'])));
        $role->orden_roles = $request->role['order_role'];
        $role->estado_sub_menu = $request->role['status_children'];
        if($request->role['status_children']==1){
            if($role->save()){
                return response()->json([
                    'status'=>true,
                    'message'=>'La informacíon se guardó correctamente'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'La informacíon no se guardó'
                ]);
            }
        }else{
            $role->save();
            $role_id = $role->id;
            foreach ($request->subRole as $value) {
                $subRole = new Role();
                $subRole->sub_role = $role_id;
                $subRole->url = e(strtolower(trim($value['url'])));
                $subRole->nombre = e(trim($value['name']));
                $subRole->descripcion = e(strtoupper(trim($value['description'])));
                $subRole->orden_sub_roles = $value['order_sub_role'];
                $subRole->estado_sub_menu = $value['status_children'];
                $subRole->save();
            }
            return response()->json([
                'status'=>true,
                'message'=>'La informacíon se guardó correctamente'
            ]);
        }
    }

    public function postUpdateRole(RoleValidateUpdate $request){
        try {
            $role = Role::findOrFail($request->role['id']);
            $role->sub_role = 0;
            $role->url = !empty($request->role['url']) ? $request->role['url'] : '';
            $role->nombre = e(trim($request->role['name']));
            $role->descripcion = e(strtoupper(trim($request->role['description'])));
            $role->orden_roles = $request->role['order_role'];
            $role->estado_sub_menu = $request->role['status_children'];
            if($role->save()){
                return response()->json([
                    'status'=>true,
                    'message'=>'La informacíon no se guardó'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message' => 'La informacíon se guardó correctamente'
            ]);
            throw $th;
        }
    }

    public function postDeleteRoleSubRole(Request $request){
        try {
            $role = Role::findOrFail($request->id);
            $count = Role::where('sub_role', $request->role_id)->count();
            if ($count === 1) {
                $updateRole = Role::findOrFail($request->role_id);
                $updateRole->estado_sub_menu = 1;
                $updateRole->save();
                if($role->delete()){
                    return response()->json([
                        'status'=>true,
                        'message'=>'La informacíon se eliminó correctamente'
                    ]);
                }
            }else{
                if($role->delete()){
                    return response()->json([
                        'status'=>true,
                        'message'=>'La informacíon se eliminó correctamente'
                    ]);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function postDeleteRole(Request $request){
        try {
            $role = Role::findOrFail($request->id);
            $role->estado = 0;
            if($role->save()){
                return response()->json([
                    'status'=>true,
                    'message'=>'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function postActiveRole(Request $request){
        try {
            $role = Role::findOrFail($request->id);
            $role->estado = 1;
            if($role->save()){
                return response()->json([
                    'status'=>true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function postSubRole(SubMenuValidateAdd $request){             
        try {
            $role = new Role();
            $role->sub_role = $request->role_id;
            $role->url = !empty($request->role['url']) ? $request->role['url'] : '';
            $role->nombre = e(trim($request->role['name']));
            $role->descripcion = e(strtoupper(trim($request->role['description'])));
            $role->orden_sub_roles = $request->role['order_role'];
            $role->estado_sub_menu = $request->role['status_children'];
            if($role->save()){
                return response()->json([
                    'status'=>true,
                    'message'=>'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }

    public function listPermisionAsing(Request $request){
        try {
            $result = Permiso::join('roles','roles.id','=','permisos.rol_id')
                                ->select('roles.nombre','permisos.id','permisos.perfil_id')
                                ->where('permisos.perfil_id',$request->id)
                                ->get();
            return  response()->json($result);                                  
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function addPermisionAsing(Request $request){
        try {
            $permisos = new Permiso();
            $permisos->perfil_id = $request->idProfile;
            $permisos->rol_id = $request->idRole;
            if($permisos->save()){
                return response()->json([
                    'status'=>true,
                    'message'=>'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
             throw $th;
        }
    }

    public function deletePermisionAsing(Request $request){
        try {
            $permisos = Permiso::findOrFail($request->permisosId);
            if($permisos->delete()){
                return response()->json([
                    'status'=>true,
                    'message'=>'Se elimino correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
