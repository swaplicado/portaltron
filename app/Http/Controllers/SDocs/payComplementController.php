<?php

namespace App\Http\Controllers\SDocs;

use App\Http\Controllers\Controller;
use App\Models\Areas\Areas;
use App\Models\SDocs\Dps;
use App\Models\SDocs\DpsComplementary;
use App\Models\SDocs\StatusDps;
use App\Models\SDocs\VoboDps;
use App\Utils\DpsComplementsUtils;
use App\Utils\FilesUtils;
use App\Utils\ordersVobosUtils;
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
            $oProvider = \Auth::user()->getProviderData();
            $type_id = SysConst::DOC_TYPE_COMPLEMENTO_PAGO;
            $year = $request->year;
            $area_id = $request->area_id != "null" ? $request->area_id : null;

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
            $oDps->pdf_url_n = $rutaPdf;
            $oDps->xml_url_n = $rutaXml;
            $oDps->status_id = $status_id;
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
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsPayComp' => $lDpsPayComp]);
    }
}
