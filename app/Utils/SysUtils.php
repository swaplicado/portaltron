<?php namespace App\Utils;
      use App\Constants\SysConst;
      use App\Exceptions\SMyException;
      use App\Models\User;


class SysUtils {

    public static function authUserCanAccessToApp(){
        if(is_null(\Auth::user()->accessApp())){
            abort_unless(false, 403, "No estas autorizado para esta acción");
        }
    }

    public static function requestUserCanAccessToApp($user_id){
        $oUser = User::where('id', $user_id)
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->first();
        
        if(is_null($oUser)){
            throw new SMyException('No se encontró el usuario a insertar');
        }

        if(is_null($oUser->accessApp())){
            throw new SMyException('El usuario a insertar no tiene acceso a la app');
        }
    }

    /**
     * Metodo para pasar de una colecciona un array,
     * el array que devuelve solo contiene los valores de la coleccion
     * eliminando las keys
     */
    public static function collectToArray($collection){
        $collection = $collection->toArray();
        
        foreach ($collection as &$subArray) { // se pasa por referencia para modificar el array original
            $subArray = array_values($subArray); // se obtiene un array simple con los valores del sub-array
        }
        unset($subArray);

        return $collection;
    }

    public static function isAdmin(){
        $roles = \Auth::user()->roles()->pluck('id_role')->toArray();
        $typeUser = \Auth::user()->type();
        if(in_array(SysConst::ROL_ADMIN, $roles) || $typeUser->id_typesuser == SysConst::TYPE_SUPER){
            return true;
        }
        
        return false;
    }
}