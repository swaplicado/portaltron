<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Sys\SyncController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Request as GuzzleRequest;
use GuzzleHttp\Exception\RequestException;
use App\Utils\AppLinkUtils;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     *  Metodo para hacer logout desde un get route
     */
    public function logout() {
        \Auth::logout();
        return redirect()->to('http://localhost:9091/appsmanager/public/login');
    }

    public function username(){
        return 'username';
    }

    protected function credentials(Request $request){
        return $request->only($this->username(), 'password');
    }

    /**
     * Metodo principal para hacer login, recibe un request con:
     * username - nombre de usuario
     * password - contraseña de usuario
     */
    public function login(Request $request){
        if (session()->has('key')) {
            return redirect()->route('home');
         }
         
        $request->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $userCredentials = $request->only('username', 'password');

        $oUser = \DB::table('users')
                    ->where('username', $userCredentials['username'])
                    ->select('rol_id')
                    ->first();

        if(is_null($oUser)){
            return $this->sendFailedLoginResponse($request);
        }

        if($oUser->rol_id == 2){
            $data = AppLinkUtils::checkUserInAppLink((object)$userCredentials);
            if(!is_null($data)){
                if($data->code != 200){
                    return redirect()->route('login')->with('message', $data->message);
                }
                
                if($data->b_del){
                    return redirect(route('login'))->with('message', 'El proveedor no se encuentra en activo');
                }

            }else{
                return redirect(route('login'))->with('message', 'AppLink no responde');
            }
        }

        if (Auth::attempt($userCredentials)) {
            $this->authenticated($request, Auth::user());
            return redirect()->route('home');
        }
        else {
            return $this->sendFailedLoginResponse($request);
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    public function showLoginForm($route = null, $idApp = null){
        return view('auth.login');
    }
}
