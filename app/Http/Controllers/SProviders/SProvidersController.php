<?php

namespace App\Http\Controllers\SProviders;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Models\Areas\Areas;
use App\Models\SDocs\DocsUrl;
use App\Models\SDocs\ProvDocs;
use App\Models\SDocs\RequestTypeDocs;
use App\Models\User;
use App\Models\UserApp;
use App\Models\UserRole;
use App\Models\UserType;
use App\Models\SDocs\VoboDoc;
use App\Utils\AppLinkUtils;
use App\Utils\DocumentsUtils;
use App\Utils\FilesUtils;
use App\Utils\ordersVobosUtils;
use App\Utils\SProvidersUtils;
use App\Utils\SysUtils;
use Illuminate\Http\Request;
use App\Models\SProviders\SProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class SProvidersController extends Controller
{
    public function index(){
        try {
            $oArea = \Auth::user()->getArea();
            $lProviders = SProvidersUtils::getProvidersToVobo($oArea);

            $lConstants = [
                'PROVIDER_PENDIENTE' => SysConst::PROVIDER_PENDIENTE,
                'PROVIDER_APROBADO' => SysConst::PROVIDER_APROBADO,
                'PROVIDER_RECHAZADO' => SysConst::PROVIDER_RECHAZADO,
                'PROVIDER_PENDIENTE_MODIFICAR' => SysConst::PROVIDER_PENDIENTE_MODIFICAR,
                'VOBO_NO_REVISION' => SysConst::VOBO_NO_REVISION,
                'VOBO_REVISION' => SysConst::VOBO_REVISION,
                'VOBO_REVISADO' => SysConst::VOBO_REVISADO,
            ];

            $lStatus = \DB::table('status_providers')
                        ->select(
                            'id_status_providers as id',
                            'name as text'
                        )
                        ->get();
            
        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('sproviders.sproviders')->with('lProviders', $lProviders)
                                            ->with('lConstants', $lConstants)
                                            ->with('lStatus', $lStatus)
                                            ->with('oArea', $oArea);
    }

    public function getProvider(Request $request){
        try {
            $oProvider = SProvidersUtils::getProvider($request->provider_id);
            $oArea = \Auth::user()->getArea();
            $lDocuments = SProvidersUtils::getDocumentsProvider($request->provider_id, $oArea->id_area);
            foreach ($lDocuments as $doc) {
                $doc->status = $doc->is_accept == true ? 'Aprobado' : ($doc->is_reject == true ? 'Rechazado' : 'Pendiente');
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => false]);
        }

        return json_encode(['success' => true, 'oProvider' => $oProvider, 'lDocuments' => $lDocuments]);
    }

    public function registerProviderIndex(){
        $lDocs = RequestTypeDocs::where('is_default', 1)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_request_type_doc',
                                    'name'
                                )
                                ->get();

        $lAreas = Areas::where('is_active', 1)->where('is_deleted', 0)->get();

        return view('SProviders.guestRegister')->with('lDocs', $lDocs)->with('lAreas', $lAreas);
    }

    public function tempProviderIndex($name){
        return view('SProviders.tempProvider')->with('name', $name);
    }

    /**
     * Metodo que registra un proveedor y queda pediente para si aprobacion
     */
    public function saveRegisterProvider(Request $request){
        try {
            $name = $request->name;
            $shortName = $request->shortName;
            $rfc = $request->rfc;
            $email = $request->email;
            $password = $request->password;
            $confirmPassword = $request->confirmPassword;
            $area_id = $request->area_id;
            $config = \App\Utils\Configuration::getConfigurations();
            $sOrders =  json_encode($config->orders);
            $lOrders = collect(json_decode($sOrders));

            $oOrder = $lOrders->where('id', $area_id)->first();
            $orders = $oOrder->orders;

            $lDocs = RequestTypeDocs::where('is_default', 1)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_request_type_doc',
                                    'name'
                                )
                                ->get();
            
            $result = SProvidersUtils::validateDataRegisterProvider($request);
    
            if(!$result[0]){
                return json_encode(['success' => false, 'message' => $result[1], 'icon' => 'info']);
            }
    
            $password = \DB::select(\DB::raw("SELECT PASSWORD('$request->password') AS password_result"))[0]->password_result;
        } catch (\Throwable $th) {
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        try {
            \DB::connection('mysqlmngr')->beginTransaction();
            try {
                $oUser = new User();
                $oUser->username = $rfc;
                $oUser->email = $email;
                $oUser->password = \Hash::make($password);
                $oUser->first_name = $rfc;
                $oUser->last_name = $rfc;
                $oUser->names = $rfc;
                $oUser->full_name = $rfc;
                $oUser->is_active = 1;
                $oUser->is_deleted = 0;
                $oUser->created_by = 1;
                $oUser->updated_by = 1;
                $oUser->save();

                $oTypeUser = new UserType();
                $oTypeUser->user_id = $oUser->id;
                $oTypeUser->app_id = env('APP_ID');
                $oTypeUser->typeuser_id = SysConst::TYPE_ESTANDAR;
                $oTypeUser->save();

                $oUserApp = new UserApp();
                $oUserApp->user_id = $oUser->id;
                $oUserApp->app_id = env('APP_ID');
                $oUserApp->save();

                $oUserRole = new UserRole();
                $oUserRole->app_n_id = env('APP_ID');
                $oUserRole->user_id = $oUser->id;
                $oUserRole->role_id = SysConst::ROL_PROVEEDOR;
                $oUserRole->save();

                \DB::connection('mysqlmngr')->commit();
            } catch (\Throwable $th) {
                \DB::connection('mysqlmngr')->rollBack();
                throw $th;
            }
    
            \DB::connection('mysql')->beginTransaction();
            try {
                $oProvider = new SProvider();
                $oProvider->provider_name = $name;
                $oProvider->provider_short_name = $shortName;
                $oProvider->provider_rfc = $rfc;
                $oProvider->provider_email = $email;
                $oProvider->user_id = $oUser->id;
                $oProvider->area_id = $area_id;
                $oProvider->status_provider_id = SysConst::PROVIDER_PENDIENTE;
                $oProvider->is_active = 1;
                $oProvider->is_deleted = 0;
                $oProvider->created_by = $oUser->id;
                $oProvider->updated_by = $oUser->id;
                $oProvider->save();

                foreach($lDocs as $doc){
                    $docType = 'doc_'.$doc->id_request_type_doc;
                    $pdf = $request->file($docType);
                    $result = FilesUtils::validateFile($pdf, 'pdf', '5 MB');
                    if(!$result[0]){
                        return json_encode(['success' => false, 'message' => $result[1], 'icon' => 'error']);
                    }
    
                    $fileName = $docType.'_'.$rfc.'_'.time().'.'.$pdf->extension();
    
                    $rutaArchivo = Storage::disk('documents')->putFileAs('/', $pdf, $fileName);
                    
                    $oProvDoc = new ProvDocs();
                    $oProvDoc->request_type_doc_id = $doc->id_request_type_doc;
                    $oProvDoc->prov_id = $oProvider->id_provider;
                    $oProvDoc->is_deleted = 0;
                    $oProvDoc->created_by = 1;
                    $oProvDoc->updated_by = 1;
                    $oProvDoc->save();

                    $docUrl = Storage::disk('documents')->url($fileName);

                    $oDocsUrl = new DocsUrl();
                    $oDocsUrl->prov_doc_id = $oProvDoc->id_prov_doc;
                    $oDocsUrl->url = $docUrl;
                    $oDocsUrl->date_ini_n = Carbon::now()->toDateString();
                    $oDocsUrl->is_deleted = 0;
                    $oDocsUrl->created_by = 1;
                    $oDocsUrl->updated_by = 1;
                    $oDocsUrl->save();

                    foreach($orders as $order){
                        $oVoboDoc = new VoboDoc();
                        $oVoboDoc->doc_url_id = $oDocsUrl->id_doc_url;
                        $oVoboDoc->area_id = $order->area;
                        $oVoboDoc->is_accept = 0;
                        $oVoboDoc->is_reject = 0;
                        $oVoboDoc->order = $order->order;
                        $oVoboDoc->check_status = $order->order == 1 ? SysConst::VOBO_REVISION : SysConst::VOBO_NO_REVISION;
                        $oVoboDoc->is_deleted = 0;
                        $oVoboDoc->created_by = 1;
                        $oVoboDoc->updated_by = 1;
                        $oVoboDoc->save();
                    }
                }

                \DB::connection('mysql')->commit();
            } catch (\Throwable $th) {
                \DB::connection('mysql')->rollBack();
                $oUser->delete();
                throw $th;
            }
        } catch (\Throwable $th) {
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true]);
    }

    public function approveProvider(Request $request){
        try {
            $id_provider = $request->id_provider;

            $oProvider = SProvider::findOrFail($id_provider);
            $oUser = User::findOrFail($oProvider->user_id);

            $config = \App\Utils\Configuration::getConfigurations();
            $oArea = \Auth::user()->getArea();
            \DB::beginTransaction();

            if($oArea->id_area != $config->fatherArea){
                $child_area_id = ordersVobosUtils::getProviderDocsChildArea($oProvider->area_id, $oArea->id_area);

                $lDocuments = SProvidersUtils::getDocumentsProvider($id_provider, $oArea->id_area);
                foreach ($lDocuments as $doc) {
                    $oVoboDoc = VoboDoc::findOrFail($doc->id_vobo);
                    $oVoboDoc->check_status = SysConst::VOBO_REVISADO;
                    $oVoboDoc->update();
                }

                $lChildDocuments = SProvidersUtils::getDocumentsProvider($id_provider, $child_area_id, [SysConst::VOBO_NO_REVISION]);
                foreach ($lChildDocuments as $doc) {
                    $oVoboDoc = VoboDoc::findOrFail($doc->id_vobo);
                    $oVoboDoc->check_status = SysConst::VOBO_REVISION;
                    $oVoboDoc->update();
                }
            }else{
                $lDocuments = SProvidersUtils::getDocumentsProvider($id_provider, $oArea->id_area);
                foreach ($lDocuments as $doc) {
                    $oVoboDoc = VoboDoc::findOrFail($doc->id_vobo);
                    $oVoboDoc->check_status = SysConst::VOBO_REVISADO;
                    $oVoboDoc->update();
                }

                $result = AppLinkUtils::checkUserInAppLink($oUser);
                $oProvider->status_provider_id = SysConst::PROVIDER_APROBADO;
                if(!is_null($result)){
                    if($result->code == 200){
                        $oProvider->external_id = $result->id_bp;
                    }
                }
                $oProvider->save();
            }
            
            $lProviders = SProvidersUtils::getProvidersToVobo($oArea);
            
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function rejectProvider(Request $request){
        try {
            $id_provider = $request->id_provider;

            $oProvider = SProvider::findOrFail($id_provider);
            $oUser = User::findOrFail($oProvider->user_id);
            $config = \App\Utils\Configuration::getConfigurations();
            $oArea = \Auth::user()->getArea();

            \DB::beginTransaction();
            $lDocuments = SProvidersUtils::getDocumentsProvider($id_provider, $oArea->id_area);
            foreach ($lDocuments as $doc) {
                $oVoboDoc = VoboDoc::findOrFail($doc->id_vobo);
                $oVoboDoc->check_status = SysConst::VOBO_REVISADO;
                $oVoboDoc->update();
            }

            $oProvider = SProvider::find($id_provider);
            $oProvider->status_provider_id = SysConst::PROVIDER_RECHAZADO;
            $oProvider->save();

            $lProviders = SProvidersUtils::getProvidersToVobo($oArea);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function requireModifyProvider(Request $request){
        try {
            $id_provider = $request->id_provider;
            $comments = $request->comments;

            $oProvider = SProvider::findOrFail($id_provider);
            $oUser = User::findOrFail($oProvider->user_id);
            $config = \App\Utils\Configuration::getConfigurations();
            $oArea = \Auth::user()->getArea();

            \DB::beginTransaction();
            $lDocuments = SProvidersUtils::getDocumentsProvider($id_provider, $oArea->id_area);
            foreach ($lDocuments as $doc) {
                $oVoboDoc = VoboDoc::findOrFail($doc->id_vobo);
                $oVoboDoc->check_status = SysConst::VOBO_REVISADO;
                $oVoboDoc->update();
            }

            $oProvider = SProvider::find($id_provider);
            $oProvider->status_provider_id = SysConst::PROVIDER_PENDIENTE_MODIFICAR;
            $oProvider->comments_n = $comments;
            $oProvider->save();

            $lProviders = SProvidersUtils::getProvidersToVobo($oArea);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    /**
     * Metodo que regresa la vista para que el proveedor modifique sus datos
     */
    public function tempModifyProvider(){
        $oProvider = \Auth::user()->getProviderData();

        $lAreas = Areas::where('is_active', 1)->where('is_deleted', 0)->get();

        // $lDocuments = SProvidersUtils::getDocumentsProvider($oProvider->id_provider, $oProvider->area_id);
        $lDocuments = SProvidersUtils::getDocumentsProviderByLastVobo($oProvider->id_provider);
        $lDocs = $lDocuments->where('is_reject', 1);
        // $lDocs = $lDocs->toArray();

        return view('sproviders.tempModifyProvider')->with('oProvider', $oProvider)
                                                    ->with('lAreas', $lAreas)
                                                    ->with('lDocs', $lDocs);
    }

    /**
     * Metodo para actualizar los datos del proveedor cuando este tiene status pendiente de modificar
     */
    public function updateTempProvider(Request $request){
        try {
            $name = $request->name;
            $shortName = $request->shortName;
            $rfc = $request->rfc;
            $email = $request->email;
            $area_id = $request->area_id;
            $config = \App\Utils\Configuration::getConfigurations();
            $sOrders =  json_encode($config->orders);
            $lOrders = collect(json_decode($sOrders));
            $oOrder = $lOrders->where('id', $area_id)->first();
            $orders = $oOrder->orders;

            $oProvider = \Auth::user()->getProviderData();

            // $lDocuments = SProvidersUtils::getDocumentsProvider($oProvider->id_provider, $oProvider->area_id);
            $lDocuments = SProvidersUtils::getDocumentsProviderByLastVobo($oProvider->id_provider);
            $lDocs = $lDocuments->where('is_reject', 1);

            if($name == null || $name == ''){
                $message = 'Debe introducir su razón social';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }
    
            if($shortName == null || $shortName == ''){
                $message = 'Debe introducir su nombre comercial';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }
    
            if($rfc == null || $rfc == ''){
                $message = 'Debe introducir su RFC';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }
    
            if(strlen($rfc) < 12){
                $message = 'Debe introducir un RFC valido';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }
    
            if($email == null || $email == ''){
                $message = 'Debe introducir su Email';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }

            try {
                $oProvider = SProvider::findOrFail(\Auth::user()->getProviderData()->id_provider);
                \DB::connection('mysqlmngr')->beginTransaction();
                try {
                    $oUser = User::findOrFail($oProvider->user_id);
                    $oUser->username = $rfc;
                    $oUser->email = $email;
                    $oUser->first_name = $rfc;
                    $oUser->last_name = $rfc;
                    $oUser->names = $rfc;
                    $oUser->full_name = $rfc;
                    $oUser->updated_by = \Auth::user()->id;
                    $oUser->update();
    
                    \DB::connection('mysqlmngr')->commit();
                } catch (\Throwable $th) {
                    \DB::connection('mysqlmngr')->rollBack();
                    throw $th;
                }
        
                \DB::connection('mysql')->beginTransaction();
                try {
                    $oProvider->provider_name = $name;
                    $oProvider->provider_short_name = $shortName;
                    $oProvider->provider_rfc = $rfc;
                    $oProvider->provider_email = $email;
                    $oProvider->user_id = $oUser->id;
                    $oProvider->status_provider_id = SysConst::PROVIDER_PENDIENTE;
                    $oProvider->updated_by = \Auth::user()->id;
                    $oProvider->update();

                    foreach($lDocs as $doc){
                        $docType = 'doc_'.$doc->id_request_type_doc;
                        $pdf = $request->file($docType);
                        $result = FilesUtils::validateFile($pdf, 'pdf', '5 MB');
                        if(!$result[0]){
                            return json_encode(['success' => false, 'message' => $result[1], 'icon' => 'error']);
                        }
        
                        $fileName = $docType.'_'.$rfc.'_'.time().'.'.$pdf->extension();
        
                        $rutaArchivo = Storage::disk('documents')->putFileAs('/', $pdf, $fileName);

                        $oProvDoc = ProvDocs::where('prov_id', $oProvider->id_provider)
                                            ->where('request_type_doc_id', $doc->id_request_type_doc)
                                            ->first();
    
                        $docUrl = Storage::disk('documents')->url($fileName);
    
                        $oDocsUrl = new DocsUrl();
                        $oDocsUrl->prov_doc_id = $oProvDoc->id_prov_doc;
                        $oDocsUrl->url = $docUrl;
                        $oDocsUrl->date_ini_n = Carbon::now()->toDateString();
                        $oDocsUrl->is_deleted = 0;
                        $oDocsUrl->created_by = 1;
                        $oDocsUrl->updated_by = 1;
                        $oDocsUrl->save();
    
                        foreach($orders as $order){
                            $oVoboDoc = new VoboDoc();
                            $oVoboDoc->doc_url_id = $oDocsUrl->id_doc_url;
                            $oVoboDoc->area_id = $order->area;
                            $oVoboDoc->is_accept = 0;
                            $oVoboDoc->is_reject = 0;
                            $oVoboDoc->order = $order->order;
                            $oVoboDoc->check_status = $order->order == 1 ? SysConst::VOBO_REVISION : SysConst::VOBO_NO_REVISION;
                            $oVoboDoc->is_deleted = 0;
                            $oVoboDoc->created_by = 1;
                            $oVoboDoc->updated_by = 1;
                            $oVoboDoc->save();
                        }
                    }
    
                    \DB::connection('mysql')->commit();
                } catch (\Throwable $th) {
                    \DB::connection('mysql')->rollBack();
                    throw $th;
                }
            } catch (\Throwable $th) {
                return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
            }

        } catch (\Throwable $th) {
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true]);
    }

    public function documentsProviders(){
        try {
            $lProviders = SProvidersUtils::getlProviders();
            $lProviders = $lProviders->where('status_provider_id', SysConst::PROVIDER_APROBADO)->values();

            $oArea = \Auth::user()->getArea();
            $lProviders = DocumentsUtils::getNumberPendigDocs($lProviders, $oArea->id_area);
            $lProviders = DocumentsUtils::havePendigDocs($lProviders, $oArea->id_area);

            $lConstants = [
                'PROVIDER_PENDIENTE' => SysConst::PROVIDER_PENDIENTE,
                'PROVIDER_APROBADO' => SysConst::PROVIDER_APROBADO,
                'PROVIDER_RECHAZADO' => SysConst::PROVIDER_RECHAZADO,
                'PROVIDER_PENDIENTE_MODIFICAR' => SysConst::PROVIDER_PENDIENTE_MODIFICAR,
                'VOBO_NO_REVISION' => SysConst::VOBO_NO_REVISION,
                'VOBO_REVISION' => SysConst::VOBO_REVISION,
                'VOBO_REVISADO' => SysConst::VOBO_REVISADO,
            ];
            
        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }

        return view('sproviders.documents_providers')->with('lProviders', $lProviders)
                                                    ->with('lConstants', $lConstants)
                                                    ->with('area_id', $oArea->id_area);
    }
}
