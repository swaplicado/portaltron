<?php namespace App\Utils;

use App\Models\SProviders\SProvider;

class SProvidersUtils {
    public static function getProvider($provider_id){
        $oProvider = SProvider::join('appsmanager.users as u', 'u.id', '=', 'providers.user_id')
                            ->where('id_provider', $provider_id)
                            ->select(
                                'providers.provider_name',
                                'providers.provider_short_name',
                                'providers.provider_rfc',
                                'providers.provider_email',
                                'u.username',
                                'u.email',
                                'u.full_name',
                            )
                            ->first();

        return $oProvider;
    }

    public static function getlProviders(){
        $lProviders = SProvider::where('providers.is_active', 1)
                            ->where('providers.is_deleted', 0)
                            ->join('appsmanager.users as u', 'u.id', '=', 'providers.user_id')
                            ->select(
                                'providers.id_provider',
                                'providers.provider_short_name',
                                'providers.provider_name',
                                'providers.provider_rfc',
                                'providers.provider_email',
                                'u.full_name as user',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->get();

        return $lProviders;
    }

    public static function getlProvidersDatas(){
        $lProviders = SProvidersUtils::getlProviders();
        $lProviders = SysUtils::collectToArray($lProviders);

        return $lProviders;
    }
}