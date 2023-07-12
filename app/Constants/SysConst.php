<?php 
namespace App\Constants;

class SysConst {
    /**
     * Constantes de la tabla adm_typesuser
     */
    public const TYPE_SUPER = 1;
    public const TYPE_MANAGER = 2;
    public const TYPE_ESTANDAR = 3;

    /**
     * Constantes de la tabla adm_rol
     */
    public const ROL_ADMIN = 1;
    public const ROL_PROVEEDOR = 2;

    /**
     * Keys de permisos
     */
     public const PROVIDERS = 'providers';
     public const PROVIDERS_CREATE = 'providers_create';
     public const PROVIDERS_UPDATE = 'providers_update';
     public const PROVIDERS_DELETE = 'providers_delete';
     public const PROVIDERS_SHOW = 'providers_show';
}
?>