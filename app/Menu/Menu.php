<?php

namespace App\Menu;
use App\Constants\SysConst;
use \Session;
class Menu {
    public static function createMenu($oUser = null)
    {
        $element = 1;
        $list = 2;
        if (is_null($oUser)) {
            return "";
        }

        $type = \Auth::user()->type();

        if($type->id_typesuser == SysConst::TYPE_SUPER){
            $lMenus = [
                (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Inicio'],
                (object) ['type' => $element, 'route' => route('sproviders.index'), 'icon' => 'bx bxs-truck bx-sm', 'name' => 'Proveedores'],
                (object) ['type' => $element, 'route' => route('purchaseOrders.indexManager'), 'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Ordenes compras'],
                (object) ['type' => $element, 'route' => route('accountStates.index'), 'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Estados de cuenta'],
                (object) ['type' => $element, 'route' => route('accountStates.managerIndex'), 'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Estados de cuenta']
            ];
        }else{
            $lPermissions = collect($oUser->permissionsByRol());
    
            $viewsAccess = $lPermissions->where('level', 'vista');
    
            $lMenus = [
                (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Inicio']
            ];
            foreach($viewsAccess as $view){
                switch ($view->key_code) {
                    case 'manager.proveedores':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('sproviders.index'), 'icon' => 'bx bxs-truck bx-sm', 'name' => 'Proveedores'];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('sproviders.documentsProv'), 'icon' => 'bx bxs-archive bx-sm', 'name' => 'Documentos'];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('dpsComplementary.complementsManager'), 'icon' => 'bx bxs-file-blank bx-sm', 'name' => 'CFDI'];
                        break;
                    case 'proveedores.oc':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('purchaseOrders.index'), 'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Ordenes compras'];
                        break;

                    case 'proveedor.cotizaciones':
                        //$lMenu[] = (object) ['type' => $element, 'route' => route('quotations.index'), 'icon' => 'bx bxs-archive bx-sm', 'name' => 'Cotizaciones'];
                        break;
                    case 'proveedor.complementos':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('dpsComplementary.complements'), 'icon' => 'bx bxs-file-blank bx-sm', 'name' => 'Complementos'];
                        break;
                    case 'proveedor.estadoscuentas':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('accountStates.index'), 'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Estados de cuenta'];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('sproviders.profile'), 'icon' => 'bx bxs-user-detail bx-sm', 'name' => 'Mis datos proveedor'];
                        break;

                    case 'manager.proveedores.oc':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('purchaseOrders.indexManager'), 'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Ordenes compras'];
                        break;
                    case 'manager.proveedor.estadoscuentas':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('accountStates.managerIndex'), 'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Estados de cuenta'];
                        break;
                        
                    default:
                        # code...
                        break;
                }
            }
        }
        
        $sMenu = "";
        foreach ($lMenus as $menu) {
            if ($menu == null) {
                continue;
            }
            if($menu->type == $element){
                $sMenu = $sMenu.Menu::createMenuElement($menu->route, $menu->icon, $menu->name);
            }else if($menu->type == $list){
                $sMenu = $sMenu.Menu::createListMenu($menu->id, $menu->list, $menu->name, $menu->icon);
            }
        }

        return $sMenu;
    }

    private static function createMenuElement($route, $icon, $name)
    {
        return '<li class="nav-item">
                    <a class="nav-link" href="'.$route.'">
                        <i class="'.$icon.' menu-icon"></i>
                        <span class="menu-title">'.$name.'</span>
                    </a>
                </li>';
    }

    private static function createListMenu($id, $list, $name, $icon){
        $str = '<li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#'.$id.'" aria-expanded="false" aria-controls="'.$id.'">
                        <i class="'.$icon.' menu-icon"></i>
                            <span class="menu-title">'.$name.'</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="'.$id.'">
                        <ul class="nav flex-column sub-menu">';
        
        foreach($list as $l){
            if(!isset($l['size'])){
                $str = $str.'<li class="nav-item"> <a class="nav-link" href="'.$l['route'].'">'.$l['name'].'</a></li>';
            }else{
                $str = $str.'<li class="nav-item"> <a class="nav-link" href="'.$l['route'].'" style="font-size:'.$l['size'].'">'.$l['name'].'</a></li>';
            }
        }
                    
        $str = $str.'</ul></div></li>';

        return $str;
    }
}