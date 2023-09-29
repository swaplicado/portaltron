<?php namespace App\Utils;

use GuzzleHttp\Client;

class RequestHttpUtils {
    public static function requestHttp($baseUri, $route, $method, $body = null, $requireAuth = true){
        if($requireAuth){
            // $data = AppLinkUtils::AppLinkLogin($oUser);
            // if(!is_null($data)){
            //     if($data->code != 200){
            //         return $data;
            //     }
            // }else{
            //     return null;
            // }
            // $headers = [
            //     'Accept' => 'application/json',
            //     'Content-Type' => 'application/json',
            //     'Authorization' => $data->token
            // ];
        }else{
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];
        }
        
        $client = new Client([
            'base_uri' => $baseUri,
            'timeout' => 30.0,
            'headers' => $headers
        ]);

        $request = new \GuzzleHttp\Psr7\Request($method, $route, $headers, $body);
        $response = $client->send($request);
        $jsonString = $response->getBody()->getContents();

        $data = json_decode($jsonString);
        return $data;
    }
}