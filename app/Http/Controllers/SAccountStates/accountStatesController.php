<?php
namespace App\Http\Controllers\SAccountStates;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Utils\AppLinkUtils;
use App\DataContainers\selectCalendarRows;
use App\Utils\dateUtils;
use App\Utils\SProvidersUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class accountStatesController extends Controller
{
    public function  index(){
        $config = \App\Utils\Configuration::getConfigurations();
        
        //fecha que se le pasara como inicial
        $start_month = Carbon::now()->startOfMonth();
        //fecha que se le pasara como final
        $end_month = Carbon::now()->endOfMonth();

        //fecha para sacar el select de fechas
        $date_filter = Carbon::now();
        //
        $year = $date_filter->get('year');
        // id del proveedor
        //$oProvider = \Auth::user()->getProviderData();
        //$idProvider = $oProvider->external_id;
        $idProvider = 887;
        // meses que apareceran en el select para cambiar el estado de cuenta
        $sMonths = [];

        for( $i = 0 ; $config->MonthsAccountState > $i ; $i++ ){
            if( $i != 0){
                $date_filter = $date_filter->subMonthsNoOverflow(1);
            }

            $select = new selectCalendarRows();
            $select->index_calendar = $i;
            $select->name_calendar = $config->MonthNames[$date_filter->get('month')-1] . " - " . $date_filter->get('year') ;
            $select->number_month = $date_filter->get('month');
            $select->number_year = $date_filter->get('year');
            $select->date_ini = $date_filter->startOfMonth()->format('Y-m-d');
            $select->date_fin = $date_filter->endOfMonth()->format('Y-m-d');

            $sMonths[$i] = $select ;
        }
        //al salir tengo la información del select

        $res = json_decode($this->getAccountState($idProvider,$year,$start_month->format('Y-m-d'),$end_month->format('Y-m-d')));

        $AccountState = $res->lRows;

        foreach ($AccountState as $as){
            $as->dateFormat = dateUtils::formatDate($as->date, 'd-m-Y');
        }

        return view('accountStates.account_states')->with('lAccountState', $AccountState)->with('sMonth',$sMonths)->with('idProvider',$idProvider);

    }

    public function getAccountState($idBp,$idYear,$dateIni,$dateFin){
        try{
            $config = \App\Utils\Configuration::getConfigurations();
            $body = '{
                "idBp": '.$idBp.',
                "idYear": '.$idYear.',
                "dateIni": "'.$dateIni.'",
                "dateFin": "'.$dateFin.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkRouteAccountState, "POST", \Auth::user(), $body);
                if(!is_null($result)){
                    if($result->code != 200){
                        return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                    }
                }else{
                    return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
                }

                $data = json_decode($result->data);
                $lRows = $data->lASData;

            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
            }
    
            return json_encode(['success' => true, 'lRows' => $lRows]);
    }

    public function updateAccountState(Request $request){
        $Aaux = $request->sMonths[$request->filter_month_id];
        //$oProvider = \Auth::user()->getProviderData();
        //$idProvider = $oProvider->external_id;
        $idProvider = 887;
        $res = json_decode($this->getAccountState($idProvider,$Aaux["number_year"],$Aaux["date_ini"],$Aaux["date_fin"]));

        $AccountState = $res->lRows; 

        foreach ($AccountState as $as){
            $as->dateFormat = dateUtils::formatDate($as->date, 'd-m-Y');
        }
        
        return json_encode(['success' => true, 'lAccountState' => $AccountState]);
    }

    public function managerIndex(){
        $config = \App\Utils\Configuration::getConfigurations();
        
        //fecha que se le pasara como inicial
        $start_month = Carbon::now()->startOfMonth();
        //fecha que se le pasara como final
        $end_month = Carbon::now()->endOfMonth();

        //fecha para sacar el select de fechas
        $date_filter = Carbon::now();
        //
        $year = $date_filter->get('year');
        // id del proveedor
        /*
        $oProvider = \Auth::user()->getProviderData();
        $idProvider = $oProvider->external_id;
        */
        //$idProvider = 887;
        // meses que apareceran en el select para cambiar el estado de cuenta
        
        $sMonths = [];

        for( $i = 0 ; $config->MonthsAccountState > $i ; $i++ ){
            if( $i != 0){
                $date_filter = $date_filter->subMonthsNoOverflow(1);
            }

            $select = new selectCalendarRows();
            $select->index_calendar = $i;
            $select->name_calendar = $config->MonthNames[$date_filter->get('month')-1] . " - " . $date_filter->get('year') ;
            $select->number_month = $date_filter->get('month');
            $select->number_year = $date_filter->get('year');
            $select->date_ini = $date_filter->startOfMonth()->format('Y-m-d');
            $select->date_fin = $date_filter->endOfMonth()->format('Y-m-d');

            $sMonths[$i] = $select ;
        }
        //al salir tengo la información del select

        //$res = json_decode($this->getAccountState($idProvider,$year,$start_month->format('Y-m-d'),$end_month->format('Y-m-d')));

        //$AccountState = $res->lRows;
        $lProviders = SProvidersUtils::getlProviders();
        $withoutProvider = 0;

        return view('accountStates.account_states_manager')->with('lProviders', $lProviders)->with('sMonth',$sMonths)->with('withoutProvider',$withoutProvider);    
    }

    public function updateAccountStateManager(Request $request){
        $Aaux = $request->sMonths[$request->filter_month_id];
        $idProvider = $request->filter_provider_id;
        $res = json_decode($this->getAccountState($idProvider,$Aaux["number_year"],$Aaux["date_ini"],$Aaux["date_fin"]));

        $AccountState = $res->lRows; 
        foreach ($AccountState as $as){
            $as->dateFormat = dateUtils::formatDate($as->date, 'd-m-Y');
        }
        $withoutProvider = 1;
        return json_encode(['success' => true, 'lAccountState' => $AccountState, 'withoutProvider' => $withoutProvider]);
    }



}

?>