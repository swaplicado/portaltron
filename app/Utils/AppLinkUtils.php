<?php namespace App\Utils;

use GuzzleHttp\Client;
use Carbon\Carbon;

class AppLinkUtils {
    public static function AppLinkLogin($oUser){
        $config = \App\Utils\Configuration::getConfigurations();

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $client = new Client([
            'base_uri' => $config->AppLinkRoute,
            'timeout' => 30.0,
            'headers' => $headers,
            'verify' => false
        ]);

        $body = '{
                    "usr": "'.env('userAppLink').'",
                    "usr_pswd": "'.env('userAppLinkPass').'",
                    "reqUser": "'.$oUser->username.'"
                }';

        try {
            $response = $client->request('POST', $config->AppLinkRouteLogin , [
                'body' => $body
            ]);
        } catch (\Throwable $th) {
            return null;
        }

        $jsonString = $response->getBody()->getContents();

        $data = json_decode($jsonString);

        return $data;
    }

    public static function checkUserInAppLink($oUser){
        $config = \App\Utils\Configuration::getConfigurations();
        $data = AppLinkUtils::AppLinkLogin($oUser);
        if(!is_null($data)){
            if($data->code != 200){
                return $data;
            }
        }else{
            return null;
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $data->token
        ];
        
        $client = new Client([
            'base_uri' => $config->AppLinkRoute,
            'timeout' => 30.0,
            'headers' => $headers
        ]);

        $oProvider = \DB::table('users as u')
                        ->leftJoin('providers as p', 'p.id_provider', '=', 'u.provider_id')
                        ->where('u.username', $oUser->username)
                        ->where('u.is_active', 1)
                        ->where('u.is_deleted', 0)
                        ->select(
                            'u.username',
                            'p.provider_rfc'
                        )
                        ->first();

        $body = '{
                    "fiscalid": "'.$oProvider->provider_rfc.'",
                    "reqUser": "'.$oUser->username.'"
                }';

        $request = new \GuzzleHttp\Psr7\Request('POST', $config->AppLinkRouteProviderFind, $headers, $body);
        $response = $client->send($request);
        $jsonString = $response->getBody()->getContents();

        $data = json_decode($jsonString);
        return $data;
    }
}