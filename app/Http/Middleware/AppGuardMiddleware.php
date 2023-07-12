<?php

namespace App\Http\Middleware;

use App\Models\UserApp;
use App\Models\UserRole;
use Closure;
use Illuminate\Http\Request;

class AppGuardMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $key, $level)
    {
        $type = \Auth::user()->type();

        if($type->id_typesuser == 1){
            return $next($request);
        }

        if($type->id_typesuser == 2){
            $access = \Auth::user()->accessApp();

            if(!$access){
                return redirect()->route('unauthorized');
            }

            return $next($request);
        }

        if($type->id_typesuser == 3){
            $access = \Auth::user()->accessApp();

            if(!$access){
                return redirect()->route('unauthorized');
            }

            $havePermission = \Auth::user()->havePermission($key, $level);

            if(!$havePermission){
                return redirect()->route('unauthorized');
            }

            return $next($request);
        }
        
        return redirect()->route('unauthorized');
    }
}
