<?php

namespace App\Http\Middleware;

use App\Utils\AppLinkUtils;
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
                        return redirect()->to('127.0.0.1:8000/login')->with('message', $data->message);
                    }
                    
                    if($data->b_del){
                        \Auth::logout();
                        return redirect()->to('127.0.0.1:8000/login')->with('message', 'El proveedor no se encuentra en activo');
                    }

                }else{
                    \Auth::logout();
                    return redirect()->to('127.0.0.1:8000/login')->with('message', 'AppLink no responde');
                }
            }
        }

        session()->put('provider_checked', true);
        return $next($request);
    }
}
