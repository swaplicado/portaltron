<?php namespace App\Utils;
      use App\Constants\SysConst;
      use Carbon\Carbon;
      use DB;
      use CfdiUtils\Cfdi;
      use CfdiUtils\Nodes\NodeInterface;

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
                            // ->whereYear('d.created_at', $year)
                            ->where('d.provider_id_n', $provider_id)
                            ->where('com.is_deleted', 0)
                            ->select(
                                'd.id_dps',
                                'd.type_doc_id',
                                'd.ext_id_year',
                                'd.ext_id_doc',
                                'd.serie_n',
                                'd.num_ref_n',
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
                    ->leftJoin('providers as prov', 'prov.id_provider', '=', 'd.provider_id_n')
                    ->leftJoin('purchase_orders as p', 'p.dps_id', '=', 'com.reference_doc_n')
                    ->leftJoin('dps as d2', 'd2.id_dps', '=', 'p.dps_id')
                    ->leftJoin('areas as a', 'a.id_area', '=', 'd.area_id')
                    ->join('vobo_dps as v', 'v.dps_id', '=', 'd.id_dps')
                    ->whereIn('v.area_id', $area_id)
                    ->where('v.is_deleted', 0)
                    ->whereIn('v.check_status', [SysConst::VOBO_REVISION, SysConst::VOBO_REVISADO])
                    ->whereIn('d.type_doc_id', $lTypes)
                    ->where('d.is_deleted', 0)
                    ->where('prov.is_deleted', 0);
                        // ->whereYear('d.created_at', $year);
        if($provider_id != 0){
            $lDps = $lDps->where('d.provider_id_n', $provider_id);
        }else{
            $lDps = $lDps->where('d.provider_id_n', '!=', null);
        }
                
        $lDps = $lDps->where('com.is_deleted', 0)
                        ->select(
                            'd.id_dps',
                            'd.type_doc_id',
                            'd.ext_id_year',
                            'd.ext_id_doc',
                            'd.serie_n',
                            'd.num_ref_n',
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
                            'prov.provider_name',
                        )
                        ->get();

        return $lDps;
    }

    public static function getlDpsReferences($dps_id){
        $lDpsReferences = DB::table('dps_references AS dps')
                                ->join('dps AS original','original.id_dps','=','dps.dps_id')
                                ->leftJoin('dps AS ref','ref.id_dps','=','dps.reference_doc')
                                ->where('dps.dps_id', $dps_id)
                                ->where('dps.is_deleted', 0)
                                ->select(
                                    'dps.id_dps_reference AS idDps',
                                    'ref.serie_n AS serie',
                                    'ref.num_ref_n AS folio',
                                    'ref.folio_n AS ref',
                                    'dps.reference_serie_n',
                                    'dps.reference_num_ref_n',
                                    'dps.reference_folio_n',
                                )
                                ->get();
        return $lDpsReferences;
    }
    // se ocupa enviar 
    public static function transformToString($lDpsReferences,$reference = "ref"){
        $toVisualice = '';
        foreach($lDpsReferences AS $ref){
            if($toVisualice == ''){
                $toVisualice = $toVisualice.$ref->$reference;
            }else{
                $toVisualice = $toVisualice.', '.$ref->$reference;
            }
        }

        return $toVisualice;
    }

    public static function getlDpsOmisionArea($lTypes, $provider_id = 0){
        $config = \App\Utils\Configuration::getConfigurations();
        $lOmisionAreaDps = collect($config->lOmisionAreaDps);
        $lAreas = $lOmisionAreaDps->whereIn('type', $lTypes)->pluck('id');

        $lDps = DB::table('dps as d')
                    ->join('dps_complementary as com', 'd.id_dps', '=', 'com.dps_id')
                    ->join('type_doc as t', 't.id_type', '=', 'd.type_doc_id')
                    ->join('status_dps as s', 's.id_status_dps', '=', 'd.status_id')
                    ->leftJoin('purchase_orders as p', 'p.id_purchase_order', '=', 'com.reference_doc_n')
                    ->leftJoin('dps as d2', 'd2.id_dps', '=', 'p.dps_id')
                    ->leftJoin('areas as a', 'a.id_area', '=', 'd.area_id')
                    ->leftJoin('providers as prov', 'prov.id_provider', '=', 'd.provider_id_n')
                    ->whereIn('d.type_doc_id', $lTypes)
                    ->where('d.is_deleted', 0)
                    ->whereIn('d.area_id', $lAreas);

        if($provider_id != 0){
            $lDps = $lDps->where('d.provider_id_n', $provider_id);
        }

        $lDps = $lDps->where('com.is_deleted', 0)
                    ->select(
                        'd.id_dps',
                        'd.type_doc_id',
                        'd.ext_id_year',
                        'd.ext_id_doc',
                        'd.serie_n',
                        'd.num_ref_n',
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
                        'prov.provider_name'
                    )
                    ->get();

        return $lDps;
    }

    public static function validateXml(
        $xml, 
        $oProvider, 
        $oCompany, 
        $aReference = [], 
        $lConditions = [ 
            SysConst::EMISOR_RFC, 
            SysConst::RECEPTOR_RFC, 
            SysConst::DATE__XML,
            SysConst::EMISOR_REGIMEN_FISCAL,
            SysConst::RECEPTOR_REGIMEN_FISCAL,
            SysConst::USO_CFDI,
            SysConst::METODO_PAGO
        ]
    ) {
        $config = \App\Utils\Configuration::getConfigurations();
        $body = null;
        $withHtml = false;
        $lErrors = [];
        $success = true;
        $message = "ok";
        $lDpsReferences = [];

        if (count($aReference) > 0) {
            foreach ($aReference as $reference) {
                $queryParams = [
                    'id_year' => $reference['ext_id_year'],
                    'id_doc' => $reference['ext_id_doc'],
                    'id_user' => 1
                ];
                $result = AppLinkUtils::requestAppLink($config->AppLinkRouteGetDpsByPk, "GET", \Auth::user(), $body, true, $queryParams);
                $data = $result->data;
                if (is_null($data)) {
                    $success = false;
                    $lErrors[] = 'No se encontró la referencia: <span style="font-weight: bold; ">' . $reference['folio_n'] . '</span>';
                    continue;
                }

                if (isset($data->oDpsHeader->oCfd->payMethod) && isset($data->oDpsHeader->oCfd->cfdUse)) {
                    $lDpsReferences[] =  ['idYear' => $data->idYear, 'idDoc' => $data->idDoc, 'payMethod' => $data->oDpsHeader->oCfd->payMethod, 'usoCfdi' => $data->oDpsHeader->oCfd->cfdUse, 'folio' => $reference['folio_n']];
                } else {
                    $lDpsReferences[] =  ['idYear' => $data->idYear, 'idDoc' => $data->idDoc, 'payMethod' => null, 'usoCfdi' => null, 'folio' => $reference['folio_n']];
                }
    
            }
    
            if (count($lDpsReferences) == 0) {
                
                $withHtml = true;
                $message = '<div style="text-align: left;"><span>Lamentablemente, el comprobante proporcionado no cumple con los siguientes aspectos: </span><br>';
                $message = $message . '<ul style="padding-left: 20px; margin-top: 10px;">';
                foreach($lErrors AS $error){
                    $message = $message . '<li>'.$error.'</li>';
                }
                $message = $message . '</ul></div>';
    
                return json_encode(['success' => $success, 'message' => $message, 'withHtml' => $withHtml]);
            }
        }

        // Convertir XML a array
        $xmlContent = file_get_contents($xml->getRealPath());
        
        $cfdi = \CfdiUtils\Cfdi::newFromString($xmlContent);
        $cfdi->getVersion(); // (string)
        $cfdi->getDocument(); // clon del objeto DOMDocument
        $cfdi->getSource(); // (string) <cfdi:Comprobante...
        $complemento = $cfdi->getNode(); // Nodo de trabajo del nodo cfdi:Comprobante

        if(in_array(SysConst::EMISOR_RFC, $lConditions)){
            $oEmisor = $complemento->searchNodes('cfdi:Emisor');
            foreach ($oEmisor as $emisor) {
                $rfcEmisor = $emisor['Rfc'];
            }
    
            if($rfcEmisor != $oProvider->provider_rfc){
                $success = false;
                $lErrors[] = 'El RFC del emisor, <span style="font-weight: bold; ">' . $rfcEmisor . '</span>, es incorrecto.';
            }
        }

        if(in_array(SysConst::EMISOR_REGIMEN_FISCAL, $lConditions)){
            $oEmisor = $complemento->searchNodes('cfdi:Emisor');
            foreach ($oEmisor as $emisor) { 
                $regimenFiscal = $emisor['RegimenFiscal'];
            }
    
            $oProvider_regimen_fiscal = DB::table('fiscal_regime')
                                            ->where('id', $oProvider->provider_fiscal_regime_id)
                                            ->first();

            if (is_null($oProvider_regimen_fiscal)) {
                $success = false;
                $lErrors[] = 'No tienes régimen fiscal registrado en sistema.';
            } else {
                if($regimenFiscal != $oProvider_regimen_fiscal->key){

                    $oRegimenFiscal = \DB::table('fiscal_regime')
                                            ->where('key', $regimenFiscal)
                                            ->first();

                    $success = false;
                    $lErrors[] = 'El régimen fiscal del emisor, <span style="font-weight: bold; ">' . 
                                    $oRegimenFiscal->key . ' - ' . $oRegimenFiscal->name . '</span>, es incorrecto.';
                }
            }
        }

        if (in_array(SysConst::RECEPTOR_RFC, $lConditions)) {
            $oReceptor = $complemento->searchNodes('cfdi:Receptor');
            foreach ($oReceptor as $receptor) {
                $rfcReceptor = $receptor['Rfc'];
            }
    
            if($rfcReceptor != $oCompany->company_rfc){
                $success = false;
                $lErrors[] = 'El RFC del receptor, <span style="font-weight: bold; ">' . $rfcReceptor . '</span>, es incorrecto.';
            }
        }

        if (in_array(SysConst::RECEPTOR_REGIMEN_FISCAL, $lConditions)) {
            $oReceptor = $complemento->searchNodes('cfdi:Receptor');
            foreach ($oReceptor as $receptor) {
                $RegimenFiscalReceptor = $receptor['RegimenFiscalReceptor'];
            }

            $oCompany_regimen_fiscal = DB::table('fiscal_regime')
                                            ->where('id', $oCompany->company_fiscal_regime_id)
                                            ->first();
    
            if (is_null($oCompany_regimen_fiscal)) {
                $success = false;
                $lErrors[] = 'El régimen fiscal del receptor no se encuentra registrado en el sistema.';
            } else {
                if($RegimenFiscalReceptor != $oCompany_regimen_fiscal->key){

                    $oRegimenFiscal = \DB::table('fiscal_regime')
                                            ->where('key', $RegimenFiscalReceptor)
                                            ->first();

                    $success = false;
                    $lErrors[] = 'El régimen fiscal del receptor, <span style="font-weight: bold; ">' . 
                                    $oRegimenFiscal->key . ' - ' . $oRegimenFiscal->name . '</span>, es incorrecto.';
                }
            }
        }

        if(in_array(SysConst::DATE__XML, $lConditions)){
            $fecha = $complemento['Fecha'];
    
            $now = Carbon::now();
            $oFecha = Carbon::createFromFormat('Y-m-d\TH:i:s', $fecha);
            if($oFecha->month != $now->month || $oFecha->year != $now->year){
                $success = false;
                $sFecha = $oFecha->format('d-m-Y');
                $lErrors[] = 'La fecha de emisión, <span style="font-weight: bold; ">' . dateUtils::formatDate($oFecha->format('d-m-Y'), 'D-m-Y') . '</span>, no corresponde al mes actual.';
            }
        }

        if (in_array(SysConst::USO_CFDI, $lConditions)) {
            $oReceptor = $complemento->searchNodes('cfdi:Receptor');
            foreach ($oReceptor as $receptor) {
                $UsoCFDI = $receptor['UsoCFDI'];
            }

            foreach ($lDpsReferences as $reference) {
                if (!is_null($reference['usoCfdi'])) {
                    if ($reference['usoCfdi'] != $UsoCFDI) {
    
                        $oUsoCfdi = \DB::table('uso_cfdi')
                                        ->where('key', $UsoCFDI)
                                        ->first();
    
                        $success = false;
                        $lErrors[] = 'El uso del CFDI, <span style="font-weight: bold; ">' . 
                                        $oUsoCfdi->key . ' - ' . $oUsoCfdi->name . '</span>, es distinto al de la referencia ' . $reference['folio'] . '.';
                    }
                }
            }
        }

        if (in_array(SysConst::METODO_PAGO, $lConditions)) {
            $MetodoPago = $complemento['MetodoPago'];

            foreach ($lDpsReferences as $reference) {
                if (!is_null($reference['usoCfdi'])) {
                    if ($reference['payMethod'] != $MetodoPago) {
    
                        $oMetodoPago = \DB::table('metodo_pago')
                                        ->where('key', $MetodoPago)
                                        ->first();
    
                        $success = false;
                        $lErrors[] = 'El método de pago, <span style="font-weight: bold; ">' . 
                                        $oMetodoPago->key . ' - ' . $oMetodoPago->name . '</span>, es distinto al de la referencia ' . $reference['folio'] . '.';
                    }
                }
            }
        }

        if (!$success) {
            $withHtml = true;
            $message = '<div style="text-align: left;"><span>Lamentablemente, el comprobante proporcionado no cumple con los siguientes aspectos: </span><br>';
            $message = $message . '<ul style="padding-left: 20px; margin-top: 10px;">';
            foreach($lErrors AS $error){
                $message = $message . '<li>'.$error.'</li>';
            }
            $message = $message . '</ul></div>';
        }

        return json_encode(['success' => $success, 'message' => $message, 'withHtml' => $withHtml]);
    }
}