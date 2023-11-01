<?php namespace App\Utils;
      use App\Models\SDocs\Dps;
      use App\Models\SDocs\DpsComplementary;
      use App\Models\SDocs\DpsReference;
      use DB;

class fixesUtils {

    /**
     * Separa la serie y folio, hace la inserción en la bd.
     * @param 
     * @return true|false True se hizo la inserción correctamente, o false en caso de error.
     */
    public static function separateReferencia(){
        try{
            $document = DB::table("dps")
                            ->where("is_deleted","=",0)
                            ->where("num_ref_n","=",null)
                            ->where("folio_n","!=",null)
                            ->where("type_doc_id","=",1)
                            ->get();
            $haveSerie = false;
            DB::beginTransaction();
            foreach ($document as $doc) {
                //ver si contiene serie en el folio
                $haveSerie = strpos($doc->folio_n,'-');

                $auxFolio = explode('-', $doc->folio_n);

                
                $oc = Dps::where('id_dps','=', $doc->id_dps)->first();
                if($haveSerie === false){
                    $oc->serie_n = null;
                    $oc->num_ref_n = $auxFolio[0];    
                }else{
                    $oc->serie_n = $auxFolio[0];
                    $oc->num_ref_n = $auxFolio[1];
                }
                $oc->update();
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            \Log::error($e);
            return false;
        }
        
        return true;
    }

    public static function doTableRef(){
        try{
            $dpsCorrect = DB::table('dps_references')
                            ->where('is_deleted',0)
                            ->distinct()
                            ->select('dps_id')
                            ->get();
            $dpsCorrect = $dpsCorrect->pluck('dps_id');
            
            $dpsToProcess = DB::table('dps')
                                ->join('dps_complementary AS com','com.dps_id','=','dps.id_dps')
                                ->join('dps AS ref','ref.id_dps','=','com.reference_doc_n')
                                ->whereNotIn('dps.id_dps', $dpsCorrect->toArray())
                                ->where('dps.is_deleted',0)
                                ->select('dps.id_dps AS idDps', 'com.reference_doc_n AS ref', 'ref.serie_n AS serie', 'ref.num_ref_n AS numRef', 'ref.folio_n AS folio')
                                ->get();
            DB::beginTransaction();
            foreach ($dpsToProcess as $dps) {

                $dps_ref = new DpsReference();
                $dps_ref->dps_id = $dps->idDps;
                $dps_ref->reference_doc = $dps->ref;
                $dps_ref->reference_serie_n = $dps->serie;
                $dps_ref->reference_num_ref_n = $dps->numRef;
                $dps_ref->reference_folio_n = $dps->folio;
                $dps_ref->is_deleted = 0;

                $dps_ref->save();

            }
            DB::commit();
            return true;

        }catch(\Exception $e){
            DB::rollBack();
            \Log::error($e);
            return false;
        }
    }
}

?>