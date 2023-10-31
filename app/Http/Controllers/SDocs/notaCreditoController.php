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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class notaCreditoController extends Controller
{
    public function providerIndex(){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $oProvider = \Auth::user()->getProviderData();
            $year = Carbon::now()->format('Y');

            $lNotaCredito = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_NOTA_CREDITO]);

            foreach ($lNotaCredito as $nc) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($nc->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
                $nc->reference_string = $Sreference;
                $nc->dateFormat = dateUtils::formatDate($nc->created_at, 'd-m-Y');
            }

            $lStatus = StatusDps::where('type_doc_id', SysConst::DOC_TYPE_NOTA_CREDITO)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_status_dps as id',
                                    'name as text'
                                )
                                ->get()
                                ->toArray();
    
            array_unshift($lStatus, ['id' => 0, 'text' => 'Todos']);
    
            $lAreas = Areas::whereIn('id_area', $config->areasToDps)
                            ->where('is_deleted', 0)
                            ->select(
                                'id_area as id',
                                'name_area as text'
                            )
                            ->get()
                            ->toArray();
    
            $default_area_id = $oProvider->area_id;

            $config = \App\Utils\Configuration::getConfigurations();
            $showAreaDps = $config->showAreaDps;
        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('notaCredito.notaCredito')->with('lNotaCredito', $lNotaCredito)
                                            ->with('year', $year)
                                            ->with('lStatus', $lStatus)
                                            ->with('lAreas', $lAreas)
                                            ->with('default_area_id', $default_area_id)
                                            ->with('showAreaDps', $showAreaDps);
    }

    public function saveNotaCredito(Request $request){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $oProvider = \Auth::user()->getProviderData();
            $reference = $request->reference;
            $refSerie = null;
            $refFolio = null;

            $year = Carbon::now()->format('Y');
            $area_id = $request->area_id;
            $serie = null;
            $folio = null;
            $serieFolio = $request->serieFolio;

            if(is_null($serieFolio)){
                return json_encode(['success' => false, 'message' => "Debes ingresar una serie y folio", 'icon' => 'info']);
            }

            if(is_null($reference)){
                return json_encode(['success' => false, 'message' => "Debes ingresar la referencia", 'icon' => 'info']);
            }

            $arrFolio = explode("-", $serieFolio);
            $arrReference = explode("-", $reference);

            if(count($arrFolio) > 1){
                $serie = $arrFolio[0];
                $folio = $arrFolio[1];
            }else{
                $folio = $arrFolio[0];
            }

            if(count($arrReference) > 1){
                $refSerie = $arrReference[0];
                $refFolio = $arrReference[1];
            }else{
                $refFolio = $arrReference[0];
            }

            if(is_null($area_id)){
                if($config->requireAreaDps){
                    return json_encode(['success' => false, 'message' => "Debes seleccionar un área de destino", 'icon' => 'info']);
                }else{
                    $lOmisionAreaDps = collect($config->lOmisionAreaDps);
                    $omisionArea = $lOmisionAreaDps->where('type', SysConst::DOC_TYPE_NOTA_CREDITO)->first();
                    $area_id = $omisionArea->id != null ? $omisionArea->id : $config->defaultAreaDps;
                    if(is_null($area_id)){
                        return json_encode(['success' => false, 'message' => "No se encontró un área de destino", 'icon' => 'info']);
                    }
                }
            }

            $orders = ordersVobosUtils::getDpsOrder(SysConst::DOC_TYPE_NOTA_CREDITO, $area_id);

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

            \DB::beginTransaction();

            $filePdfName = 'NC_'.$reference.'_'.$oProvider->provider_rfc.'_'.time().'.'.$pdf->extension();
            $resPdf = Storage::disk('notas_credito')->putFileAs('/', $pdf, $filePdfName);
            $rutaPdf = Storage::disk('notas_credito')->url($filePdfName);

            $fileXmlName = 'XML_'.$reference.'_'.$oProvider->provider_rfc.'_'.time().'.'.$xml->extension();
            $resXml = Storage::disk('notas_credito')->putFileAs('/', $xml, $fileXmlName);
            $rutaXml = Storage::disk('notas_credito')->url($fileXmlName);

            $oDps = new Dps();
            $oDps->type_doc_id = SysConst::DOC_TYPE_NOTA_CREDITO;
            $oDps->provider_id_n = $oProvider->id_provider;
            $oDps->serie_n = $serie;
            $oDps->folio_n = $serieFolio;
            $oDps->num_ref_n = $folio;
            $oDps->area_id = $area_id;
            $oDps->pdf_url_n = $rutaPdf;
            $oDps->xml_url_n = $rutaXml;
            $oDps->status_id = SysConst::NOTA_CREDITO_STATUS_NUEVO;
            $oDps->is_deleted = 0;
            $oDps->created_by = \Auth::user()->id;
            $oDps->updated_by = \Auth::user()->id;
            $oDps->save();

            $oDpsComp = new DpsComplementary();
            $oDpsComp->dps_id = $oDps->id_dps;
            $oDpsComp->is_opened = 1;
            $oDpsComp->is_deleted = 0;
            $oDpsComp->created_by = \Auth::user()->id;
            $oDpsComp->updated_by = \Auth::user()->id;
            $oDpsComp->save();

            $oDpsRef = new DpsReference();
            $oDpsRef->dps_id = $oDps->id_dps;
            $oDpsRef->reference_serie_n = $refSerie;
            $oDpsRef->reference_num_ref_n = $refFolio;
            $oDpsRef->reference_folio_n = $reference;
            $oDpsRef->is_deleted = 0;
            $oDpsRef->save();

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

            $lNotaCredito = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_NOTA_CREDITO]);

            foreach ($lNotaCredito as $nc) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($nc->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
                $nc->reference_string = $Sreference;
                $nc->dateFormat = dateUtils::formatDate($nc->created_at, 'd-m-Y');
            }

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        try {
            $order = collect($orders)->first();
            $oArea = Areas::findOrFail($order->area);

            Mail::to($oArea->email_n)->send(new newDpsMail(
                                                $oProvider->provider_short_name,
                                                $oDps->type_doc_id,
                                                "nota de crédito",
                                                $oDps->folio_n,
                                                [1,2,3]
                                            )
                                        );
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => true, 'lNotaCredito' => $lNotaCredito, 'mailSuccess' => false, "message" => $th->getMessage(), "icon"=> "warning"]);
        }

        return json_encode(['success'=> true, 'lNotaCredito' => $lNotaCredito]);
    }

    public function getNotaCredito(Request $request){
        try {
            $id_dps = $request->id_dps;

            $oDps = \DB::table('dps as d')
                    ->join('dps_complementary as dc', 'd.id_dps', '=', 'dc.dps_id')
                    ->leftJoin('dps as d2', 'd2.id_dps', '=', 'dc.reference_doc_n')
                    ->where('d.id_dps', $id_dps)
                    ->where('dc.is_deleted', 0)
                    ->select(
                        'd.*',
                        'd2.folio_n as reference',
                        'dc.requester_comment_n',
                    )
                    ->first();

            $lDpsReferences = DpsComplementsUtils::getlDpsReferences($oDps->id_dps);
            $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
            $oDps->reference_string = $Sreference;

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'oDps' => $oDps]);
    }

    public function notaCreditoManager(){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $oArea = \Auth::user()->getArea();
            $olProviders = SProvidersUtils::getlProviders($oArea->id_area);

            $lProviders = [];
            array_push($lProviders, ['id' => 0, 'text' => "Todos"]);
            foreach ($olProviders as $value) {
                array_push($lProviders, ['id' => $value->id_provider, 'text' => $value->provider_name]);
            }

            $year = Carbon::now()->format('Y');

            $lStatus = StatusDps::where('type_doc_id', SysConst::DOC_TYPE_NOTA_CREDITO)
                            ->where('is_deleted', 0)
                            ->select(
                                'id_status_dps as id',
                                'name as text'
                            )
                            ->get()
                            ->toArray();

            array_unshift($lStatus, ['id' => 0, 'text' => 'Todos']);
            
            $arrStatusNC = SysConst::statusTypesDoc['NOTA_CREDITO'];

            $lConstants = [
                'NOTA_CREDITO' => SysConst::DOC_TYPE_NOTA_CREDITO,
                'NC_STATUS_NUEVO' => $arrStatusNC['NUEVO'],
                'NC_STATUS_PENDIENTE' => $arrStatusNC['PENDIENTE'],
            ];

            $lNotaCredito = DpsComplementsUtils::getlDpsComplementsToVobo($year, 0, 
                    [SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->id_area);
            foreach ($lNotaCredito as $nc) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($nc->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
                $nc->reference_string = $Sreference;
                $nc->dateFormat = dateUtils::formatDate($nc->created_at, 'd-m-Y');
            }

            $lAreas = Areas::whereIn('id_area', $config->areasToDps)
                            ->where('is_deleted', 0)
                            ->where('is_active', 1)
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

        return view('notaCredito.notaCredito_manager')->with('lProviders', $lProviders)
                                                    ->with('year', $year)
                                                    ->with('lStatus', $lStatus)
                                                    ->with('lConstants', $lConstants)
                                                    ->with('lNotaCredito', $lNotaCredito)
                                                    ->with('lAreas', $lAreas);
    }

    public function getNotaCreditoManager(Request $request){
        try {
            $id_dps = $request->id_dps;
            $oArea = \Auth::user()->getArea();
            $oDps = \DB::table('dps as d')
                    ->join('dps_complementary as dc', 'dc.dps_id', '=', 'd.id_dps')
                    ->leftJoin('dps as d2', 'd2.id_dps', '=', 'dc.reference_doc_n')
                    ->leftJoin('vobo_dps as v', 'v.dps_id', '=', 'd.id_dps')
                    ->where('d.id_dps', $id_dps)
                    // ->where('v.area_id', $oArea->id_area)
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

            $lDpsReferences = DpsComplementsUtils::getlDpsReferences($oDps->id_dps);
            $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
            $oDps->reference_string = $Sreference;

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'oDps' => $oDps, 'lDpsReasons' => $lDpsReasons]);
    }

    /**
     * Obtiene las NC de un proveedor
     */
    public function getNotasCreditoProvider(Request $request){
        try {
            $oArea = \Auth::user()->getArea();
            $provider_id = $request->provider_id;

            if($provider_id != 0){
                $oProvider = SProvider::findOrFail($provider_id);
                $provider_id = $oProvider->id_provider;
            }

            $year = $request->year;

            if(is_null($year)){
                $year = Carbon::now()->format('Y');
            }

            $lNotaCredito = DpsComplementsUtils::getlDpsComplementsToVobo($year, $provider_id, 
                                [SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->id_area);
            foreach ($lNotaCredito as $nc) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($nc->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
                $nc->reference_string = $Sreference;
                $nc->dateFormat = dateUtils::formatDate($nc->created_at, 'd-m-Y');
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lNotaCredito' => $lNotaCredito]);
    }

    public function setVoboNotaCredito(Request $request){
        try {
            $mailStatus = '';
            $sendMail = false;

            $id_dps = $request->id_dps;
            $is_accept = $request->is_accept;
            $is_reject = $request->is_reject;
            $provider_id = $request->provider_id;
            $year = $request->year;
            $comments = $request->comments;

            $oArea = \Auth::user()->getArea();

            \DB::beginTransaction();

            $oDps = Dps::findOrFail($id_dps);
            
            $arrTypes = SysConst::lTypesDoc;
            $key = array_search($oDps->type_doc_id, $arrTypes);
            $arrStatus = SysConst::statusTypesDoc[$key];
            
            $statusKey = $is_accept == true ? 'APROBADO' : 'RECHAZADO';
            $status_id = $arrStatus[$statusKey];

            $oVobo = VoboDps::where('dps_id', $id_dps)->where('area_id', $oArea->id_area)->first();
            $oVobo->user_id = \Auth::user()->id;
            $oVobo->is_accept = $is_accept;
            $oVobo->is_reject = $is_reject;
            $oVobo->date_accept_n = $is_accept == true ? Carbon::now()->toDateString() : null;
            $oVobo->date_rej_n = $is_reject == true ? Carbon::now()->toDateString() : null;
            $oVobo->check_status = SysConst::VOBO_REVISADO;
            $oVobo->is_deleted = 0;
            $oVobo->updated_by = \Auth::user()->id;
            $oVobo->update();

            $childAreaId = ordersVobosUtils::getDpsChildArea($oDps->type_doc_id, $oArea->id_area);
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

            $lNotaCredito = DpsComplementsUtils::getlDpsComplementsToVobo($year, $provider_id, 
                        [SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->id_area);
            
            foreach ($lNotaCredito as $nc) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($nc->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
                $nc->reference_string = $Sreference;
                $nc->dateFormat = dateUtils::formatDate($nc->created_at, 'd-m-Y');
            }

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        if($sendMail){
            try {
                $oProvider = SProvider::findOrFail($oDps->provider_id_n);
                Mail::to($oProvider->provider_email)->send(new voboDpsMail(
                                                        $oProvider->provider_short_name,
                                                        $oDps->type_doc_id,
                                                        "nota de credito",
                                                        $oDps->folio_n,
                                                        $mailStatus,
                                                        $comments
                                                    )
                                                );
            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => true, 'lNotaCredito' => $lNotaCredito, 'mailSuccess' => false, 
                "message" => "Registro guardado con éxito, pero no se pudo enviar el email de notificación", "icon"=> "info"]);
            }
        }

        return json_encode(['success' => true, 'lNotaCredito' => $lNotaCredito]);
    }

    public function changeAreaDps(Request $request){
        try {
            $area_id = $request->area_id;
            $dps_id = $request->dps_id;
            $provider_id = $request->provider_id;

            if(is_null($area_id)){
                return json_encode(['success' => false, 'message' => "Debe seleccionar un area de destino", 'icon' => 'info']);
            }

            $oArea = \Auth::user()->getArea();
            $year = Carbon::now()->format('Y');

            \DB::beginTransaction();

            $oDps = Dps::find($dps_id);

            $arrStatusNC = SysConst::statusTypesDoc['NOTA_CREDITO'];

            if($oDps->status_id != $arrStatusNC['NUEVO']){
                return json_encode(['success' => false, 'message' => "Solo se puede reenviar documentos con estatus Nuevo", 'icon' => 'info']);
            }

            $oDps->area_id = $area_id;
            $oDps->update();
            
            VoboDps::where('dps_id', $dps_id)->where('is_deleted', 0)->update(['is_deleted' => 1]);

            $orders = ordersVobosUtils::getDpsOrder(SysConst::DOC_TYPE_NOTA_CREDITO, $area_id);

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

            $lNotaCredito = DpsComplementsUtils::getlDpsComplementsToVobo($year, $provider_id, 
                                [SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->id_area);
            
            foreach ($lNotaCredito as $nc) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($nc->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
                $nc->reference_string = $Sreference;
                $nc->dateFormat = dateUtils::formatDate($nc->created_at, 'd-m-Y');
            }

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lNotaCredito' => $lNotaCredito]);
    }

    public function getNotasCreditoOmision(Request $request){
        try {
            $omision = $request->omision;
            if($omision){
                $lNotaCredito = DpsComplementsUtils::getlDpsOmisionArea([SysConst::DOC_TYPE_NOTA_CREDITO]);
            }else{
                $oArea = \Auth::user()->getArea();
                $year = Carbon::now()->format('Y');
                $lNotaCredito = DpsComplementsUtils::getlDpsComplementsToVobo($year, 0, 
                    [SysConst::DOC_TYPE_NOTA_CREDITO], $oArea->id_area);
            }

            foreach ($lNotaCredito as $nc) {
                $lDpsReferences = DpsComplementsUtils::getlDpsReferences($nc->id_dps);
                $Sreference = DpsComplementsUtils::transformToString($lDpsReferences, "reference_folio_n");
                $nc->reference_string = $Sreference;
                $nc->dateFormat = dateUtils::formatDate($nc->created_at, 'd-m-Y');
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lNotaCredito' => $lNotaCredito]);
    }
}
