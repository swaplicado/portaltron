<?php

namespace App\Http\Middleware;

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
        if(session()->get('provider_checked')){
            return $next($request);
        }
        
        $type = \Auth::user()->type();

        if($type->id_typesuser == 3){
            if(\Auth::user()->is_provider()){
                $data = AppLinkUtils::checkUserInAppLink(\Auth::user());

                if(!is_null($data)){
                    if($data->code != 200){
                        \Auth::logout();
                        return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', $data->message);
                    }
                    
                    if($data->b_del){
                        \Auth::logout();
                        return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', 'El proveedor no se encuentra en activo');
                    }

                }else{
                    \Auth::logout();
                    return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', 'AppLink no responde');
                }
            }

            try {
                $oProvider = SProvidersUtils::getProviderByUser(\Auth::user()->id);
                session()->put('provider_id', $oProvider->id_provider);
                session()->put('provider_name', $oProvider->provider_name);
                session()->put('provider_rfc', $oProvider->provider_rfc);
                session()->put('provider_checked', true);
            } catch (\Throwable $th) {
                \Auth::logout();
                return redirect()->to(config('myapp.appmanager_link').'/login')->with('message', 'No existe un proveedor registrado con estas credenciales');
            }
        }

        
        return $next($request);
    }
}
