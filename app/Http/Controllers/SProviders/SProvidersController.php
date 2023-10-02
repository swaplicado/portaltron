<?php

namespace App\Http\Controllers\SProviders;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserApp;
use App\Models\UserRole;
use App\Models\UserType;
use App\Utils\AppLinkUtils;
use App\Utils\SProvidersUtils;
use App\Utils\SysUtils;
use Illuminate\Http\Request;
use App\Models\SProviders\SProvider;

class SProvidersController extends Controller
{
    public function index(){
        try {
            $lProviders = SProvidersUtils::getlProviders();
            
            $lConstants = [
                'PROVIDER_PENDIENTE' => SysConst::PROVIDER_PENDIENTE,
                'PROVIDER_APROBADO' => SysConst::PROVIDER_APROBADO,
                'PROVIDER_RECHAZADO' => SysConst::PROVIDER_RECHAZADO,
                'PROVIDER_PENDIENTE_MODIFICAR' => SysConst::PROVIDER_PENDIENTE_MODIFICAR,
            ];

            $lStatus = \DB::table('status_providers')
                        ->select(
                            'id_status_providers as id',
                            'name as text'
                        )
                        ->get();

        // array_unshift($lStatus, ['id' => 0, 'text' => 'Todos']);
            
        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('sproviders.sproviders')->with('lProviders', $lProviders)->with('lConstants', $lConstants)->with('lStatus', $lStatus);
    }

    public function getProvider(Request $request){
        try {
            $oProvider = SProvidersUtils::getProvider($request->provider_id);
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => false]);
        }

