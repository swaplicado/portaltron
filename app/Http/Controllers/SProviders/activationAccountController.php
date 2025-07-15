<?php

namespace App\Http\Controllers\SProviders;

use App\Http\Controllers\Controller;
use App\Utils\SProvidersUtils;
use Illuminate\Http\Request;
use App\Utils\AppLinkUtils;
use stdClass;
use App\Models\User;
use App\Models\UserApp;
use App\Models\UserRole;
use App\Models\UserType;
use App\Constants\SysConst;
use App\Models\SProviders\SProvider;

class activationAccountController extends Controller
{
    public function checkProviderToRegister(Request $request) {
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $rfc = $request->rfc;
            $provider = SProvider::where('provider_rfc', $rfc)->first();
            
            if ($provider) {
                return json_encode(['success' => false, 'message' => 'El RFC ya se encuentra registrado, intenta iniciar sesión para continuar']);
            }

            $route = $config->AppLinkRouteProviderData;

            $oUser = new stdClass();
            $oUser->rfc = $rfc;
            $oUser->username = $rfc;

            $body = '{
                "fiscalid": "'.$rfc.'",
                "reqUser": "'.$rfc.'"
            }';

            $data = AppLinkUtils::requestAppLink($route, 'POST', $oUser, $body);

            if(!is_null($data)){
                if($data->code != 200){
                    $route = route('registerProvider.registerProvider', [ 'key' => base64_encode($rfc)]);
                    return json_encode(['success' => false, 'message' => $data->message, 'route' => $route]);
                }
                
                if ($data->code == 200) {
                    if ($data->email) {
                        SProvidersUtils::sendMailToRegisterProvider($rfc, $data->email);
                    } else {
                        return json_encode(['success' => false, 'message' => 'No cuentas con correo electrónico registrado, contacta con soporte']);
                    }
                }

                return json_encode(['success' => true, 'message' => 'El proveedor se encuentra en nustros registros']);
            }else{
                return json_encode(['success' => false, 'message' => 'AppLink no responde']);
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => 'Ahora mismo no es posible validar su rfc, intentelo mas tarde', 'icon' => 'error']);
        }
    }

    public function providerAccountActivateIndex($key, $token) {
        try {
            $account_activate_valid = \DB::table('providers_account_activations')
                                    ->where('key', $key)
                                    ->where('token', $token)
                                    ->first();

            if (!$account_activate_valid) {
                return redirect(route('login'))->with('message', '');
            }

            $rfc = base64_decode($key);

            $config = \App\Utils\Configuration::getConfigurations();
            $route = $config->AppLinkRouteProviderData;
            $oUser = new stdClass();
            $oUser->rfc = $rfc;
            $oUser->username = $rfc;

            $body = '{
                "fiscalid": "'.$rfc.'",
                "reqUser": "'.$rfc.'"
            }';
            
            $data = AppLinkUtils::requestAppLink($route, 'POST', $oUser, $body);

            if(is_null($data)){
                return redirect(route('login'))->with('message', 'AppLink no responde, contacta con soporte');
            }

            $name = $data->bp;
            $shortName = $data->bp_comm;
            $email = $data->email;
            $fiscal_id = \DB::table('fiscal_regime')
                                ->where('key', $data->tax_regime)
                                ->value('id');

            $fiscal_id = is_null($fiscal_id) ? "" : $fiscal_id; // Default fiscal regime if not found

            $fiscalRegime = \DB::table('fiscal_regime')
                                ->select(
                                    'id',
                                    'key',
                                    'name'
                                )
                                ->get();

            $lFiscalRegime = [];
            foreach ($fiscalRegime as $fiscal) {
                $lFiscalRegime[] = [ 'id' => $fiscal->id, 'text' => $fiscal->key . ' - '. $fiscal->name ];
            }

            return view('SProviders.providerAccountActivation')->with('lFiscalRegime', $lFiscalRegime)
                                                                ->with('rfc', $rfc)
                                                                ->with('name', $name)
                                                                ->with('shortName', $shortName)
                                                                ->with('email', $email)
                                                                ->with('fiscal_id', $fiscal_id)
                                                                ->with('key', $key);
        } catch (\Throwable $th) {
            \Log::error($th);
            return redirect(route('login'))->with('message', 'No fue posible activar la cuenta del proveedor, contacta con soporte');
        }
    }

    public function registryAndActivateProdviderAccount(Request $request) {
        try {
            $name = $request->name;
            $shortName = $request->shortName;
            $rfc = $request->rfc;
            $key = $request->key;
            $email = $request->email;
            $password = $request->password;
            $confirmPassword = $request->confirmPassword;
            $fiscal_id = $request->fiscal_id;
            $config = \App\Utils\Configuration::getConfigurations();
    
            if(is_null($fiscal_id)){
                return json_encode(['success' => false, 'message' => "Debes seleccionar un régimen fiscal", 'icon' => 'info']);
            }
    
            $searchRfc = \DB::table('providers')
                            ->where('provider_rfc', $rfc)
                            ->where('is_deleted', 0)
                            ->first();
    
            if(!is_null($searchRfc)){
                \Log::error("El RFC ya se encuentra registrado: " . $rfc);
                return json_encode(['success' => false, 'message' => "El RFC ya se encuentra registrado, intenta iniciar sesión para continuar", 'icon' => 'info']);
            }
            
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
                $oProvider->provider_fiscal_regime_id = $fiscal_id;
                $oProvider->provider_email = $email;
                $oProvider->user_id = $oUser->id;
                $oProvider->status_provider_id = SysConst::PROVIDER_APROBADO;
                $oProvider->is_active = 1;
                $oProvider->is_deleted = 0;
                $oProvider->created_by = $oUser->id;
                $oProvider->updated_by = $oUser->id;
                $oProvider->save();

                \DB::connection('mysql')->commit();
            } catch (\Throwable $th) {
                \DB::connection('mysql')->rollBack();

                \DB::connection('mysqlmngr')->beginTransaction();
                    $oUser->delete();
                \DB::connection('mysqlmngr')->commit();
                
                //regresar error con mensaje personalizado
                new \Exception('No fue posible registrar el proveedor, contacta con soporte');
            }
        
            \DB::table('providers_account_activations')
                ->where('key', $key)
                ->delete();

            // Autenticar al usuario después del registro exitoso
            if ($oUser) {
                \Auth::login($oUser);
                return json_encode(['success' => true, 'redirect' => route('login')]);
            }
    
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }
    
        return json_encode(['success' => true]);
    }
}
