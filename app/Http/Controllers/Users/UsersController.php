<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Providers\Provider;
use App\Models\Roles\Rol;
use App\Constants\SysConst;
use Illuminate\Support\Str;
use App\Utils\UserUtils;

class UsersController extends Controller
{

    public function createUser(Request $request){
        try {
            $rol_id = $request->rol_id;
            $provider_id = $request->provider_id;
            $last_name1 = strtoupper($request->last_name1);
            $last_name2 = strtoupper($request->last_name2);
            $names = strtoupper($request->names);
            $email = $request->email;

            $password = Str::random(8);

            \DB::beginTransaction();

            $oUser = new User();
            $oUser->email = $email;
            $oUser->password = \Hash::make($password);
            $oUser->last_name1 = $last_name1;
            $oUser->last_name2 = $last_name2;
            $oUser->names = $names;
            $oUser->full_name = $last_name1.' '.$last_name2.', '.$names;
            $oUser->rol_id = $rol_id;
            $oUser->provider_id = $rol_id == SysConst::ROL_PROVEEDOR ? $provider_id : null;
            $oUser->created_by = \Auth::user()->id;
            $oUser->updated_by = \Auth::user()->id;

            $username = UserUtils::makeUsername($oUser);

            if (is_null($username)) {
                \DB::rollBack();
                return json_encode(['success' => false, 'message' => 'no hay nombre de usuario disponible', 'icon' => 'error']);
            }

            $oUser->username = $username;
            $oUser->save();
            
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }
    }

    public function updateUser(Request $request){

    }

    public function deleteUser(Request $request){

    }
}
