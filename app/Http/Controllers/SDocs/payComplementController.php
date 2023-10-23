<?php

namespace App\Http\Controllers\SDocs;

use App\Http\Controllers\Controller;
use App\Models\Areas\Areas;
use App\Models\SDocs\Dps;
use App\Models\SDocs\DpsComplementary;
use App\Models\SDocs\DpsReasonRejection;
use App\Models\SDocs\StatusDps;
use App\Models\SDocs\VoboDps;
use App\Models\SProviders\SProvider;
use App\Utils\dateUtils;
use App\Utils\DpsComplementsUtils;
use App\Utils\FilesUtils;
use App\Utils\ordersVobosUtils;
use App\Utils\SProvidersUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\SysConst;
use Illuminate\Support\Facades\Storage;

class payComplementController extends Controller
{
    public function payComplement(){
        try {
            $oProvider = \Auth::user()->getProviderData();
            $year = Carbon::now()->format('Y');

            $lDpsPayComp = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_COMPLEMENTO_PAGO]);

            foreach ($lDpsPayComp as $dps) {
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }

            $lStatus = StatusDps::where('type_doc_id', SysConst::DOC_TYPE_COMPLEMENTO_PAGO)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_status_dps as id',
                                    'name as text'
                                )
                                ->get()
                                ->toArray();

            array_unshift($lStatus, ['id' => 0, 'text' => 'Todos']);

            $lAreas = Areas::where('is_deleted', 0)
                            ->select(
                                'id_area as id',
                                'name_area as text'
                            )
                            ->get()
                            ->toArray();

            $default_area_id = $oProvider->area_id;
        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('payComplements.payComplements')->with('lDpsPayComp', $lDpsPayComp)
                                                        ->with('year', $year)
                                                        ->with('lStatus', $lStatus)
                                                        ->with('lAreas', $lAreas)
                                                        ->with('default_area_id', $default_area_id);
    }

    public function savePayComplement(Request $request){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $oProvider = \Auth::user()->getProviderData();
            $type_id = SysConst::DOC_TYPE_COMPLEMENTO_PAGO;
            $year = $request->year;
            $area_id = $request->area_id;
            $folio = $request->folio;
            $comments = $request->comments;

            if(is_null($area_id) || $area_id == "null"){
                return json_encode(['success' => false, 'message' => 'Debe ingresar un área destino', 'icon' => 'warning']);
            }

            if(is_null($comments) || $comments == "null"){
                return json_encode(['success' => false, 'message' => 'Debe ingresar la referencia de factura', 'icon' => 'warning']);
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

            \DB::beginTransaction();

            $filePdfName = 'COMP_PAGO_'.$oProvider->provider_rfc.'_'.time().'.'.$pdf->extension();
            $resPdf = Storage::disk('complemento_pago')->putFileAs('/', $pdf, $filePdfName);
            $rutaPdf = Storage::disk('complemento_pago')->url($filePdfName);

            $fileXmlName = 'COMP_PAGO_XML_'.$oProvider->provider_rfc.'_'.time().'.'.$xml->extension();
            $resXml = Storage::disk('complemento_pago')->putFileAs('/', $xml, $fileXmlName);
            $rutaXml = Storage::disk('complemento_pago')->url($fileXmlName);

            $arrTypes = SysConst::lTypesDoc;
            $key = array_search($type_id, $arrTypes);
            $arrStatus = SysConst::statusTypesDoc[$key];
            
            $statusKey = 'NUEVO';
            $status_id = $arrStatus[$statusKey];

            $oDps = new Dps();
            $oDps->type_doc_id = $type_id;
            $oDps->provider_id_n = $oProvider->id_provider;
            $oDps->area_id = $area_id;
            $oDps->folio_n = $folio;
            $oDps->pdf_url_n = $rutaPdf;
            $oDps->xml_url_n = $rutaXml;
            $oDps->status_id = $status_id;
            $oDps->is_deleted = 0;
            $oDps->created_by = \Auth::user()->id;
            $oDps->updated_by = \Auth::user()->id;
            $oDps->save();

            $oDpsComp = new DpsComplementary();
            $oDpsComp->dps_id = $oDps->id_dps;
            $oDpsComp->provider_comment_n = $comments;
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

            $lDpsPayComp = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_COMPLEMENTO_PAGO]);

            foreach ($lDpsPayComp as $dps) {
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }

            \DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsPayComp' => $lDpsPayComp]);
    }

    public function getPayComplement(Request $request){
        try {
            $id_dps = $request->id_dps;

            $oDps = \DB::table('dps as d')
                    ->join('dps_complementary as dc', 'd.id_dps', '=', 'dc.dps_id')
                    ->where('d.id_dps', $id_dps)
                    ->where('dc.is_deleted', 0)
                    ->select(
                        'd.*',
                        'dc.requester_comment_n',
                    )
                    ->first();

        } catch (\Throwable $th) {
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'oDps' => $oDps]);
    }

    public function getlPayCompByYear(Request $request){
        try {
            $year = $request->year;
            $oProvider = \Auth::user()->getProviderData();
            $lDpsPayComp = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_COMPLEMENTO_PAGO]);

            foreach ($lDpsPayComp as $dps) {
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsPayComp' => $lDpsPayComp]);
    }

    /**
     * Index para la vista de complementos de pago para usuarios tipo manager
     */
    public function payComplementsManager(){
        try {
            $oArea = \Auth::user()->getArea();
            $olProviders = SProvidersUtils::getlProviders($oArea->id_area);

            $lProviders = [];
            array_push($lProviders, ['id' => 0, 'text' => "Todos"]);
            foreach ($olProviders as $value) {
                array_push($lProviders, ['id' => $value->id_provider, 'text' => $value->provider_name]);
            }

            $year = Carbon::now()->format('Y');

            $lStatus = StatusDps::where('type_doc_id', SysConst::DOC_TYPE_COMPLEMENTO_PAGO)
                            ->where('is_deleted', 0)
                            ->select(
                                'id_status_dps as id',
                                'name as text'
                            )
                            ->get()
                            ->toArray();

            array_unshift($lStatus, ['id' => 0, 'text' => 'Todos']);

            $arrStatusFac = SysConst::statusTypesDoc['COMPLEMENTO_PAGO'];

            $lConstants = [
                'COMPLEMENTO_PAGO' => SysConst::DOC_TYPE_COMPLEMENTO_PAGO,
                'CP_STATUS_NUEVO' => $arrStatusFac['NUEVO'],
                'CP_STATUS_PENDIENTE' => $arrStatusFac['PENDIENTE'],
            ];

            $lDpsPayComp = DpsComplementsUtils::getlDpsComplementsToVobo($year, 0, 
                                                [SysConst::DOC_TYPE_COMPLEMENTO_PAGO], $oArea->id_area);

        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('payComplements.payComplements_manager')->with('lProviders', $lProviders)
                                                                ->with('year', $year)
                                                                ->with('lStatus', $lStatus)
                                                                ->with('lConstants', $lConstants)
                                                                ->with('lDpsPayComp', $lDpsPayComp);
    }

    /**
     * Obtiene los complementos de un proveedor
     */
    public function getPayComplementsProvider(Request $request){
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

            $lDpsPayComp = DpsComplementsUtils::getlDpsComplementsToVobo($year, $provider_id, 
                                                [SysConst::DOC_TYPE_COMPLEMENTO_PAGO], $oArea->id_area);

            foreach ($lDpsPayComp as $dps) {
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsPayComp' => $lDpsPayComp]);
    }

    public function getPayComplementManager(Request $request){
        try {
            $id_dps = $request->id_dps;
            $oArea = \Auth::user()->getArea();
            $oDps = \DB::table('dps as d')
                    ->join('dps_complementary as dc', 'dc.dps_id', '=', 'd.id_dps')
                    ->join('vobo_dps as v', 'v.dps_id', '=', 'd.id_dps')
                    ->where('d.id_dps', $id_dps)
                    ->where('v.area_id', $oArea->id_area)
                    ->where('d.is_deleted', 0)
                    ->select(
                        'd.*',
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

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'oDps' => $oDps, 'lDpsReasons' => $lDpsReasons]);
    }

    public function setVoboPayComplement(Request $request){
        try {
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

            $lDpsPayComp = DpsComplementsUtils::getlDpsComplementsToVobo($year, $provider_id, 
                                                [SysConst::DOC_TYPE_COMPLEMENTO_PAGO], $oArea->id_area);

            foreach ($lDpsPayComp as $dps) {
                $dps->dateFormat = dateUtils::formatDate($dps->created_at, 'd-m-Y');
            }

            \DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsPayComp' => $lDpsPayComp]);
    }
}
