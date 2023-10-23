<?php

namespace App\Http\Controllers\SDocs;

use App\Models\SProviders\SProvider;
use App\Utils\dateUtils;
use App\Utils\EstimateRequestUtils;
use App\Utils\PurchaseOrdersUtils;
use App\Models\SDocs\EstimateRequest;
use App\Utils\SProvidersUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Utils\AppLinkUtils;
class estimateRequestController extends Controller{
    public function index(){
        // \Auth::user()->authorizedPermission([['key' => 'proveedores.oc', 'level' => 'vista']]);

        // $oProvider = \Auth::user()->getProviderData();

        $Year = Carbon::now()->format('Y');
        $lStatus[0] = ['id' => 2, 'text' => 'Todos'];
        $lStatus[1] = ['id' => 0, 'text' => 'Abierto'];
        $lStatus[2] = ['id' => 1, 'text' => 'Sin abrir'];
        
        $res = json_decode($this->getEstimateRequest($Year));

        $lEstimateRequest = $res->lRows;

        foreach ($lEstimateRequest as $er) {
            $er->dateFormat = dateUtils::formatDate($er->date, 'd-m-Y');
        }

        $result = EstimateRequestUtils::insertEstimateRequest($lEstimateRequest);

        return view('estimateRequests.estimate_requests')->with('lEstimateRequest', $lEstimateRequest)
                                                    ->with('lStatus', $lStatus)
                                                    ->with('Year', $Year);
    } 
    
    public function getEstimateRequest($year, $providerId = null){
        try {
            if(\Auth::user()->type()->id_typesuser != SysConst::TYPE_ESTANDAR){
                $idProvider = json_encode($providerId);
            }else{
                $oProvider = \Auth::user()->getProviderData();
                $idProvider[0] = $oProvider->external_id;
            }

            $config = \App\Utils\Configuration::getConfigurations();
            $date = Carbon::now()->subMonthsNoOverflow($config->subMounthsDps)->toDateString();
            //$idProvider = 887;
            
            $body = '{
                "idBp": '.0.',
                "aBp": '.$idProvider.',
                "year": '.$year.',
                "date": '.$date.',
                "user": "'.\Auth::user()->username.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkGetEstimateRequest, "POST", \Auth::user(), $body);
            if(!is_null($result)){
                if($result->code != 200){
                    return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                }
            }else{
                return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
            }

            $data = json_decode($result->data);
            $lRows = $data->lERData;

            $result = EstimateRequestUtils::insertEstimateRequest($lRows);
            
            foreach($lRows as $row){
                $oEstimateRequest = \DB::table('est_req as er')
                                ->join('providers','providers.id_provider','=','er.provider_id')
                                ->where('er.external_id', $row->idEstimateRequest)
                                ->where('er.is_deleted', 0)
                                ->select(
                                    'er.id_est_req AS idInternal',
                                    'er.is_opened',
                                    'providers.provider_name AS name'
                                    'er.provider_comment_n',
                                    'er.created_at AS createdAt',
                                    'er.updated_at AS updatedAt'
                                )
                                ->first();

                if(!is_null($oEstimateRequest)){
                    $row->idInternal = $oEstimateRequest->idInternal;
                    $row->is_opened = $oEstimateRequest->is_opened;
                    $row->provider_comment_n = $oEstimateRequest->provider_comment_n;
                    if($oEstimateRequest->updatedAt == $oEstimateRequest->createdAt){
                        $row->updatedAt = "Sin abrir";
                    }else{
                        $date = Carbon::parse($oEstimateRequest->updatedAt);
                        $date->isoFormat('DD/MM/YYYY HH:mm');
                        $row->updatedAt = $date->isoFormat('DD/MM/YYYY HH:mm');    
                    }
                    if(count($providerId) != 1){
                        $row->prov_name = $oEstimateRequest->name;
                    }
                }else{
                    $row->idInternal = 0;
                    $row->is_opened = 0;
                    $row->provider_comment_n = '';
                    $row->updatedAt = 0;
                }
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lRows' => $lRows]);
    }

    public function getRows(Request $request){
        try {
            $idEstimateRequest = $request->idExt;

            $oEstimateRequest = $oEstimateRequest = \DB::table('est_req as er')
                                    ->where('er.external_id', $idEstimateRequest)
                                    ->where('er.is_deleted', 0)
                                    ->select(
                                        'er.id_est_req AS idInternal',
                                        'er.is_opened',
                                        'er.provider_comment_n',
                                        'er.external_id'
                                    )
                                ->first();

            if($request->isCompany == 0){
                $er = EstimateRequest::find($oEstimateRequest->idInternal);
                $er->is_opened = 1;
                $er->update();
            }

            $config = \App\Utils\Configuration::getConfigurations();
            $body = '{
                "idEstReq": '.$idEstimateRequest.',
                "user": "'.\Auth::user()->username.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkGetEstimateRequestRows, "POST", \Auth::user(), $body);
            if(!is_null($result)){
                if($result->code != 200){
                    return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                }
            }else{
                return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
            }

            $data = json_decode($result->data);
            $lRows = $data->lEREData;

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lRows' => $lRows]);
    }

    public function estimateRequestManager(){
        $config = \App\Utils\Configuration::getConfigurations();

        $lProviders = [];
        
        $olProviders = SProvidersUtils::getlProviders();

        $AuxProviders = $olProviders;
        $AuxProviders = $lProviders->pluck('ext_id');

        $result = json_decode($this->getEstimateRequest($year, $AuxProviders));

        array_push($lProviders, ['id' => 0, 'text' => 'Todos']);

        foreach ($olProviders as $value) {
            array_push($lProviders, ['id' => $value->id_provider, 'text' => $value->provider_name]);
        }

        $Year = Carbon::now()->format('Y');
        $lStatus[0] = ['id' => 2, 'text' => 'Todos'];
        $lStatus[1] = ['id' => 0, 'text' => 'Abierto'];
        $lStatus[2] = ['id' => 1, 'text' => 'Sin abrir'];
        


        return view('estimateRequests.estimate_request_manager')->with('lEstimateRequest', $result)
                                                    ->with('lStatus', $lStatus)
                                                    ->with('lProviders',$lProviders)
                                                    ->with('Year', $Year);
    } 

    public function getEstimateRequestByProvider(Request $request){
        try {
            $providerId = $request->providerId;
            $year = $request->year;

            if($providerId == 0){
                $olProviders = SProvidersUtils::getlProviders();

                $AuxProviders = $olProviders;
                $AuxProviders = $lProviders->pluck('ext_id');

            }else{
                $oProvider = SProvider::findOrFail($providerId);
                $AuxProviders[0] = $oProvider->external_id;
            }

            $result = json_decode($this->getEstimateRequest($year, $AuxProviders));

            $lEstimateRequest = $result->lRows;

            foreach ($lEstimateRequest as $er) {
                $er->dateFormat = dateUtils::formatDate($er->date, 'd-m-Y');
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lEstimateRequest' => $lEstimateRequest]);
    }
   

}
?>