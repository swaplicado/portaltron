<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application ID
    |--------------------------------------------------------------------------
    |
    | Este valor se usa para conocer la app actual y determinar si el usuario puede acceder a ella o no 
    |
    */

    'id' => env('APP_ID', 0),
    'mngr_db' => env('DB_DATABASE_MNGR', 'db'),
    'app_db' => env('DB_DATABASE', 'db'),
    'appmanager_link' => "http://192.168.1.100:9091"

];