        return json_encode(['success' => true, 'oProvider' => $oProvider]);
    }

    public function registerProviderIndex(){
        return view('SProviders.guestRegister');
    }

    public function tempProviderIndex($name){
        return view('SProviders.tempProvider')->with('name', $name);
    }

    /**
     * Metodo que registra un proveedor y queda pediente para si aprobacion
     */
    public function saveRegisterProvider(Request $request){
        try {
            $name = $request->name;
            $shortName = $request->shortName;
            $rfc = $request->rfc;
            $email = $request->email;
            $password = $request->password;
            $confirmPassword = $request->confirmPassword;
            
            $result = SProvidersUtils::validateDataRegisterProvider($request);
    
            if(!$result[0]){
                return json_encode(['success' => false, 'message' => $result[1], 'icon' => 'info']);
            }
    
            $password = \DB::select(\DB::raw("SELECT PASSWORD('$request->password') AS password_result"))[0]->password_result;
        } catch (\Throwable $th) {
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        try {
            \DB::connection('mysqlmngr')->beginTransaction();
            try {
                $oUser = new User();
                $oUser->username = $rfc;
                $oUser->email = $email;
                $oUser->password = \Hash::make($password);
                $oUser->first_name = $rfc;
                $oUser->last_name = $rfc;
                $oUser->names = $rfc;
                $oUser->full_name = $rfc;
                $oUser->is_active = 1;
                $oUser->is_deleted = 0;
                $oUser->created_by = 1;
                $oUser->updated_by = 1;
                $oUser->save();

                $oTypeUser = new UserType();
                $oTypeUser->user_id = $oUser->id;
                $oTypeUser->app_id = env('APP_ID');
                $oTypeUser->typeuser_id = SysConst::TYPE_ESTANDAR;
                $oTypeUser->save();

                $oUserApp = new UserApp();
                $oUserApp->user_id = $oUser->id;
                $oUserApp->app_id = env('APP_ID');
                $oUserApp->save();

                $oUserRole = new UserRole();
                $oUserRole->app_n_id = env('APP_ID');
                $oUserRole->user_id = $oUser->id;
                $oUserRole->role_id = SysConst::ROL_PROVEEDOR;
                $oUserRole->save();

                \DB::connection('mysqlmngr')->commit();
            } catch (\Throwable $th) {
                \DB::connection('mysqlmngr')->rollBack();
                throw $th;
            }
    
            \DB::connection('mysql')->beginTransaction();
            try {
                $oProvider = new SProvider();
                $oProvider->provider_name = $name;
                $oProvider->provider_short_name = $shortName;
                $oProvider->provider_rfc = $rfc;
                $oProvider->provider_email = $email;
                $oProvider->user_id = $oUser->id;
                $oProvider->status_provider_id = SysConst::PROVIDER_PENDIENTE;
                $oProvider->is_active = 1;
                $oProvider->is_deleted = 0;
                $oProvider->created_by = $oUser->id;
                $oProvider->updated_by = $oUser->id;
                $oProvider->save();

                \DB::connection('mysql')->commit();
            } catch (\Throwable $th) {
                \DB::connection('mysql')->rollBack();
                $oUser->delete();
                throw $th;
            }
        } catch (\Throwable $th) {
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true]);
    }

    public function approveProvider(Request $request){
        try {
            $id_provider = $request->id_provider;

            $oProvider = SProvider::find($id_provider);
            $oUser = User::find($oProvider->user_id);

            $result = AppLinkUtils::checkUserInAppLink($oUser);

            \DB::beginTransaction();

            $oProvider->status_provider_id = SysConst::PROVIDER_APROBADO;
            if(!is_null($result)){
                if($result->code == 200){
                    $oProvider->external_id = $result->id_bp;
                }
            }
            $oProvider->save();

            $lProviders = SProvidersUtils::getlProviders();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function rejectProvider(Request $request){
        try {
            $id_provider = $request->id_provider;

            \DB::beginTransaction();

            $oProvider = SProvider::find($id_provider);
            $oProvider->status_provider_id = SysConst::PROVIDER_RECHAZADO;
            $oProvider->save();

            $lProviders = SProvidersUtils::getlProviders();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function requireModifyProvider(Request $request){
        try {
            $id_provider = $request->id_provider;
            $comments = $request->comments;

            \DB::beginTransaction();

            $oProvider = SProvider::find($id_provider);
            $oProvider->status_provider_id = SysConst::PROVIDER_PENDIENTE_MODIFICAR;
            $oProvider->comments_n = $comments;
            $oProvider->save();

            $lProviders = SProvidersUtils::getlProviders();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function tempModifyProvider(){
        $oProvider = \Auth::user()->getProviderData();
        return view('sproviders.tempModifyProvider')->with('oProvider', $oProvider);
    }

    /**
     * Metodo para actualizar los datos del proveedor cuando este tiene status pendiente de modificar
     */
    public function updateTempProvider(Request $request){
        try {
            $name = $request->name;
            $shortName = $request->shortName;
            $rfc = $request->rfc;
            $email = $request->email;

            if($name == null || $name == ''){
                $message = 'Debe introducir su razón social';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }
    
            if($shortName == null || $shortName == ''){
                $message = 'Debe introducir su nombre comercial';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }
    
            if($rfc == null || $rfc == ''){
                $message = 'Debe introducir su RFC';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }
    
            if(strlen($rfc) < 12){
                $message = 'Debe introducir un RFC valido';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }
    
            if($email == null || $email == ''){
                $message = 'Debe introducir su Email';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }

            try {
                $oProvider = SProvider::findOrFail(\Auth::user()->getProviderData()->id_provider);
                \DB::connection('mysqlmngr')->beginTransaction();
                try {
                    $oUser = User::findOrFail($oProvider->user_id);
                    $oUser->username = $rfc;
                    $oUser->email = $email;
                    $oUser->first_name = $rfc;
                    $oUser->last_name = $rfc;
                    $oUser->names = $rfc;
                    $oUser->full_name = $rfc;
                    $oUser->updated_by = \Auth::user()->id;
                    $oUser->update();
    
                    \DB::connection('mysqlmngr')->commit();
                } catch (\Throwable $th) {
                    \DB::connection('mysqlmngr')->rollBack();
                    throw $th;
                }
        
                \DB::connection('mysql')->beginTransaction();
                try {
                    $oProvider->provider_name = $name;
                    $oProvider->provider_short_name = $shortName;
                    $oProvider->provider_rfc = $rfc;
                    $oProvider->provider_email = $email;
                    $oProvider->user_id = $oUser->id;
                    $oProvider->status_provider_id = SysConst::PROVIDER_PENDIENTE;
                    $oProvider->updated_by = \Auth::user()->id;
                    $oProvider->update();
    
                    \DB::connection('mysql')->commit();
                } catch (\Throwable $th) {
                    \DB::connection('mysql')->rollBack();
                    throw $th;
                }
            } catch (\Throwable $th) {
                return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
            }

        } catch (\Throwable $th) {
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true]);
    }
}
