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

    /**
     * Constantes de la tabla type_doc
    */
    public const DOC_TYPE_PURCHASE_ORDER = 1;
    public const DOC_TYPE_FACTURA = 2;
    public const DOC_TYPE_NOTA_CREDITO = 3;

    /**
     * Constantes de la tabla status doc
     */
    public const DOC_STATUS_NUEVO = 1;
    public const DOC_STATUS_ATENDIDO = 2;
    public const FACTURA_STATUS_NUEVO = 3;
    public const FACTURA_STATUS_APROBADO = 4;
    public const FACTURA_STATUS_RECHAZADO = 5;
    public const FACTURA_STATUS_PENDIENTE = 6;
    public const NOTA_CREDITO_STATUS_NUEVO = 7;
    public const NOTA_CREDITO_STATUS_APROBADO = 8;
    public const NOTA_CREDITO_STATUS_RECHAZADO = 9;
    public const NOTA_CREDITO_STATUS_PENDIENTE = 10;

    /**
     * Constantes de la tabla status provider
     */
    public const PROVIDER_PENDIENTE = 1;
    public const PROVIDER_APROBADO = 2;
    public const PROVIDER_RECHAZADO = 3;
    public const PROVIDER_PENDIENTE_MODIFICAR = 4;

    /**
     * Constantes de check status
     */
    public const VOBO_NO_REVISION = 0;
    public const VOBO_REVISION = 1;
    public const VOBO_REVISADO = 2;

    /**
     * Areas de usuarios
     */
    public const AREA_COMPRAS = 1;
    public const AREA_MANTENIMIENTO = 2;
    public const AREA_PROYECTOS = 3;
    public const AREA_CONTABILIDAD = 4;
}
?>