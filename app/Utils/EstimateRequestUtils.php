<?php namespace App\Utils;

use App\Models\SDocs\EstimateRequest;

class EstimateRequestUtils {
    public static function insertEstimateRequest($lEstimateRequest){
        try {
            \DB::beginTransaction();
            foreach($lEstimateRequest as $er){
                $oER = EstimateRequest::where('external_id', $er->idEstimateRequest)->first();

                if(is_null($oER)){
                    $oER = new EstimateRequest();
                    $oER->external_id = $er->idEstimateRequest;
                    $oER->is_opened = 0; 
                    $oER->status = 0;
                    $oER->is_deleted = 0;
                    $oER->created_by = \Auth::user()->id;
                    $oER->updated_by = \Auth::user()->id;
                    
                    $oER->save();
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

?>