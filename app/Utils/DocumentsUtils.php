<?php namespace App\Utils;
use DB;

class DocumentsUtils {

    // lProviders debe tener un campo que se llame id_provider para que funcione
    public static function getNumberPendigDocs( $lProviders = [], $area_id = 0 ){
        for( $i = 0 ; count($lProviders) > $i ; $i++ ){
            $oProviders = DB::table('vobo_docs')
                            ->join('docs_url', 'docs_url.id_doc_url', '=', 'vobo_docs.doc_url_id')
                            ->join('prov_docs', 'prov_docs.id_prov_doc', '=', 'docs_url.prov_doc_id')
                            ->where('prov_docs.prov_id',$lProviders[$i]->id_provider)
                            ->where('vobo_docs.is_accept',false)
                            ->where('vobo_docs.is_reject',false)
                            ->where('vobo_docs.check_status',1);
            
            if( $area_id != 0 ){
                $oProviders = $oProviders->where('vobo_docs.area_id',$area_id);
            }
            
            $oProviders = $oProviders->get();
            
            $lProviders[$i]->number_pen_doc = count($oProviders);
        }

        return $lProviders;
    }

    // lProviders debe tener un campo que se llame id_provider para que funcione
    public static function havePendigDocs( $lProviders = [], $area_id = 0 ){
        for( $i = 0 ; count($lProviders) > $i ; $i++ ){
            $oProviders = DB::table('vobo_docs')
                            ->join('docs_url', 'docs_url.id_doc_url', '=', 'vobo_docs.doc_url_id')
                            ->join('prov_docs', 'prov_docs.id_prov_doc', '=', 'docs_url.prov_doc_id')
                            ->where('prov_docs.prov_id',$lProviders[$i]->id_provider)
                            ->where('vobo_docs.is_accept',false)
                            ->where('vobo_docs.is_reject',false)
                            ->where('vobo_docs.check_status',1);
                            
            if( $area_id != 0 ){
                $oProviders = $oProviders->where('vobo_docs.area_id',$area_id);
            }
                            
            $oProviders = $oProviders->get();
            
            if( count($oProviders) == 0 ){
                $lProviders[$i]->have_pen_doc = 0;
            }else{
                $lProviders[$i]->have_pen_doc = 1;
            }
            
        }
        return $lProviders;
    }

    public static function getPendigdDocs( $id_provider = 0 , $id_user = 0){
        $oProviders = DB::table('vobo_docs')
                        ->join('doc_url', 'doc_url.id_doc_url', '=', 'vobo_docs.doc_url_id')
                        ->join('prov_docs', 'prov_docs.id_prov_docs', '=', 'doc_url.prov_doc_id')
                        ->join('request_type_docs', 'request_type_docs.id_request_type_doc', '=', 'prov_docs.request_type_doc_id')
                        ->where('prov_docs.prov_id',$id_provider)
                        ->where('vobo_docs',$id_user)
                        ->where('vobo_docs.is_accept',false)
                        ->where('vobo_docs.is_reject',false)
                        ->get();

        return $oProviders;
    }
}