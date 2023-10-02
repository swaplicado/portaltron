<?php namespace App\Utils;

use App\Models\SProviders\SProvider;

class SProvidersUtils {

    public static function getProviderByUser($user_id){
        $oProvider = SProvider::where('user_id', $user_id)->first();

        return $oProvider;
    }

    public static function getProvider($provider_id){
        $oProvider = SProvider::join(config('myapp.mngr_db').'.users as u', 'u.id', '=', 'user_id')
                            ->join('status_providers as sp', 'sp.id_status_providers', '=', 'providers.status_provider_id')
                            ->where('id_provider', $provider_id)
                            ->select(
                                'id_provider',
                                'user_id',
                                'provider_short_name',
                                'provider_name',
                                'provider_rfc',
                                'provider_email',
                                'status_provider_id',
                                'u.username',
                                'sp.name as status',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->first();

        return $oProvider;
    }

    public static function getlProviders(){
        $lProviders = SProvider::where('providers.is_active', 1)
                            ->where('providers.is_deleted', 0)
                            ->join(config('myapp.mngr_db').'.users as u', 'u.id', '=', 'user_id')
                            ->join('status_providers as sp', 'sp.id_status_providers', '=', 'providers.status_provider_id')
                            ->select(
                                'id_provider',
                                'user_id',
                                'provider_short_name',
                                'provider_name',
                                'provider_rfc',
                                'provider_email',
                                'status_provider_id',
                                'u.username',
                                'sp.name as status',
                                'providers.external_id as ext_id',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->get();

        return $lProviders;
    }

    public static function validateDataRegisterProvider($oData){
        $message = "";

        $name = $oData->name;
        $shortName = $oData->shortName;
        $rfc = $oData->rfc;
        $email = $oData->email;
        $password = $oData->password;
        $confirmPassword = $oData->confirmPassword;

        if($name == null || $name == ''){
            $message = 'Debe introducir su razón social';
            return [false, $message];
        }

        if($shortName == null || $shortName == ''){
            $message = 'Debe introducir su nombre comercial';
            return [false, $message];
        }

        if($rfc == null || $rfc == ''){
            $message = 'Debe introducir su RFC';
            return [false, $message];
        }

        if(strlen($rfc) < 12){
            $message = 'Debe introducir un RFC valido';
            return [false, $message];
        }

        if($email == null || $email == ''){
            $message = 'Debe introducir su Email';
            return [false, $message];
        }

        if($password == null || $password == ''){
            $message = 'Debe introducir una contraseña de al menos 8 caracteres';
            return [false, $message];
        }

        if(strlen($password) < 8){
            $message = 'La contraseña debe contener al menos 8 caracteres';
            return [false, $message];
        }

        if($confirmPassword == null || $confirmPassword == ''){
            $message = 'Debe introducir la confirmación de la contraseña';
            return [false, $message];
        }

        if($password != $confirmPassword){
            $message = 'La contraseña y la confirmación de la contraseña deben ser iguales';
            return [false, $message];
        }

        return [true, $message];
    }
}