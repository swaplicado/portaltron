<?php namespace App\Utils;

use App\Constants\SysConst;
use App\Models\SDocs\RequestTypeDocs;
use App\Models\SProviders\SProvider;

class SProvidersUtils {

    /**
     * Metodo para obtener un proveedor por el id de usuario
     */
    public static function getProviderByUser($user_id){
        $oProvider = SProvider::where('user_id', $user_id)->first();

        return $oProvider;
    }

    /**
     * Metodo para obtener un proveedor por el id de proveedor
     */
    public static function getProvider($provider_id){
        $oProvider = SProvider::join(config('myapp.mngr_db').'.users as u', 'u.id', '=', 'user_id')
                            ->join('status_providers as sp', 'sp.id_status_providers', '=', 'providers.status_provider_id')
                            ->where('id_provider', $provider_id)
                            ->select(
                                'id_provider',
                                'user_id',
                                'provider_short_name',
                                'provider_name',
                                'provider_rfc',
                                'provider_email',
                                'area_id',
                                'status_provider_id',
                                'u.username',
                                'sp.name as status',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->first();

        return $oProvider;
    }

    /**
     * Metodo que obtiene todos los proveedores
     */
    public static function getlProviders($area_id = []){
        $config = \App\Utils\Configuration::getConfigurations();

        $lProviders = SProvider::where('providers.is_active', 1)
                            ->where('providers.is_deleted', 0);

        if(!is_null($area_id)){
            if(in_array( $config->fatherArea, $area_id)){
                $lProviders = $lProviders->whereIn('providers.area_id', $area_id);
            }
        }

        $lProviders = $lProviders->join(config('myapp.mngr_db').'.users as u', 'u.id', '=', 'user_id')
                            ->join('status_providers as sp', 'sp.id_status_providers', '=', 'providers.status_provider_id')
                            ->select(
                                'id_provider',
                                'user_id',
                                'provider_short_name',
                                'provider_name',
                                'provider_rfc',
                                'provider_email',
                                'status_provider_id',
                                'u.username',
                                'sp.name as status',
                                'providers.external_id as ext_id',
                                'providers.area_id',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->get();

        return $lProviders;
    }

    /**
     * Metodo que valida los campos de un proveedor
     */
    public static function validateDataRegisterProvider($oData){
        $message = "";

        $name = $oData->name;
        $shortName = $oData->shortName;
        $rfc = $oData->rfc;
        $email = $oData->email;
        $password = $oData->password;
        $confirmPassword = $oData->confirmPassword;
        $area_id = $oData->area_id;

        if($name == null || $name == ''){
            $message = 'Debe introducir su razón social';
            return [false, $message];
        }

        if($shortName == null || $shortName == ''){
            $message = 'Debe introducir su nombre comercial';
            return [false, $message];
        }

        if($rfc == null || $rfc == ''){
            $message = 'Debe introducir su RFC';
            return [false, $message];
        }

        if(strlen($rfc) < 12){
            $message = 'Debe introducir un RFC valido';
            return [false, $message];
        }

        if($email == null || $email == ''){
            $message = 'Debe introducir su Email';
            return [false, $message];
        }

        if($password == null || $password == ''){
            $message = 'Debe introducir una contraseña de al menos 8 caracteres';
            return [false, $message];
        }

        if(strlen($password) < 8){
            $message = 'La contraseña debe contener al menos 8 caracteres';
            return [false, $message];
        }

        if($confirmPassword == null || $confirmPassword == ''){
            $message = 'Debe introducir la confirmación de la contraseña';
            return [false, $message];
        }

        if($password != $confirmPassword){
            $message = 'La contraseña y la confirmación de la contraseña deben ser iguales';
            return [false, $message];
        }

        $showAreaRegisterProvider = \App\Utils\Configuration::getConfigurations()->showAreaRegisterProvider;

        if($showAreaRegisterProvider){
            if($area_id == null){
                $message = 'Debe seleccionar un área';
                return [false, $message];
            }
        }

        $lDocs = RequestTypeDocs::where('is_default', 1)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_request_type_doc',
                                    'name'
                                )
                                ->get();

        foreach ($lDocs as $doc) {
            $docType = 'doc_'.$doc->id_request_type_doc;
            $pdf = $oData->file($docType);
            if(is_null($pdf)){
                $message = 'Faltó cargar '.$doc->name;
                return [false, $message];
            }
        }

        return [true, $message];
    }

    /**
     * Metodo que regresa los proveedores por area y por el estatus del vobo doc
     */
    public static function filterProviderToVobo(){
        $oArea = \Auth::user()->getArea();
        $config = \App\Utils\Configuration::getConfigurations();

        $lProvidersVobos = \DB::table('providers as p')
                            ->join(config('myapp.mngr_db').'.users as u', 'u.id', '=', 'user_id')
                            ->join('status_providers as sp', 'sp.id_status_providers', '=', 'p.status_provider_id')
                            ->join('prov_docs', 'prov_docs.prov_id', '=', 'p.id_provider')
                            ->join('docs_url', 'docs_url.prov_doc_id', '=', 'prov_docs.id_prov_doc')
                            ->join('vobo_docs as vd', 'vd.doc_url_id', '=', 'docs_url.id_doc_url')
                            ->where(function($query){
                                $query->where('vd.check_status', SysConst::VOBO_REVISION)
                                    ->orWhere('vd.check_status', SysConst::VOBO_REVISADO);

                            })
                            ->where('vd.is_deleted', 0)
                            ->where('vd.area_id', $oArea->id_area)
                            ->where('p.status_provider_id', SysConst::PROVIDER_PENDIENTE)
                            ->whereRaw('(docs_url.id_doc_url, prov_docs.id_prov_doc) IN (SELECT MAX(docs_url.id_doc_url), 
                            prov_docs.id_prov_doc FROM prov_docs INNER JOIN docs_url 
                            ON prov_docs.id_prov_doc = docs_url.prov_doc_id GROUP BY prov_docs.id_prov_doc)')
                            ->select(
                                'p.id_provider',
                                'p.user_id',
                                'p.provider_short_name',
                                'p.provider_name',
                                'p.provider_rfc',
                                'p.provider_email',
                                'p.status_provider_id',
                                'u.username',
                                'sp.name as status',
                                'p.external_id as ext_id',
                                'p.area_id',
                                \DB::raw('DATE_FORMAT(p.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(p.updated_at, "%Y-%m-%d") as updated')
                            )
                            ->groupBy([
                                'p.id_provider',
                                'p.user_id',
                                'p.provider_short_name',
                                'p.provider_name',
                                'p.provider_rfc',
                                'p.provider_email',
                                'p.status_provider_id',
                                'u.username',
                                'status',
                                'ext_id',
                                'p.area_id',
                                'created',
                                'updated'
                            ])
                            ->get();

        return $lProvidersVobos;
    }

    /**
     * Metodo que obtiene los documentos de un proveedor,
     * recibe el id del proveedor, el area del proveedor y el array de los estatus de los vobos de los documentos,
     * por defecto obtiene los docs en revision y revisados.
     * 
     * con whereRaw obtenemos solo los renglones mas nuevos de la tabla docs_url
     */
    public static function getDocumentsProvider($provider_id, $area_id, $lCheckstatus = [SysConst::VOBO_REVISION, SysConst::VOBO_REVISADO]){
        $lDocuments = \DB::table('vobo_docs as vd')
                        ->join('docs_url', 'docs_url.id_doc_url', '=', 'vd.doc_url_id')
                        ->join('prov_docs', 'prov_docs.id_prov_doc', '=', 'docs_url.prov_doc_id')
                        ->join('request_type_docs as rtd', 'rtd.id_request_type_doc', '=', 'prov_docs.request_type_doc_id')
                        ->where('prov_docs.prov_id', $provider_id)
                        ->where('vd.area_id', $area_id)
                        ->where('vd.is_deleted', 0)
                        ->whereIn('vd.check_status', $lCheckstatus)
                        ->whereRaw('(docs_url.id_doc_url, prov_docs.id_prov_doc) IN (SELECT MAX(docs_url.id_doc_url), 
                        prov_docs.id_prov_doc FROM prov_docs INNER JOIN docs_url 
                        ON prov_docs.id_prov_doc = docs_url.prov_doc_id GROUP BY prov_docs.id_prov_doc)')
                        ->select(
                            'vd.id_vobo',
                            'vd.is_accept',
                            'vd.is_reject',
                            'vd.check_status',
                            'rtd.name',
                            'docs_url.url',
                            'rtd.id_request_type_doc'
                        )
                        ->get();

        return $lDocuments;
    }

    public static function getDocumentsProviderByLastVobo($provider_id, $lCheckstatus = [SysConst::VOBO_REVISION, SysConst::VOBO_REVISADO]){
        $oProvider = SProvider::find($provider_id);
        $lOrders = ordersVobosUtils::getProviderDocsOrderToVobo($oProvider->area_id);
        // $lOrders = $lOrders->sortByDesc('order');

        $lDocs = [];
        foreach($lOrders as $order){
            $lDocs = SProvidersUtils::getDocumentsProvider($provider_id, $order->area, [SysConst::VOBO_REVISADO]);
            $rejectDocs = $lDocs->where('is_reject', 1);
            if(count($rejectDocs) > 0){
                break;
            }
        }
        
        return $lDocs;
    }

    /**
     * metodo que mezcla el resultado de los proveedores pendientes de vobo y el resto de proveedores
     */
    public static function getProvidersToVobo($oArea){
        $lAllProviders = SProvidersUtils::getlProviders();

        $lProvidersToVobo = SProvidersUtils::filterProviderToVobo();

        $config = \App\Utils\Configuration::getConfigurations();

        if($oArea->id_area != $config->fatherArea){
            $lProviders = $lAllProviders->where('area_id', $oArea->id_area)->where('status_provider_id', '!=', SysConst::PROVIDER_PENDIENTE);
        }else{
            $lProviders = $lAllProviders->where('status_provider_id', '!=', SysConst::PROVIDER_PENDIENTE);
        }
        
        $arr = $lProviders->toArray();

        $lProvidersToVobo = $lProvidersToVobo->concat($arr);

        return $lProvidersToVobo;
    }
}