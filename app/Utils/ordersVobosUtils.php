<?php namespace App\Utils;

class ordersVobosUtils {
    /**
     * Metodo que regresa el order para dar vobo a los documentos de un proveedor
     * Recibe el area del proveedor y regresa un objeto con el order
     */
    public static function getProviderDocsOrderToVobo($provider_area_id){
        $config = \App\Utils\Configuration::getConfigurations();
        $sOrders =  json_encode($config->orders);
        $lOrders = collect(json_decode($sOrders));

        $oOrder = $lOrders->where('id', $provider_area_id)->first();
        $orders = collect($oOrder->orders);
        
        return $orders;
    }

    /**
     * Metodo que regresa el id del area hijo de acuerdo al order para dar vobo a los docs del proveedor
     * recibe el area del proveedor y el area del usuario
     */
    public static function getProviderDocsChildArea($provider_area_id, $my_area_id){
        $config = \App\Utils\Configuration::getConfigurations();
        if($my_area_id != $config->fatherArea && $provider_area_id != $config->fatherArea){
            $lOrders = ordersVobosUtils::getProviderDocsOrderToVobo($provider_area_id);
            $myOrder = $lOrders->where('area', $my_area_id)->first();
            $oOrder = $lOrders->where('order', ($myOrder->order + 1))->first();
            $child_area_id = $oOrder->area;
        }else{
            $child_area_id = 0;
        }

        return $child_area_id;
    }

    /**
     * Metodo que regresa el order para dar vobo a los dps
     */
    public static function getDpsOrder($type_dps_id, $area_id){
        if(is_null($area_id)){
            $area_id = 0;
        }

        $config = \App\Utils\Configuration::getConfigurations();
        $sOrders =  json_encode($config->ordersDps);
        $lOrders = collect(json_decode($sOrders));
        $oOrder = $lOrders->where('type', $type_dps_id)->where('id', $area_id)->first();
        $orders = $oOrder->orders;

        return $orders;
    }

    /**
     * Metodo que regresa el id del area hijo de acuerdo al order para dar vobo a los docs del proveedor
     * recibe el area del proveedor y el area del usuario
     */
    public static function getDpsChildArea($type_dps_id, $area_id){
        $config = \App\Utils\Configuration::getConfigurations();
        if($area_id != $config->fatherArea){
            $lOrders = collect(ordersVobosUtils::getDpsOrder($type_dps_id, $area_id));
            $myOrder = $lOrders->where('area', $area_id)->first();
            $oOrder = $lOrders->where('order', ($myOrder->order + 1))->first();
            if(!is_null($oOrder)){
                $child_area_id = $oOrder->area;
            }else{
                $child_area_id = 0;
            }
        }else{
            $child_area_id = 0;
        }

        return $child_area_id;
    }
}