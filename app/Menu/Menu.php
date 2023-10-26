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
                (object) ['type' => $element, 'route' => route('purchaseOrders.indexManager'), 'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Ordenes compra'],
                // (object) ['type' => $element, 'route' => route('accountStates.index'), 'icon' => 'bx bx-wallet bx-sm', 'name' => 'Estados de cuenta'],
                (object) ['type' => $element, 'route' => route('accountStates.managerIndex'), 'icon' => 'bx bx-wallet bx-sm', 'name' => 'Estados de cuenta'],
                (object) ['type' => $element, 'route' => route('estimateRequest.indexERManager'), 'icon' => 'bx bxs-dollar-circle bx-sm', 'name' => 'Sol. cotización'],
                (object) ['type' => $element, 'route' => route('payComplement.payComplement'), 'icon' => 'bx bx bx-receipt bx-sm', 'name' => 'CFDI de pago']
            ];
        }else{
            $lPermissions = collect($oUser->permissionsByRol());
    
            $viewsAccess = $lPermissions->where('level', 'vista');
    
            $lMenus = [
                (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Inicio', 'order' => 0],
                (object) ['type' => $element, 'route' => route('manualUsuario'), 'icon' => 'bx bxs-book-alt bx-sm', 'name' => 'Manual de usuario', 'order' => 8]
            ];
            foreach($viewsAccess as $view){
                switch ($view->key_code) {
                    case 'manager.proveedores':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('sproviders.index'), 
                                    'icon' => 'bx bxs-truck bx-sm', 'name' => 'Proveedores', 'order' => 1];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('sproviders.documentsProv'), 
                                    'icon' => 'bx bxs-archive bx-sm', 'name' => 'Documentos prov.','order' => 2];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('dpsComplementary.complementsManager'), 
                                    'icon' => 'bx bxs-file-blank bx-sm', 'name' => 'Facturas y NC', 'order' => 5];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('payComplement.payComplementsManager'), 
                                    'icon' => 'bx bx bx-receipt bx-sm', 'name' => 'CFDI de pago', 'order' => 6];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('estimateRequest.indexERManager'), 
                                    'icon' => 'bx bxs-dollar-circle bx-sm', 'name' => 'Sol. cotización', 'order' => 3];
                        break;
                    case 'proveedores.oc':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('purchaseOrders.index'), 
                                    'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Ordenes compra', 'order' => 4];
                        break;

                    case 'proveedor.cotizaciones':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('estimateRequest.index'), 
                                    'icon' => 'bx bxs-dollar-circle bx-sm', 'name' => 'Sol. cotización', 'order' => 3];
                        break;
                    case 'proveedor.complementos':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('dpsComplementary.complements'), 
                                    'icon' => 'bx bxs-file-blank bx-sm', 'name' => 'Facturas y NC', 'order' => 5];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('notaCredito.notaCredito'), 
                                    'icon' => 'bx bx bxs-credit-card bx-sm', 'name' => 'Notas de crédito', 'order' => 6];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('payComplement.payComplement'), 
                                    'icon' => 'bx bx bx-receipt bx-sm', 'name' => 'CFDI de pago', 'order' => 7];
                        break;
                    case 'proveedor.estadoscuentas':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('accountStates.index'), 
                                    'icon' => 'bx bx-wallet bx-sm', 'name' => 'Estados de cuenta', 'order' => 8];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('sproviders.profile'), 
                                    'icon' => 'bx bxs-user-detail bx-sm', 'name' => 'Mis datos proveedor', 'order' => 1];
                        break;

                    case 'manager.proveedores.oc':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('purchaseOrders.indexManager'), 
                                    'icon' => 'bx bx-cart-alt bx-sm', 'name' => 'Ordenes compra', 'order' => 4];
                        break;
                    case 'manager.proveedor.estadoscuentas':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('accountStates.managerIndex'), 
                                    'icon' => 'bx bx-wallet bx-sm', 'name' => 'Estados de cuenta', 'order' => 7];
                        break;
                        
                    default:
                        # code...
                        break;
                }
            }
        }

        $oMenu = collect($lMenus)->sortBy('order');
        $lMenus = $oMenu->toArray();
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