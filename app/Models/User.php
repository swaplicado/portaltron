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
        'username',
        'email',
        'password',
        'last_name1',
        'last_name2',
        'names',
        'full_name',
        'rol_id',
        'provider_id',
        'remember_token',
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

    public function type(){
        return $this->belongsToMany(AppmanagerModels\Typesuser::class, 'adm_users_typesuser', 'user_id', 'typeuser_id')
                    ->where('adm_users_typesuser.app_id', 1)
                    ->first();
    }

    public function roles(){
        return $this->belongsToMany(AppmanagerModels\Role::class, 'adm_user_roles','user_id', 'role_id')
                    ->where('adm_user_roles.app_n_id', config('myapp.id', 0))
                    ->get();
    }

    public function is_provider(){
        return !is_null(
                        $this->belongsToMany(AppmanagerModels\Role::class, 'adm_user_roles','user_id', 'role_id')
                            ->where('adm_user_roles.app_n_id', config('myapp.id', 0))
                            ->where('adm_roles.id_role', SysConst::ROL_PROVEEDOR)
                            ->first()
                        );
    }

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

    public function accessApp(){
        return !is_null(
                            $this->belongsToMany(AppmanagerModels\SApp::class, 'adm_user_apps', 'user_id', 'app_id')
                                ->where('app_id', config('myapp.id', 0))
                                ->first()
                        );
    }
}
