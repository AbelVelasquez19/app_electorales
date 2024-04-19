<?php
namespace App\Traits;

use App\Models\Permiso;
use Illuminate\Support\Facades\Auth;

trait Acces {
    public function getMenus(){
        $menusPrin = Permiso::select('roles.id','roles.nombre','roles.url', 'roles.sub_role', 'roles.estado_sub_menu')
            ->join('perfiles', 'permisos.perfil_id', '=', 'perfiles.id')
            ->join('roles', 'permisos.rol_id', '=', 'roles.id')
            ->join('users', 'users.perfil_id', '=', 'perfiles.id')
            ->where('users.id', '=', Auth::user()->id)
            ->where('roles.estado', '=', 1)
            ->where('roles.tipo_web', '=', 1)
            ->orderBy('roles.orden_roles', 'asc')
            ->get();

        return $menusPrin;
    }
   /*  public function getUserChange(){
        $user = UserCharges::join("dbo.users","users.id","=","system.user_charges.user_id")
                            ->join("system.charges","charges.id","=","system.user_charges.charges_id")
                            ->join("system.organic_unit","organic_unit.id","=","system.charges.organic_unit_id")
                            ->join("system.person","person.id","=","dbo.users.person_id")
                            ->select("users.id as user_id",
                                     "users.user_name",
                                     "person.name as person_name",
                                     "person.last_name as person_lastname",
                                     "person.mother_last_name as person_mother_last_name",
                                     "charges.id as charges_id",
                                     "charges.name as charges_name",
                                     "organic_unit.id as organic_unit_id",
                                     "organic_unit.name as organic_unit_name"
                            )
                            ->where("users.id","=",Auth::user()->id)
                            ->get();
        return $user;                            
    } */
}