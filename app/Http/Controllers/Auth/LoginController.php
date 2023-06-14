<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Sys\SyncController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    public function logout() {
        \Auth::logout();
        return redirect('/login');
    }

    public function username(){
        return 'username';
    }

    protected function credentials(Request $request){
        return $request->only($this->username(), 'password');
    }

    public function login(Request $request){
        if (session()->has('key')) {
            return redirect()->route('home');
         }
         
        $request->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $userCredentials = $request->only('username', 'password');

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
