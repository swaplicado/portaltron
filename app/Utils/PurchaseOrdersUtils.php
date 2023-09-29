<?php namespace App\Utils;

use App\Models\SDocs\Dps;
use App\Models\SDocs\PurchaseOrders;

class PurchaseOrdersUtils {
    public static function insertPurchaseOrders($lPurchaseOrders){
        try {
            \DB::beginTransaction();
            foreach($lPurchaseOrders as $oc){
                $oDps = Dps::where('ext_id_year', $oc->idYear)->where('ext_id_doc', $oc->idDoc)->first();

                if(is_null($oDps)){
                    $oDps = new Dps();
                    $oDps->type_doc_id = SysConst::DOC_TYPE_PURCHASE_ORDER;
                    $oDps->ext_id_year = $oc->idYear;
                    $oDps->ext_id_doc = $oc->idDoc;
                    $oDps->status_id = SysConst::DOC_STATUS_NUEVO;
                    $oDps->is_deleted = 0;
                    $oDps->created_by = \Auth::user()->id;
                    $oDps->updated_by = \Auth::user()->id;
                    $oDps->save();
        
                    $oPurchaseOrder = new PurchaseOrders();
                    $oPurchaseOrder->dps_id = $oDps->id_dps;
                    $oPurchaseOrder->is_opened = 0;
                    $oPurchaseOrder->is_deleted = 0;
                    $oPurchaseOrder->created_by = \Auth::user()->id;
                    $oPurchaseOrder->updated_by = \Auth::user()->id;
                    $oPurchaseOrder->save();
                }
            }
            \DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th);
            \DB::rollBack();
            return false;
        }

        return true;
    }
}