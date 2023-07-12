<?php namespace App\Utils;

use App\Models\SProviders\SProvider;

class SProvidersUtils {
    public static function getProvider($provider_id){
        $oProvider = SProvider::join(config('myapp.mngr_db').'.users as u', 'u.id', '=', 'user_id')
                            ->where('id_provider', $provider_id)
                            ->select(
                                'provider_name',
                                'provider_short_name',
                                'provider_rfc',
                                'provider_email',
                                'u.username',
                                'u.email',
                                'u.full_name',
                            )
                            ->first();

        return $oProvider;
    }

    public static function getlProviders(){
        $lProviders = SProvider::where('is_active', 1)
                            ->where('is_deleted', 0)
                            ->join(config('myapp.mngr_db').'.users as u', 'u.id', '=', 'user_id')
                            ->select(
                                'id_provider',
                                'provider_short_name',
                                'provider_name',
                                'provider_rfc',
                                'provider_email',
                                'u.full_name as user',
                                \DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d") as updated'),
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