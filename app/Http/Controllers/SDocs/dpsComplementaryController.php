<?php

namespace App\Http\Controllers\SDocs;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Models\SDocs\Dps;
use App\Models\SDocs\DpsComplementary;
use App\Models\SDocs\StatusDps;
use App\Models\SDocs\TypeDoc;
use App\Models\SDocs\VoboDps;
use App\Utils\DpsComplementsUtils;
use App\Utils\FilesUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class dpsComplementaryController extends Controller
{
    public static function providerIndex(){
        try {
            $oProvider = \Auth::user()->getProviderData();
            $year = Carbon::now()->format('Y');

            $lDpsComp = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO]);

        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
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

        return view('dpsComplementary.dps_complementary')->with('lDpsComp', $lDpsComp)
                                                        ->with('year', $year)
                                                        ->with('lStatus', $lStatus)
                                                        ->with('lTypes', $lTypes);
    }

    public function saveComplementary(Request $request){
        try {
            $oProvider = \Auth::user()->getProviderData();
            $type_id = $request->type_id;
            $reference = $request->reference;
            $year = $request->year;

            $oReference = Dps::where('folio_n', $reference)
                            ->where('is_deleted', 0)    
                            ->first();

            if(is_null($oReference)){
                return json_encode(['success' => false, 'message' => 'No se encuentra el documento con la referencia '.$reference, 'icon' => 'warning']);
            }

            $config = \App\Utils\Configuration::getConfigurations();
            $sOrders =  json_encode($config->orders);
            $lOrders = collect(json_decode($sOrders));
            $oOrder = $lOrders->where('id', $oProvider->area_id)->first();
            $orders = $oOrder->orders;

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

            if($type_id == SysConst::DOC_TYPE_FACTURA){
                $filePdfName = 'FAC_'.$reference.'_'.$oProvider->provider_rfc.'_'.time().'.'.$pdf->extension();
                $resPdf = Storage::disk('facturas')->putFileAs('/', $pdf, $filePdfName);
                $rutaPdf = Storage::disk('facturas')->url($filePdfName);

                $fileXmlName = 'XML_'.$reference.'_'.$oProvider->provider_rfc.'_'.time().'.'.$xml->extension();
                $resXml = Storage::disk('facturas')->putFileAs('/', $xml, $fileXmlName);
                $rutaXml = Storage::disk('facturas')->url($fileXmlName);
            }else if($type_id == SysConst::DOC_TYPE_NOTA_CREDITO){
                $filePdfName = 'FAC_'.$reference.'_'.$oProvider->provider_rfc.'_'.time().'.'.$pdf->extension();
                $rutaPdf = Storage::disk('notas_credito')->putFileAs('/', $pdf, $filePdfName);
                $fileXmlName = 'XML_'.$reference.'_'.$oProvider->provider_rfc.'_'.time().'.'.$xml->extension();
                $rutaXml = Storage::disk('notas_credito')->putFileAs('/', $xml, $fileXmlName);
            }

            $oDps = new Dps();
            $oDps->type_doc_id = $type_id;
            $oDps->provider_id_n = $oProvider->id_provider;
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

            $lDpsComp = DpsComplementsUtils::getlDpsComplements($year, $oProvider->id_provider, [SysConst::DOC_TYPE_FACTURA, SysConst::DOC_TYPE_NOTA_CREDITO]);

            \DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsComp' => $lDpsComp]);
    }

    public function getDpsComplement(Request $request){
        try {
            $id_dps = $request->id_dps;

            $oDps = \DB::table('dps as d')
                    ->join('dps_complementary as dc', 'd.id_dps', '=', 'dc.dps_id')
                    ->join('dps as d2', 'd2.id_dps', '=', 'dc.reference_doc_n')
                    ->where('d.id_dps', $id_dps)
                    ->where('dc.is_deleted', 0)
                    ->select(
                        'd.*',
                        'd2.folio_n as reference'
                    )
                    ->first();

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
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDpsComp' => $lDpsComp]);
    }
}
