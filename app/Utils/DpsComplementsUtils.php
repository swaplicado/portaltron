<?php namespace App\Utils;
      use App\Constants\SysConst;

class DpsComplementsUtils {
    public static function getlDpsComplements($year, $provider_id, $lTypes){
        $lDpsComp = \DB::table('dps as d')
                            ->join('dps_complementary as com', 'd.id_dps', '=', 'com.dps_id')
                            ->join('type_doc as t', 't.id_type', '=', 'd.type_doc_id')
                            ->join('status_dps as s', 's.id_status_dps', '=', 'd.status_id')
                            ->leftJoin('purchase_orders as p', 'p.id_purchase_order', '=', 'com.reference_doc_n')
                            ->leftJoin('dps as d2', 'd2.id_dps', '=', 'p.dps_id')
                            ->leftJoin('areas as a', 'a.id_area', '=', 'd.area_id')
                            ->whereIn('d.type_doc_id', $lTypes)
                            ->where('d.is_deleted', 0)
                            ->whereYear('d.created_at', $year)
                            ->where('d.provider_id_n', $provider_id)
                            ->where('com.is_deleted', 0)
                            ->select(
                                'd.id_dps',
                                'd.type_doc_id',
                                'd.ext_id_year',
                                'd.ext_id_doc',
                                'd.folio_n',
                                'd.pdf_url_n',
                                'd.xml_url_n',
                                'd.status_id',
                                'd.is_deleted',
                                'd.area_id',
                                'com.reference_doc_n',
                                'com.provider_comment_n',
                                'com.requester_comment_n',
                                'com.provider_date_n',
                                'com.requester_date_n',
                                'com.is_opened',
                                't.name_type as type',
                                's.name as status',
                                'd2.folio_n as reference_folio',
                                'd.created_at',
                                'a.name_area'
                            )
                            ->get();

        return $lDpsComp;
    }

    public static function getlDpsComplementsToVobo($year, $provider_id, $lTypes, $area_id){
        $lDps = \DB::table('dps as d')
                    ->join('dps_complementary as com', 'd.id_dps', '=', 'com.dps_id')
                    ->join('type_doc as t', 't.id_type', '=', 'd.type_doc_id')
                    ->join('status_dps as s', 's.id_status_dps', '=', 'd.status_id')
                    ->leftJoin('purchase_orders as p', 'p.id_purchase_order', '=', 'com.reference_doc_n')
                    ->leftJoin('dps as d2', 'd2.id_dps', '=', 'p.dps_id')
                    ->leftJoin('areas as a', 'a.id_area', '=', 'd.area_id')
                    ->join('vobo_dps as v', 'v.dps_id', '=', 'd.id_dps')
                    ->where('v.area_id', $area_id)
                    ->whereIn('v.check_status', [SysConst::VOBO_REVISION, SysConst::VOBO_REVISADO])
                    ->whereIn('d.type_doc_id', $lTypes)
                        ->where('d.is_deleted', 0)
                        ->whereYear('d.created_at', $year)
                        ->where('d.provider_id_n', $provider_id)
                        ->where('com.is_deleted', 0)
                        ->select(
                            'd.id_dps',
                            'd.type_doc_id',
                            'd.ext_id_year',
                            'd.ext_id_doc',
                            'd.folio_n',
                            'd.pdf_url_n',
                            'd.xml_url_n',
                            'd.status_id',
                            'd.is_deleted',
                            'd.area_id',
                            'com.reference_doc_n',
                            'com.provider_comment_n',
                            'com.requester_comment_n',
                            'com.provider_date_n',
                            'com.requester_date_n',
                            'com.is_opened',
                            't.name_type as type',
                            's.name as status',
                            'd2.folio_n as reference_folio',
                            'd.created_at',
                            'a.name_area',
                            'v.check_status',
                            'v.is_accept',
                            'v.is_reject',
                        )
                        ->get();

        return $lDps;
    }
}