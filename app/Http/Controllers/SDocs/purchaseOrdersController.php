<?php

namespace App\Http\Controllers\SDocs;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Models\SDocs\Dps;
use App\Models\SDocs\PurchaseOrders;
use App\Models\SDocs\StatusDps;
use App\Utils\AppLinkUtils;
use App\Utils\dateUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class purchaseOrdersController extends Controller
{
    public function index(){
        // \Auth::user()->authorizedPermission([['key' => 'proveedores.oc', 'level' => 'vista']]);

        // $oProvider = \Auth::user()->getProviderData();

        $lStatus = StatusDps::where('is_deleted', 0)
                            ->select(
                                'id_status_dps as id',
                                'name as text'
                            )
                            ->get()
                            ->toArray();

        array_unshift($lStatus, ['id' => 0, 'text' => 'Todos']);
        
        $res = json_decode($this->getPurchaseOrders());

        $lPurchaseOrders = $res->lRows;

        try {
            \DB::beginTransaction();
            foreach($lPurchaseOrders as $oc){
                $oDps = Dps::where('ext_id_year', $oc->idYear)->where('ext_id_doc', $oc->idDoc)->first();

                if(is_null($oDps)){
                    $oDps = new Dps();
                    $oDps->type_doc_id = SysConst::DOC_TYPE_PURCHASE_ORDER;
                    $oDps->ext_id_year = $oc->idYear;
                    $oDps->ext_id_doc = $oc->idDoc;
                    $oDps->status_id = SysConst::DOC_STATUS_NUEVO;
                    $oDps->is_deleted = 0;
                    $oDps->created_by = \Auth::user()->id;
                    $oDps->updated_by = \Auth::user()->id;
                    $oDps->save();
        
                    $oPurchaseOrder = new PurchaseOrders();
                    $oPurchaseOrder->dps_id = $oDps->id_dps;
                    $oPurchaseOrder->is_opened = 0;
                    $oPurchaseOrder->is_deleted = 0;
                    $oPurchaseOrder->created_by = \Auth::user()->id;
                    $oPurchaseOrder->updated_by = \Auth::user()->id;
                    $oPurchaseOrder->save();
                }
            }
            \DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th);
            \DB::rollBack();
        }

        return view('purchaseOrders.purchase_orders')->with('lPurchaseOrders', $lPurchaseOrders)->with('lStatus', $lStatus);
    }

    public function getPurchaseOrders(){
        try {
            $idProvider = 887;
            $year = 2023;

            $config = \App\Utils\Configuration::getConfigurations();
            $body = '{
                "idBp": '.$idProvider.',
                "year": '.$year.',
                "user": "'.\Auth::user()->username.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkGetPurchaseOrders, "POST", \Auth::user(), $body);
            if(!is_null($result)){
                if($result->code != 200){
                    return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                }
            }else{
                return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
            }

            $data = json_decode($result->data);
            $lRows = $data->lPOData;

            foreach($lRows as $row){
                $oPurchaseOrder = \DB::table('dps as d')
                                ->join('purchase_orders as oc', 'oc.dps_id', '=', 'id_dps')
                                ->join('status_dps as st', 'st.id_status_dps', '=', 'd.status_id')
                                ->where('d.ext_id_doc', $row->idDoc)
                                ->where('d.ext_id_year', $row->idYear)
                                ->where('d.is_deleted', 0)
                                ->select(
                                    'oc.provider_date_n',
                                    'st.name as status',
                                    'st.id_status_dps as id_status'
                                )
                                ->first();

                $row->dateStartCred = !is_null($row->dateStartCred) ? dateUtils::formatDate($row->dateStartCred, 'd-m-Y') : null;
                $row->delivery_date = !is_null($oPurchaseOrder->provider_date_n) ? dateUtils::formatDate($oPurchaseOrder->provider_date_n, 'd-m-Y') : null;
                $row->id_status = $oPurchaseOrder->id_status;
                $row->status = $oPurchaseOrder->status;
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lRows' => $lRows]);
    }

    public function getRows(Request $request){
        try {
            $idDoc = $request->idDoc;
            $idYear = $request->idYear;

            $oPurchaseOrder = \DB::table('dps as d')
                                ->join('purchase_orders as oc', 'oc.dps_id', '=', 'id_dps')
                                ->where('d.ext_id_doc', $idDoc)
                                ->where('d.ext_id_year', $idYear)
                                ->where('d.is_deleted', 0)
                                ->select(
                                    'oc.provider_comment_n',
                                    'oc.provider_date_n',
                                    'oc.id_purchase_order',
                                )
                                ->first();

            $oc = PurchaseOrders::find($oPurchaseOrder->id_purchase_order);
            $oc->is_opened = 1;
            $oc->update();

            $config = \App\Utils\Configuration::getConfigurations();
            $body = '{
                "idDoc": '.$idDoc.',
                "idYear": '.$idYear.',
                "user": "'.\Auth::user()->username.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkGetPurchaseOrderRows, "POST", \Auth::user(), $body);
            if(!is_null($result)){
                if($result->code != 200){
                    return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                }
            }else{
                return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
            }

            $data = json_decode($result->data);
            $lRows = $data->lPOEData;

            $deliveryDate = !is_null($oPurchaseOrder->provider_date_n) ? dateUtils::formatDate($oPurchaseOrder->provider_date_n, 'd-m-Y') : null;
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lRows' => $lRows, 'deliveryDate' => $deliveryDate, 'providerComment' => $oPurchaseOrder->provider_comment_n,]);
    }

    public function updatePurchaseOrder(Request $request){
        try {
            $idDoc = $request->idDoc;
            $idYear = $request->idYear;
            $deliveryDate = $request->deliveryDate;
            $comments = $request->comments;

            if(is_null($deliveryDate)){
                return json_encode(['success' => false, 'message' => 'Debe introducir una fecha de entrega', 'icon' => 'warning']);
            }

            \DB::beginTransaction();

            $oDps = Dps::where('ext_id_year', $idYear)->where('ext_id_doc', $idDoc)->first();

            $oPurchaseOrder = PurchaseOrders::where('dps_id', $oDps->id_dps)->first();
            $oPurchaseOrder->provider_date_n = dateUtils::formatDate($deliveryDate, 'Y-m-d');
            $oPurchaseOrder->provider_comment_n = $comments;
            $oPurchaseOrder->update();

            $oDps->status_id = SysConst::DOC_STATUS_ATENDIDO;
            $oDps->update();

            $deliveryDate = dateUtils::formatDate($oPurchaseOrder->provider_date_n, 'd-m-Y');
            $oStatus = StatusDps::find($oDps->status_id);
            $status = $oStatus->name;

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'deliveryDate' => $deliveryDate, 'comments' => $oPurchaseOrder->provider_comment_n, 'idStatus' => $oDps->status_id, 'status' =>  $status]);
    }
}