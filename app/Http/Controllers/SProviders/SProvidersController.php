<?php

namespace App\Http\Controllers\SProviders;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Utils\SProvidersUtils;
use App\Utils\SysUtils;
use Illuminate\Http\Request;
use App\Models\SProviders\SProvider;

class SProvidersController extends Controller
{
    public function index($user_id = null){
        try {
            $lProviders = SProvidersUtils::getlProvidersDatas();
    
            $oUser = null;
            if(!is_null($user_id)){
                $oUser = User::where('users.id', $user_id)
                            ->join('adm_user_apps as ua', 'ua.user_id', '=', 'users.id')
                            ->where('ua.app_id', config('myapp.id', 0))
                            ->select(
                                'users.id',
                                'users.username',
                                'users.email',
                                'users.full_name',
                            )
                            ->first();
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('sproviders.sproviders')->with('lProviders', $lProviders)
                                    ->with('oUser', $oUser);
    }

    public function createProvider(Request $request){
        try {
            $provider_name = $request->provider_name;
            $provider_short_name = $request->provider_short_name;
            $provider_rfc = $request->provider_rfc;
            $provider_email = $request->provider_email;
            $user_id = $request->user_id;

            if(sizeof($provider_rfc) < 12 || sizeof($provider_rfc) > 13){
                return json_encode(['success' => false, 'message' => 'El RFC no es valido', 'icon' => 'warning']);
            }

            SysUtils::requestUserCanAccessToApp($user_id);

            \DB::beginTransaction();

            $oProvider = new SProvider();
            $oProvider->provider_name = $provider_name;
            $oProvider->provider_short_name = $provider_short_name;
            $oProvider->provider_rfc = $provider_rfc;
            $oProvider->provider_email = $provider_email;
            $oProvider->user_id = $user_id;
            $oProvider->save();

            $lProviders = SProvidersUtils::getlProvidersDatas();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function updateProvider(Request $request){
        try {
            $id_provider = $request->id_provider;
            $provider_name = $request->provider_name;
            $provider_short_name = $request->provider_short_name;
            $provider_rfc = $request->provider_rfc;
            $provider_email = $request->provider_email;

            if(strlen($provider_rfc) < 12 || strlen($provider_rfc) > 13){
                return json_encode(['success' => false, 'message' => 'El RFC no es valido', 'icon' => 'warning']);
            }

            \DB::beginTransaction();
            $oProvider = SProvider::findOrFail($id_provider);
            $oProvider->provider_name = $provider_name;
            $oProvider->provider_short_name = $provider_short_name;
            $oProvider->provider_rfc = $provider_rfc;
            $oProvider->provider_email = $provider_email;
            $oProvider->update();

            $lProviders = SProvidersUtils::getlProvidersDatas();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function deleteProvider(Request $request){
        try {
            $id_provider = $request->id_provider;

            \DB::beginTransaction();
            $oProvider = SProvider::findOrFail($id_provider);
            $oProvider->is_active = 0;
            $oProvider->is_deleted = 1;
            $oProvider->update();

            $lProviders = SProvidersUtils::getlProvidersDatas();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
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
}
