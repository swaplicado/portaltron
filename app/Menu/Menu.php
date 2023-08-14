<?php

namespace App\Menu;
use App\Constants\SysConst;
use \Session;
class Menu {
    public static function createMenu($oUser = null)
    {
        $element = 1;
        $list =2;
        if ($oUser == null) {
            return "";
        }

        $typeUser = $oUser->type()->id_typesuser;

        if($typeUser == SysConst::TYPE_SUPER){
            $roles = [SysConst::ROL_ADMIN];
        }else{
            $roles = $oUser->roles()->pluck('id_role')->toArray();
            // $res = $roles->where('id_role', 2)->first();
            // $rol = $res['id_role'];
        }

        switch ($roles) {
            //Admin
            case in_array('1', $roles):
                $lMenus = [
                    (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Home'],
                    (object) ['type' => $element, 'route' => route('sproviders.index'), 'icon' => 'bx bxs-truck bx-sm', 'name' => 'Proveedores'],
                    // (object) ['type' => $element, 'route' => route('users_index'), 'icon' => 'bx bxs-user bx-sm', 'name' => 'Usuarios'],
                    // (object) ['type' => $list, 'list' => [
                    //     ['route' => route('register'), 'icon' => 'bx bxs-user-plus bx-sm', 'name' => 'Registrar usuario'],
                    //                             ],
                    //                             'icon' => 'bx bxs-user bx-sm', 'name' => 'Usuarios', 'id' => 'Usuarios'
                    // ],
                ];
                break;

            //Proveedor
            case in_array('2', $roles):
                $lMenus = [ 
                    (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Home'],
                    (object) ['type' => $element, 'route' => route('quotations.index'), 'icon' => 'bx bxs-archive bx-sm', 'name' => 'Cotizaciones'],
                    // (object) ['type' => $element, 'route' => route('sproviders.index'), 'icon' => 'bx bxs-truck bx-sm', 'name' => 'Proveedores'],
                    // (object) ['type' => $element, 'route' => route('users_index'), 'icon' => 'bx bxs-user bx-sm', 'name' => 'Usuarios'],
                ];  
                break;

            //Proveedor
            case in_array('3', $roles):
                $lMenus = [ 
                    (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Home'],
                    // (object) ['type' => $element, 'route' => route('sproviders.index'), 'icon' => 'bx bxs-truck bx-sm', 'name' => 'Proveedores'],
                    // (object) ['type' => $element, 'route' => route('users_index'), 'icon' => 'bx bxs-user bx-sm', 'name' => 'Usuarios'],
                ];  
                break;

            default:
                $lMenus = [];
                break;

        }

        // if(!$oUser->changed_password){
        //     $lMenus = [
        //         (object) ['type' => $element, 'route' => route('profile'), 'icon' => 'bx bxs-key bx-sm', 'name' => 'Cambiar contraseña']
        //     ];
        // }
        
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