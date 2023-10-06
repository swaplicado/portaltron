<?php

namespace App\Models;

use App\Constants\SysConst;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection= 'mysqlmngr';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id_n',
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'names',
        'full_name',
        'img_path',
        'is_active',
        'is_deleted',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Regresa el tipo de usuario, la información la obtiene desde appmanager
     */
    public function type(){
        return $this->belongsToMany(AppmanagerModels\Typesuser::class, 'adm_users_typesuser', 'user_id', 'typeuser_id')
                    ->where('adm_users_typesuser.app_id', config('myapp.id', 0))
                    ->first();
    }

    /**
     * Regresa los roles de usuario, la información la obtiene desde appmanager
     */
    public function roles(){
        return $this->belongsToMany(AppmanagerModels\Role::class, 'adm_user_roles','user_id', 'role_id')
                    ->where('adm_user_roles.app_n_id', config('myapp.id', 0))
                    ->get();
    }

    /**
     * Regresa si el usuario tiene rol proveedor, la información la obtiene desde appmanager, la informacion se obtiene de appmanager
     */
    public function is_provider(){
        return !is_null(
                        $this->belongsToMany(AppmanagerModels\Role::class, 'adm_user_roles','user_id', 'role_id')
                            ->where('adm_user_roles.app_n_id', config('myapp.id', 0))
                            ->where('adm_roles.id_role', SysConst::ROL_PROVEEDOR)
                            ->first()
                        );
    }

    /**
     * Obtiene los permisos del usuario asignados unicamente por el rol de usuario, no obtiene los permisos
     * asignados individualmente. la informacion se obtiene de appmanager
     */
    public function permissionsByRol(){
        $lRoles = $this->roles()->pluck('id_role');
        $RolePermissions = \DB::connection('mysqlmngr')
                                ->table('adm_roles_permissions as rp')
                                ->join('adm_permissions as p', 'p.id_permission', '=', 'rp.permission_id')
                                ->whereIn('role_id', $lRoles)
                                ->where('rp.app_n_id', config('myapp.id', 0))
                                ->select(
                                    'p.key_code',
                                    'p.level',
                                )
                                ->get();

        return $RolePermissions;
    }

    /**
     * Obtiene todos los permisos asignados al usuario, los permisos por rol y los permisos
     * asignados individualmente, la informacion se obtiene de appmanager
     */
    public function permissions(){
        $RolePermissions = $this->permissionsByRol();

        $blockedPermissions = $this->belongsToMany(AppmanagerModels\Permission::class, 'adm_user_permissions', 'user_id', 'permission_id')
                                    ->where('adm_user_permissions.app_n_id', config('myapp.id', 0))
                                    ->where('is_blocked', 1)
                                    ->pluck('id_permission')
                                    ->toArray();

        $RolePermissions = $RolePermissions->whereNotIn('permission_id', $blockedPermissions);

        $assignPermissions = $this->belongsToMany(AppmanagerModels\Permission::class, 'adm_user_permissions', 'user_id', 'permission_id')
                                    ->where('adm_user_permissions.app_n_id', config('myapp.id', 0))
                                    ->where('is_blocked', 0)
                                    ->select(
                                        'key_code',
                                        'level',
                                    )
                                    ->get();

        $lPermissions = $RolePermissions->merge($assignPermissions);

        return $lPermissions;
    }

    /**
     * Regresa true si el usuario tiene el permiso solicitado, si no, regresa false
     * el metodo recibe la key del permiso y el level del permisos, la informacion se obtiene de appmanager
     */
    public function havePermission($key, $level){
        $lRoles = $this->roles()->pluck('id_role')->toArray();

        $rolePermission =  \DB::connection('mysqlmngr')
                            ->table('adm_permissions as p')
                            ->join('adm_roles_permissions as rp', 'rp.permission_id', '=', 'p.id_permission')
                            ->whereIn('rp.role_id', $lRoles)
                            ->where('p.key_code', $key)
                            ->where('p.level', $level)
                            ->first();

        $userPermission =  \DB::connection('mysqlmngr')
                            ->table('adm_permissions as p')
                            ->join('adm_user_permissions as up', 'up.permission_id', '=', 'p.id_permission')
                            ->where('p.key_code', $key)
                            ->where('p.level', $level)
                            ->first();

        if(!is_null($userPermission)){
            return !$userPermission->is_blocked;
        }

        return !is_null($rolePermission);
    }

    /**
     * Regresa true si el usuario tiene acceso a la app, false si no lo tiene, informacion se obtiene de appmanager
     */
    public function accessApp(){
        return !is_null(
                            $this->belongsToMany(AppmanagerModels\SApp::class, 'adm_user_apps', 'user_id', 'app_id')
                                ->where('app_id', config('myapp.id', 0))
                                ->first()
                        );
    }

    /**
     * Metodo que comprueba si el usuario tiene asigando los permisos
     * que se le piden, el metodo recibe un array de los permisos de la forma
     * [['key': key, 'level': level], [...]]
     */
    public function authorizedPermission($lPermission){
        $continue = false;
        foreach($lPermission as $permission){
            if($this->havePermission($permission->key, $permission->level)){
                $continue = true;
                break;
            }
        }
        abort_unless($continue, 401);
    }

    public function getProviderData(){
        $oProvider = \DB::connection('mysql')
                        ->table('providers as p')
                        ->join('status_providers as sp', 'sp.id_status_providers', '=', 'p.status_provider_id')
                        ->where('p.user_id', $this->id)
                        ->select(
                            'p.*',
                            'sp.name as status_name'
                        )
                        ->first();

        return $oProvider;
    }

    public function getArea(){
        $oArea = \DB::connection('mysql')
                    ->table('users_areas as ua')
                    ->join('areas as a', 'a.id_area', '=', 'ua.area_id')
                    ->where('ua.user_id', $this->id)
                    ->where('ua.is_deleted', 0)
                    ->select(
                        'a.id_area',
                        'a.name_area'
                    )
                    ->first();

    return $oArea;
    }
}
