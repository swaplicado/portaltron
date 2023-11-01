<?php

namespace App\Http\Controllers\SDocs;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Mail\newDpsMail;
use App\Mail\rejectDpsMail;
use App\Mail\voboDpsMail;
use App\Models\Areas\Areas;
use App\Models\SDocs\Dps;
use App\Models\SDocs\DpsComplementary;
use App\Models\SDocs\DpsReasonRejection;
use App\Models\SDocs\DpsReference;
use App\Models\SDocs\StatusDps;
use App\Models\SDocs\TypeDoc;
use App\Models\SDocs\VoboDps;
use App\Models\SProviders\SProvider;
use App\Utils\dateUtils;
use App\Utils\DpsComplementsUtils;
use App\Utils\FilesUtils;
use App\Utils\ordersVobosUtils;
use App\Utils\SProvidersUtils;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class dpsComplementaryController extends Controller
{
    public static function providerIndex(){
        try {
            $oProvider = \Auth::user()->getProviderData();
            $year = Carbon::now()->format('Y');

            $lDpsComp = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO]);
            
            foreach ($lDpsComp as $dps) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($dps->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences);
                $dps->reference_string = $Sreference; 
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }

            $lStatus = StatusDps::where('type_doc_id', SysConst::DOC_TYPE_FACTURA)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_status_dps as id',
                                    'name as text'
                                )
                                ->get()
                                ->toArray();
    
            array_unshift($lStatus, ['id' => 0, 'text' => 'Todos']);
    
            $lTypes = TypeDoc::whereIn('id_type', [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO])
                            ->where('is_deleted', 0)
                            ->select(
                                'id_type as id',
                                'name_type as text'
                            )
                            ->get()
                            ->toArray();
    
            $lAreas = Areas::where('is_deleted', 0)
                            ->select(
                                'id_area as id',
                                'name_area as text'
                            )
                            ->get()
                            ->toArray();
            

            $config = \App\Utils\Configuration::getConfigurations();
            $lAreas = Areas::whereIn('id_area', $config->areasToRegisterProvider)
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->get();

            $showAreaDps = $config->showAreaDps;
            $requireAreaDps = $config->requireAreaDps;

            if($config->useSerie != 1){
                $default_area_id = $oProvider->area_id;
            }else{
                $default_area_id = null;   
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('dpsComplementary.dps_complementary')->with('lDpsComp', $lDpsComp)
                                                    ->with('year', $year)
                                                    ->with('lStatus', $lStatus)
                                                    ->with('lTypes', $lTypes)
                                                    ->with('lAreas', $lAreas)
                                                    ->with('default_area_id', $default_area_id)
                                                    ->with('showAreaDps', $showAreaDps)
                                                    ->with('requireAreaDps',$requireAreaDps);
    }

    public function saveComplementary(Request $request){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $oProvider = \Auth::user()->getProviderData();
            $type_id = $request->type_id;
            
            $serieoc = $request->serieoc;
            $folio = $request->folio;
            $haveSerie = strpos($request->folio,'-');
            $auxFolio = explode('-', $request->folio);
            $area_id = $request->area_id;
            $year = $request->year;

            $config = \App\Utils\Configuration::getConfigurations();

            $references = explode(',', $serieoc);
            //se utilizará para saber si es la primera vez que entras en el foreach
            $auxCont = 0;
            $aReference = [];
            foreach($references as $reference){
                $reference = trim($reference);
                $oReference = Dps::where('folio_n', $reference)
                            ->where('provider_id_n', $oProvider->id_provider)
                            ->where('is_deleted', 0)    
                            ->first(); 
                if(is_null($oReference)){
                    return json_encode(['success' => false, 'message' => 'No se encuentra el documento con la referencia '.$reference , 'icon' => 'warning']);
                }    
                if($auxCont == 0){
                    $serieComparacion = $oReference->serie_n;
                }else{
                    if($serieComparacion != $oReference->serie_n ){
                        return json_encode(['success' => false, 'message' => 'No se puede enlazar a documentos con series diferentes' , 'icon' => 'warning']);    
                    }
                }
                $auxCont++; 

                array_push($aReference, $oReference);
            }
            if(is_null($area_id) || $area_id == "null"){
                if($config->useSerie != 1){
                    if($config->requireAreaDps){
                        return json_encode(['success' => false, 'message' => "Debes seleccionar un área de destino", 'icon' => 'info']);
                    }else{
                        $lOmisionAreaDps = collect($config->lOmisionAreaDps);
                        $omisionArea = $lOmisionAreaDps->where('type', SysConst::DOC_TYPE_FACTURA)->first();
                        $area_id = $omisionArea->id != null ? $omisionArea->id : $config->defaultAreaDps;
                        if(is_null($area_id)){
                            return json_encode(['success' => false, 'message' => "No se encontró un área de destino", 'icon' => 'info']);
                        }
                    }
                }else{
                    $serie_area = DB::table('series')
                        ->where('type_doc_id',$type_id)
                        ->where('code', $serieComparacion)
                        ->where('is_deleted', 0)
                        ->first();

                    if(is_null($serie_area)){
                        return json_encode(['success' => false, 'message' => "La serie especificada no existe", 'icon' => 'info']);
                    }
                    $area_id = $serie_area->area_id_n;
                }
            }

            $orders = ordersVobosUtils::getDpsOrder($type_id, $area_id);

            $pdf = $request->file('pdf');
            if(is_null($pdf)){
                return json_encode(['success' => false, 'message' => 'Debe ingresar el pdf', 'icon' => 'warning']);
            }
            $result = FilesUtils::validateFile($pdf, 'pdf', '5 MB');
            if(!$result[0]){
                return json_encode(['success' => false, 'message' => $result[1], 'icon' => 'error']);
            }

            $xml = $request->file('xml');
            if(is_null($xml)){
                return json_encode(['success' => false, 'message' => 'Debe ingresar el xml', 'icon' => 'warning']);
            }
            $result = FilesUtils::validateFile($xml, 'xml', '5 MB');
            if(!$result[0]){
                return json_encode(['success' => false, 'message' => $result[1], 'icon' => 'error']);
            }

            DB::beginTransaction();

            if($type_id == SysConst::DOC_TYPE_FACTURA){
                $filePdfName = 'FAC_'.$folio.'_'.$oProvider->provider_rfc.'_'.time().'.'.$pdf->extension();
                $resPdf = Storage::disk('facturas')->putFileAs('/', $pdf, $filePdfName);
                $rutaPdf = Storage::disk('facturas')->url($filePdfName);

                $fileXmlName = 'XML_'.$folio.'_'.$oProvider->provider_rfc.'_'.time().'.'.$xml->extension();
                $resXml = Storage::disk('facturas')->putFileAs('/', $xml, $fileXmlName);
                $rutaXml = Storage::disk('facturas')->url($fileXmlName);
            }else if($type_id == SysConst::DOC_TYPE_NOTA_CREDITO){
                $filePdfName = 'NC_'.$folio.'_'.$oProvider->provider_rfc.'_'.time().'.'.$pdf->extension();
                $resPdf = Storage::disk('notas_credito')->putFileAs('/', $pdf, $filePdfName);
                $rutaPdf = Storage::disk('notas_credito')->url($filePdfName);

                $fileXmlName = 'XML_'.$folio.'_'.$oProvider->provider_rfc.'_'.time().'.'.$xml->extension();
                $resXml = Storage::disk('notas_credito')->putFileAs('/', $xml, $fileXmlName);
                $rutaXml = Storage::disk('notas_credito')->url($fileXmlName);
            }

            $oDps = new Dps();
            $oDps->type_doc_id = $type_id;
            $oDps->provider_id_n = $oProvider->id_provider;
            if($haveSerie === false){
                $oDps->serie_n = null;
                $oDps->num_ref_n = $auxFolio[0];
            }else{
                $oDps->serie_n = $auxFolio[0];
                $oDps->num_ref_n = $auxFolio[1];
            }
            
            $oDps->folio_n = $folio;
            $oDps->area_id = $area_id;
            $oDps->pdf_url_n = $rutaPdf;
            $oDps->xml_url_n = $rutaXml;
            $oDps->status_id = $type_id == SysConst::DOC_TYPE_FACTURA ? SysConst::FACTURA_STATUS_NUEVO : 
                                            ($type_id == SysConst::DOC_TYPE_NOTA_CREDITO ? SysConst::NOTA_CREDITO_STATUS_NUEVO : 1);
            $oDps->is_deleted = 0;
            $oDps->created_by = \Auth::user()->id;
            $oDps->updated_by = \Auth::user()->id;
            $oDps->save();

            $oDpsComp = new DpsComplementary();
            $oDpsComp->dps_id = $oDps->id_dps;
            $oDpsComp->reference_doc_n = $oReference->id_dps;
            $oDpsComp->is_opened = 1;
            $oDpsComp->is_deleted = 0;
            $oDpsComp->created_by = \Auth::user()->id;
            $oDpsComp->updated_by = \Auth::user()->id;
            $oDpsComp->save();

            foreach($orders as $order){
                $oVoboDps = new VoboDps();
                $oVoboDps->dps_id = $oDps->id_dps;
                $oVoboDps->area_id = $order->area;
                $oVoboDps->is_accept = 0;
                $oVoboDps->is_reject = 0;
                $oVoboDps->order = $order->order;
                $oVoboDps->check_status = $order->order == 1 ? SysConst::VOBO_REVISION : SysConst::VOBO_NO_REVISION;
                $oVoboDps->is_deleted = 0;
                $oVoboDps->created_by = 1;
                $oVoboDps->updated_by = 1;
                $oVoboDps->save();
            }

            foreach($aReference as $ref){
                $DpsReference = new DpsReference();
                $DpsReference->dps_id = $oDps->id_dps;
                $DpsReference->reference_doc = $ref->id_dps;
                $DpsReference->reference_serie_n = $ref->serie_n;
                $DpsReference->reference_num_ref_n = $ref->num_ref_n;
                $DpsReference->reference_folio_n = $ref->folio_n;
                $DpsReference->is_deleted = 0;
                $oVoboDps->created_by = 1;
                $oVoboDps->updated_by = 1;
                $DpsReference->save();
            }

            $lDpsComp = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO]);

            foreach ($lDpsComp as $dps) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($dps->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences);
                $dps->reference_string = $Sreference; 
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }
            DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        try {
            $order = collect($orders)->first();
            $oArea = Areas::findOrFail($order->area);

            Mail::to($oArea->email_n)->send(new newDpsMail(
                                                $oProvider->provider_short_name,
                                                $oDps->type_doc_id,
                                                "Factura",
                                                $oDps->folio_n,
                                                [1,2,3]
                                            )
                                        );
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => true, 'lDpsComp' => $lDpsComp, 'mailSuccess' => false, "message" => $th->getMessage(), "icon"=> "warning"]);
        }

        return json_encode(['success' => true, 'lDpsComp' => $lDpsComp, 'mailSuccess' => true]);
    }

    public function getDpsComplement(Request $request){
        try {
            $id_dps = $request->id_dps;

            $oDps = DB::table('dps as d')
                    ->join('dps_complementary as dc', 'd.id_dps', '=', 'dc.dps_id')
                    ->join('dps as d2', 'd2.id_dps', '=', 'dc.reference_doc_n')
                    ->where('d.id_dps', $id_dps)
                    ->where('dc.is_deleted', 0)
                    ->select(
                        'd.*',
                        'd2.folio_n as reference',
                        'dc.requester_comment_n',
                    )
                    ->first();
            $lDpsRef = DpsComplementsUtils::getlDpsReferences($id_dps);
            $SDpsRef = DpsComplementsUtils::transformToString($lDpsRef);

            $oDps->reference = $SDpsRef;

        } catch (\Throwable $th) {
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'oDps' => $oDps]);
    }

    public function getlDpsCompByYear(Request $request){
        try {
            $year = $request->year;

            $oProvider = \Auth::user()->getProviderData();
            $lDpsComp = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO]);
            foreach ($lDpsComp as $dps) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($dps->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences);
                $dps->reference_string = $Sreference; 
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsComp' => $lDpsComp]);
    }

    /**
     * Index para la vista de complementos para usuarios tipo manager
     */
    public function complementsManager(){
        try {

            $config = \App\Utils\Configuration::getConfigurations();
            $canSeeAll = $config->canSeeAll;
            $lOmisionAreaDps = collect($config->lOmisionAreaDps)->pluck('id');
            
            if(in_array(\Auth::user()->id,$canSeeAll)){
                $oArea = DB::table('areas')
                                ->where('is_deleted',0)
                                ->whereNotIn('id_area',$lOmisionAreaDps)
                                ->get(); 
                $oArea = $oArea->pluck('id_area');   
            }else{
                $oArea = collect([\Auth::user()->getArea()]);
                $oArea = $oArea->pluck('id_area'); 
            }
            
            $olProviders = SProvidersUtils::getlProviders($oArea->toArray());

            $lProviders = [];
            array_push($lProviders, ['id' => 0, 'text' => "Todos"]);
            foreach ($olProviders as $value) {
                array_push($lProviders, ['id' => $value->id_provider, 'text' => $value->provider_name]);
            }

            $year = Carbon::now()->format('Y');

            $lStatus = StatusDps::where('type_doc_id', SysConst::DOC_TYPE_FACTURA)
                            ->where('is_deleted', 0)
                            ->select(
                                'id_status_dps as id',
                                'name as text'
                            )
                            ->get()
                            ->toArray();

            array_unshift($lStatus, ['id' => 0, 'text' => 'Todos']);

            $lTypes = TypeDoc::whereIn('id_type', [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO])
                            ->where('is_deleted', 0)
                            ->select(
                                'id_type as id',
                                'name_type as text'
                            )
                            ->get()
                            ->toArray();
            
            $arrStatusFac = SysConst::statusTypesDoc['FACTURA'];
            $arrStatusNC = SysConst::statusTypesDoc['NOTA_CREDITO'];

            $lConstants = [
                'FACTURA' => SysConst::DOC_TYPE_FACTURA,
                'NOTA_CREDITO' => SysConst::DOC_TYPE_NOTA_CREDITO,
                'FAC_STATUS_NUEVO' => $arrStatusFac['NUEVO'],
                'FAC_STATUS_PENDIENTE' => $arrStatusFac['PENDIENTE'],
                'NC_STATUS_NUEVO' => $arrStatusNC['NUEVO'],
                'NC_STATUS_PENDIENTE' => $arrStatusNC['PENDIENTE'],
            ];

            $lDpsComp = DpsComplementsUtils::getlDpsComplementsToVobo($year, 0, 
                    [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->toArray());
                    foreach ($lDpsComp as $dps) {
                        $lDpsReferences = DpsComplementsUtils::getlDpsReferences($dps->id_dps);
                        $Sreference = DpsComplementsUtils::transformToString($lDpsReferences);
                        $dps->reference_string = $Sreference; 
                        $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
                    }

            $lAreas = Areas::whereIn('id_area', $config->areasToDps)
            ->where('is_deleted', 0)
            ->select(
                'id_area as id',
                'name_area as text'
            )
            ->get()
            ->toArray();

        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('dpsComplementary.dps_complementary_manager')->with('lProviders', $lProviders)
                                                                ->with('year', $year)
                                                                ->with('lStatus', $lStatus)
                                                                ->with('lTypes', $lTypes)
                                                                ->with('lConstants', $lConstants)
                                                                ->with('lDpsComp', $lDpsComp)
                                                                ->with('lAreas', $lAreas);
    }

    /**
     * Obtiene los complementos de un proveedor
     */
    public function getComplementsProvider(Request $request){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $canSeeAll = $config->canSeeAll;
            $lOmisionAreaDps = collect($config->lOmisionAreaDps)->pluck('id');
            
            if(in_array(\Auth::user()->id,$canSeeAll)){
                $oArea = DB::table('areas')
                                ->where('is_deleted',0)
                                ->whereNotIn('id_area',$lOmisionAreaDps)
                                ->get(); 
                $oArea = $oArea->pluck('id_area');   
            }else{
                $oArea = collect([\Auth::user()->getArea()]);
                $oArea = $oArea->pluck('id_area'); 
            }
            
            $provider_id = $request->provider_id;

            if($provider_id != 0){
                $oProvider = SProvider::findOrFail($provider_id);
                $provider_id = $oProvider->id_provider;
            }

            $year = $request->year;

            if(is_null($year)){
                $year = Carbon::now()->format('Y');
            }

            $lDpsComp = DpsComplementsUtils::getlDpsComplementsToVobo($year, $provider_id, 
            [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->toArray());
            foreach ($lDpsComp as $dps) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($dps->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences);
                $dps->reference_string = $Sreference; 
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsComp' => $lDpsComp]);
    }

    public function getDpsComplementManager(Request $request){
        try {
            $id_dps = $request->id_dps;
            $config = \App\Utils\Configuration::getConfigurations();
            $canSeeAll = $config->canSeeAll;
            $lOmisionAreaDps = collect($config->lOmisionAreaDps)->pluck('id');
            
            if(in_array(\Auth::user()->id,$canSeeAll)){
                $oArea = DB::table('areas')
                                ->where('is_deleted',0)
                                ->whereNotIn('id_area',$lOmisionAreaDps)
                                ->get(); 
                $oArea = $oArea->pluck('id_area');   
            }else{
                $oArea = collect([\Auth::user()->getArea()]);
                $oArea = $oArea->pluck('id_area'); 
            }
            $oDps = DB::table('dps as d')
                    ->join('dps_complementary as dc', 'dc.dps_id', '=', 'd.id_dps')
                    ->join('dps as d2', 'd2.id_dps', '=', 'dc.reference_doc_n')
                    ->join('vobo_dps as v', 'v.dps_id', '=', 'd.id_dps')
                    ->where('d.id_dps', $id_dps)
                    ->whereIn('v.area_id', $oArea->toArray())
                    ->where('d.is_deleted', 0)
                    ->select(
                        'd.*',
                        'd2.folio_n as reference',
                        'dc.requester_comment_n',
                        'v.is_accept',
                        'v.is_reject',
                        'v.order',
                        'v.check_status',
                    )
                    ->first();
            $lDpsRef = DpsComplementsUtils::getlDpsReferences($id_dps);
            $SDpsRef = DpsComplementsUtils::transformToString($lDpsRef);
        
            $oDps->reference = $SDpsRef;

            $lDpsReasons = DpsReasonRejection::where(function($query) use($oDps){
                                                $query->where('type_doc_id_n', $oDps->type_doc_id)->orWhere('type_doc_id_n', null);
                                            })
                                            ->where('is_active', 1)
                                            ->where('is_deleted', 0)
                                            ->orderBy('type_doc_id_n', 'desc')
                                            ->get()
                                            ->map(function ($item) {
                                                return [
                                                    'id' => $item->id_dps_reason_rejection,
                                                    'text' => $item->reason,
                                                ];
                                            });

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'oDps' => $oDps, 'lDpsReasons' => $lDpsReasons]);
    }

    public function setVoboComplement(Request $request){
        try {
            $mailStatus = '';
            $sendMail = false;

            $id_dps = $request->id_dps;
            $is_accept = $request->is_accept;
            $is_reject = $request->is_reject;
            $provider_id = $request->provider_id;
            $year = $request->year;
            $comments = $request->comments;

            $config = \App\Utils\Configuration::getConfigurations();
            $canSeeAll = $config->canSeeAll;
            $lOmisionAreaDps = collect($config->lOmisionAreaDps)->pluck('id');
            
            if(in_array(\Auth::user()->id,$canSeeAll)){
                $oArea = DB::table('areas')
                                ->where('is_deleted',0)
                                ->whereNotIn('id_area',$lOmisionAreaDps)
                                ->get(); 
                $oArea = $oArea->pluck('id_area');   
            }else{
                $oArea = collect([\Auth::user()->getArea()]);
                $oArea = $oArea->pluck('id_area'); 
            }
            $voboArea = \Auth::user()->getArea();

            DB::beginTransaction();

            $oDps = Dps::findOrFail($id_dps);
            
            $arrTypes = SysConst::lTypesDoc;
            $key = array_search($oDps->type_doc_id, $arrTypes);
            $arrStatus = SysConst::statusTypesDoc[$key];
            
            $statusKey = $is_accept == true ? 'APROBADO' : 'RECHAZADO';
            $status_id = $arrStatus[$statusKey];

            $oVobo = VoboDps::where('dps_id', $id_dps)->where('area_id', $voboArea->id_area)->first();
            $oVobo->user_id = \Auth::user()->id;
            $oVobo->is_accept = $is_accept;
            $oVobo->is_reject = $is_reject;
            $oVobo->date_accept_n = $is_accept == true ? Carbon::now()->toDateString() : null;
            $oVobo->date_rej_n = $is_reject == true ? Carbon::now()->toDateString() : null;
            $oVobo->check_status = SysConst::VOBO_REVISADO;
            $oVobo->is_deleted = 0;
            $oVobo->updated_by = \Auth::user()->id;
            $oVobo->update();

            $childAreaId = ordersVobosUtils::getDpsChildArea($oDps->type_doc_id, $voboArea->id_area);
            if($childAreaId != 0 && $is_accept == true){
                $oDpsChild = VoboDps::where('dps_id', $id_dps)->where('area_id', $childAreaId)->first();
                $oDpsChild->check_status = SysConst::VOBO_REVISION;
                $oDpsChild->update();
            }else{
                $oDps->status_id = $status_id;
                $oDps->update();
                $mailStatus = $statusKey;
                $sendMail = true;
            }

            if($is_reject){
                $oDpsComplementary = DpsComplementary::where('dps_id', $id_dps)->where('is_deleted', 0)->first();
                $oDpsComplementary->requester_comment_n = $comments;
                $oDpsComplementary->update();

                $rejection_id = $request->rejection_id;
                if(!is_null($rejection_id) && $rejection_id != "null"){
                    $oDpsReasonRejection = DpsReasonRejection::find($rejection_id);
                    $oDpsReasonRejection->count_usage = $oDpsReasonRejection->count_usage + 1;
                    $oDpsReasonRejection->update();
                }
            }

            $lDpsComp = DpsComplementsUtils::getlDpsComplementsToVobo($year, $provider_id, 
                        [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->toArray());
            
            foreach ($lDpsComp as $dps) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($dps->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences);
                $dps->reference_string = $Sreference; 
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }

            DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        if($sendMail){
            try {
                $oProvider = SProvider::findOrFail($oDps->provider_id_n);
                Mail::to($oProvider->provider_email)->send(new voboDpsMail(
                                                        $oProvider->provider_short_name,
                                                        $oDps->type_doc_id,
                                                        "factura",
                                                        $oDps->folio_n,
                                                        $mailStatus,
                                                        $comments
                                                    )
                                                );
            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => true, 'lDpsComp' => $lDpsComp, 'mailSuccess' => false, 
                "message" => "Registro guardado con éxito, pero no se pudo enviar el email de notificación", "icon"=> "warning"]);
            }
        }

        return json_encode(['success' => true, 'lDpsComp' => $lDpsComp, 'mailSuccess' => true]);
    }

    public function changeAreaDps(Request $request){
        try {
            $area_id = $request->area_id;
            $dps_id = $request->dps_id;
            $provider_id = $request->provider_id;
            $type_id = $request->type_id;

            if(is_null($area_id)){
                return json_encode(['success' => false, 'message' => "Debe seleccionar un area de destino", 'icon' => 'info']);
            }

            $config = \App\Utils\Configuration::getConfigurations();
            $canSeeAll = $config->canSeeAll;
            $lOmisionAreaDps = collect($config->lOmisionAreaDps)->pluck('id');
            
            if(in_array(\Auth::user()->id,$canSeeAll)){
                $oArea = DB::table('areas')
                                ->where('is_deleted',0)
                                ->whereNotIn('id_area',$lOmisionAreaDps)
                                ->get(); 
                $oArea = $oArea->pluck('id_area');   
            }else{
                $oArea = collect([\Auth::user()->getArea()]);
                $oArea = $oArea->pluck('id_area');
            }
            $year = Carbon::now()->format('Y');

            DB::beginTransaction();

            $oDps = Dps::find($dps_id);

            $arrStatusFac = SysConst::statusTypesDoc['FACTURA'];
            $arrStatusNC = SysConst::statusTypesDoc['NOTA_CREDITO'];

            if($oDps->status_id != $arrStatusFac['NUEVO'] && $oDps->status_id != $arrStatusNC['NUEVO']){
                return json_encode(['success' => false, 'message' => "Solo se puede reenviar documentos con estatus Nuevo", 'icon' => 'info']);
            }

            $oDps->area_id = $area_id;
            $oDps->update();
            
            VoboDps::where('dps_id', $dps_id)->where('is_deleted', 0)->update(['is_deleted' => 1]);

            $orders = ordersVobosUtils::getDpsOrder($type_id, $area_id);

            foreach($orders as $order){
                $oVoboDps = new VoboDps();
                $oVoboDps->dps_id = $oDps->id_dps;
                $oVoboDps->area_id = $order->area;
                $oVoboDps->is_accept = 0;
                $oVoboDps->is_reject = 0;
                $oVoboDps->order = $order->order;
                $oVoboDps->check_status = $order->order == 1 ? SysConst::VOBO_REVISION : SysConst::VOBO_NO_REVISION;
                $oVoboDps->is_deleted = 0;
                $oVoboDps->created_by = 1;
                $oVoboDps->updated_by = 1;
                $oVoboDps->save();
            }

            $lDpsComp = DpsComplementsUtils::getlDpsComplementsToVobo($year, $provider_id, 
                        [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->toArray());
            
            foreach ($lDpsComp as $dps) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($dps->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences);
                $dps->reference_string = $Sreference; 
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }

            
            DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsComp' => $lDpsComp]);
    }

    public function getDpsComplementOmision(Request $request){
        try {
            $omision = $request->omision;
            if($omision){
                $lDpsComp = DpsComplementsUtils::getlDpsOmisionArea([SysConst::DOC_TYPE_FACTURA]);
            }else{
                $config = \App\Utils\Configuration::getConfigurations();
                $canSeeAll = $config->canSeeAll;
                $lOmisionAreaDps = collect($config->lOmisionAreaDps)->pluck('id');
                
                if(in_array(\Auth::user()->id,$canSeeAll)){
                    $oArea = DB::table('areas')
                                    ->where('is_deleted',0)
                                    ->whereNotIn('id_area',$lOmisionAreaDps)
                                    ->get(); 
                    $oArea = $oArea->pluck('id_area');   
                }else{
                    $oArea = collect([\Auth::user()->getArea()]);
                    $oArea = $oArea->pluck('id_area');
                }
                $year = Carbon::now()->format('Y');
                $lDpsComp = DpsComplementsUtils::getlDpsComplementsToVobo($year, 0, 
                    [SysConst::DOC_TYPE_FACTURA], $oArea->toArray());
            }

            foreach ($lDpsComp as $dps) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($dps->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
                $dps->reference_string = $Sreference;
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsComp' => $lDpsComp]);
    }
}
