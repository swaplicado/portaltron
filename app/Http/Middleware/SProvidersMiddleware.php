<?php

namespace App\Http\Middleware;

use App\Constants\SysConst;
use App\Utils\AppLinkUtils;
use App\Utils\SProvidersUtils;
use Closure;
use Illuminate\Http\Request;

class SProvidersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $type = \Auth::user()->type();

        if($type->id_typesuser == 3){
            if(\Auth::user()->is_provider()){
                // $data = AppLinkUtils::checkUserInAppLink(\Auth::user());

                // if(!is_null($data)){
                //     if($data->code != 200){
                //         \Auth::logout();
                //         return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', $data->message);
                //     }
                    
                //     if($data->b_del){
                //         \Auth::logout();
                //         return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', 'El proveedor no se encuentra en activo');
                //     }

                // }else{
                //     \Auth::logout();
                //     return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', 'AppLink no responde');
                // }

                $oProvider = \Auth::user()->getProviderData();

                switch ($oProvider->status_provider_id) {
                    case SysConst::PROVIDER_APROBADO:
                        return $next($request);
                    case SysConst::PROVIDER_RECHAZADO:
                        \Auth::logout();
                        return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', 'El proveedor no se encuentra en activo');
                    case SysConst::PROVIDER_PENDIENTE:
                        \Auth::logout();
                        return redirect()->to(route('registerProvider.tempProvider', ['name' => $oProvider->provider_name]));
                    case SysConst::PROVIDER_PENDIENTE_MODIFICAR:
                        return redirect()->to(route('registerProvider.tempModifyProvider'));
                    default:
                        return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', 'No se encontró al proveedor');
                }
            }
        }

        return $next($request);
    }
}
