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
    public function handle(Request $request, Closure $next)
    {
        $aRoles = UserRole::where('user_id', \Auth::user()->id)
                    ->pluck('role_id')
                    ->toArray();

        if (in_array(1, $aRoles)) {
            return $next($request);
        }

        $aApps = UserApp::where('user_id', \Auth::user()->id)
                        ->pluck('app_id')
                        ->toArray();

        if (in_array(config('myapp.id', 0), $aApps)) {
            return $next($request);
        }
        
        return redirect()->route('unauthorized');
    }
}
