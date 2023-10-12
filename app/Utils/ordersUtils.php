<?php namespace App\Utils;

class ordersUtils {
    public static function getDpsOrder($type_dps, $area_id){
        if(is_null($area_id)){
            $area_id = 0;
        }

        $config = \App\Utils\Configuration::getConfigurations();
        $sOrders =  json_encode($config->orders);
        $lOrders = collect(json_decode($sOrders));
        $oOrder = $lOrders->where('id', $area_id)->first();
        $orders = $oOrder->orders;

        return $orders;
    }
}