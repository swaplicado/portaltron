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
    public function index(){
        $lUsers = User::where('users.is_active', 1)
                        ->where('users.is_deleted', 0)
                        ->where('users.id', '!=', 1)
                        ->leftJoin('providers as p', 'p.id_provider', '=', 'users.provider_id')
                        ->leftJoin('adm_rol as r', 'r.id_rol', '=', 'users.rol_id')
                        ->leftJoin('users as uCreated', 'uCreated.id', '=', 'users.created_by')
                        ->leftJoin('users as uUpdated', 'uUpdated.id', '=', 'users.updated_by')
                        ->select(
                            'users.id',
                            'users.rol_id',
                            'users.provider_id',
                            'users.names',
                            'users.last_name1',
                            'users.last_name2',
                            'users.username',
                            'users.email',
                            'users.full_name',
                            'r.rol',
                            'p.provider_name',
                            'uCreated.full_name as created_by',
                            'uUpdated.full_name as updated_by',
                            \DB::raw('DATE_FORMAT(users.created_at, "%Y-%m-%d") as created'),
                            \DB::raw('DATE_FORMAT(users.updated_at, "%Y-%m-%d") as updated'),
                        )
                        ->get()
                        ->toArray();

        $lProviders = Provider::where('is_active', 1)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_provider as id',
                                    'provider_name as text'
                                )
                                ->get()
                                ->toArray();

        $lRoles = Rol::where('is_deleted', 0)
                    ->select(
                        'id_rol as id',
                        'rol as text',
                    )
                    ->get()
                    ->toArray();

        foreach ($lUsers as &$subArray) { // se pasa por referencia para modificar el array original
            $subArray = array_values($subArray); // se obtiene un array simple con los valores del sub-array
        }
        unset($subArray);

        $constants = [
            'ROL_ADMIN' => SysConst::ROL_ADMIN,
            'ROL_PROVEEDOR' => SysConst::ROL_PROVEEDOR
        ];

        return view('users.index')->with('lUsers', $lUsers)
                                ->with('lProviders', $lProviders)
                                ->with('lRoles', $lRoles)
                                ->with('constants', $constants);
    }

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

            if(is_null($username)){
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
