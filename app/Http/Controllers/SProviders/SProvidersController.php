<?php

namespace App\Http\Controllers\SProviders;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Mail\newProviderMail;
use App\Mail\voboProviderMail;
use App\Mail\nextStepVoboProviderMail;
use App\Mail\notifyTesoreria;
use App\Mail\notifyAreasNewProviderAccepted;
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
use App\Utils\UserUtils;
use Illuminate\Http\Request;
use App\Models\SProviders\SProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use stdClass;
use ZipArchive;
use Response;   

class SProvidersController extends Controller
{
    public function index(){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $oArea = \Auth::user()->getArea();
            $lProviders = SProvidersUtils::getProvidersToVobo($oArea);

            foreach ($lProviders as $provider) {
                $provider->fiscal_regime_name = $provider->fiscal_regime_name ? $provider->fiscal_key . ' - ' . $provider->fiscal_regime_name : 'N/D';
            }

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

            
            $lAreas = Areas::whereIn('id_area', $config->areasToRegisterProvider)
                            ->where('is_active', 1)
                            ->where('is_deleted', 0)
                            ->get();
            
            $user_area = $oArea->id_area;
            $fatherArea = $config->fatherArea;
            $showAreaRegisterProvider = $config->showAreaRegisterProvider;
        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }
        return view('SProviders.sproviders')->with('lProviders', $lProviders)
                                            ->with('lConstants', $lConstants)
                                            ->with('lStatus', $lStatus)
                                            ->with('oArea', $oArea)
                                            ->with('user_area', $user_area)
                                            ->with('fatherArea', $fatherArea)
                                            ->with('lAreas', $lAreas)
                                            ->with('showAreaRegisterProvider', $showAreaRegisterProvider);
    }

    public function getProvider(Request $request){
        try {
            $oProvider = SProvidersUtils::getProvider($request->provider_id);
            $oProvider->fiscal_regime_name = $oProvider->fiscal_regime_name ? $oProvider->fiscal_key . ' - ' . $oProvider->fiscal_regime_name : 'N/D';
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

    public function registerProviderIndex($key){
        $rfc = base64_decode($key);
        $type_register = 1;
        $lDocs = RequestTypeDocs::where('is_default', 1)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_request_type_doc',
                                    'name',
                                    'tooltip'
                                )
                                ->get();

        $config = \App\Utils\Configuration::getConfigurations();
        $lAreas = Areas::whereIn('id_area', $config->areasToRegisterProvider)
                        ->where('is_active', 1)
                        ->where('is_deleted', 0)->get();

        $showAreaRegisterProvider = $config->showAreaRegisterProvider;

        $fiscalRegime = \DB::table('fiscal_regime')
                            ->select(
                                'id',
                                'key',
                                'name'
                            )
                            ->get();

        $lFiscalRegime = [];
        foreach ($fiscalRegime as $fiscal) {
            $lFiscalRegime[] = [ 'id' => $fiscal->id, 'text' => $fiscal->key . ' - '. $fiscal->name ];
        }

        return view('SProviders.guestRegister')->with('lDocs', $lDocs)
                                                ->with('lAreas', $lAreas)
                                                ->with('showAreaRegisterProvider', $showAreaRegisterProvider)
                                                ->with('type_register', $type_register)
                                                ->with('lFiscalRegime', $lFiscalRegime)
                                                ->with('rfc', $rfc);
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
            $fiscal_id = $request->fiscal_id;
            $type_register = $request->type_register;
            $config = \App\Utils\Configuration::getConfigurations();
            
            if(is_null($area_id)){
                if($config->requireAreaRegisterProvider){
                    return json_encode(['success' => false, 'message' => "Debes seleccionar un área de destino", 'icon' => 'info']);
                }else{
                    $area_id = $config->omisionAreaRegisterProvider != null ? $config->omisionAreaRegisterProvider : $config->defaultAreaProvider;
                    if(is_null($area_id)){
                        return json_encode(['success' => false, 'message' => "No se encontró un área de destino", 'icon' => 'info']);
                    }
                }
            }

            if(is_null($fiscal_id)){
                return json_encode(['success' => false, 'message' => "Debes seleccionar un régimen fiscal", 'icon' => 'info']);
            }

            $searchRfc = \DB::table('providers')
                            ->where('provider_rfc', $rfc)
                            ->where('is_deleted', 0)
                            ->first();

            if(!is_null($searchRfc)){
                return json_encode(['success' => false, 'message' => "El RFC ya se encuentra registrado, intenta iniciar sesión para continuar", 'icon' => 'info']);
            }

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
    
            // $password = \DB::select(\DB::raw("SELECT PASSWORD('$request->password') AS password_result"))[0]->password_result;
        } catch (\Throwable $th) {
            \Log::error($th);
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
                $oProvider->provider_fiscal_regime_id = $fiscal_id;
                $oProvider->provider_email = $email;
                $oProvider->user_id = $oUser->id;
                $oProvider->area_id = $area_id;
                $oProvider->status_provider_id = $type_register == 1 ? SysConst::PROVIDER_PENDIENTE : SysConst::PROVIDER_APROBADO;
                $oProvider->is_active = 1;
                $oProvider->is_deleted = 0;
                $oProvider->created_by = $oUser->id;
                $oProvider->updated_by = $oUser->id;
                $oProvider->save();

                if ($type_register == 1) {
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
                }

                \DB::connection('mysql')->commit();
            } catch (\Throwable $th) {
                \DB::connection('mysql')->rollBack();

                \DB::connection('mysqlmngr')->beginTransaction();
                    $oUser->delete();
                \DB::connection('mysqlmngr')->commit();
                throw $th;
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => 'No se pudo crear el proveedor, intentalo más tarde', 'icon' => 'error']);
        }

        try {
            if ($type_register == 1) {
                $order = collect($orders)->first();
                $oArea = Areas::findOrFail($order->area);
                $lUsers = UserUtils::getUsersByArea($oArea->id_area);
    
                foreach ($lUsers as $user) {
                    $email = $user->email;
                    Mail::to($email)->send(new newProviderMail(
                                                            $oProvider->provider_short_name,
                                                            $oProvider->provider_rfc,
                                                        )
                                                    );
                }
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => true, 'mailSuccess' => false, 
            "message" => "Registro guardado con éxito, pero no se pudo enviar el email de notificación", "icon"=> "info"]);
        }

        return json_encode(['success' => true]);
    }

    public function approveProvider(Request $request){
        try {
            $mailStatus = '';
            $sendMail = false;
            $sendMailNextStep = false;

            $config = \App\Utils\Configuration::getConfigurations();
            $id_provider = $request->id_provider;
            $provider_area = $request->provider_area != "null" ? $request->provider_area : $config->fatherArea;

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

                if ($oProvider->status_provider_id == SysConst::PROVIDER_PENDIENTE_MODIFICAR) {
                    $oProvider->status_provider_id = SysConst::PROVIDER_PENDIENTE;
                    $oProvider->save();
                }

                $sendMailNextStep = true;
            }else{
                $lDocuments = SProvidersUtils::getDocumentsProvider($id_provider, $oArea->id_area);
                foreach ($lDocuments as $doc) {
                    $oVoboDoc = VoboDoc::findOrFail($doc->id_vobo);
                    $oVoboDoc->check_status = SysConst::VOBO_REVISADO;
                    $oVoboDoc->update();
                }

                $result = AppLinkUtils::checkUserInAppLink($oUser);
                $oProvider->status_provider_id = SysConst::PROVIDER_APROBADO;
                $oProvider->area_id = $provider_area;
                if(!is_null($result)){
                    if($result->code == 200){
                        $oProvider->external_id = $result->id_bp;
                    }
                }
                $oProvider->save();

                $mailStatus = "APROBADO";
                $sendMail = true;
            }
            
            $lProviders = SProvidersUtils::getProvidersToVobo($oArea);
            
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        if ($sendMailNextStep) {
            try {
                $lUsers = UserUtils::getUsersByArea($child_area_id);
                foreach ($lUsers as $user) {
                    $email = $user->email;
                    Mail::to($email)->send(new nextStepVoboProviderMail(
                                                            $oProvider->provider_short_name,
                                                            $oProvider->provider_rfc,
                                                            $oArea->name_area
                                                        )
                                                    );
                }
            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => true, 'lProviders' => $lProviders, 'mailSuccess' => false, 
                "message" => "Registro guardado con éxito, pero no se pudo enviar el email de notificación", "icon"=> "info"]);
            }
        }

        if($sendMail){
            try {
                if ($oArea->id_area == $config->fatherArea) {
                    $lUsers = UserUtils::getUsersByArea($oProvider->area_id);
                    foreach ($lUsers as $user) {
                        $email = $user->email;
                        Mail::to($email)->send(new notifyAreasNewProviderAccepted(
                                $oProvider->provider_short_name,
                                $oProvider->provider_rfc,
                                true
                            )
                        );
                    }
                }

                Mail::to($oProvider->provider_email)->send(new voboProviderMail(
                                                        SysConst::MAIL_PROVEEDOR,
                                                        $oProvider->provider_short_name,
                                                        $mailStatus,
                                                        ""
                                                    )
                                                );
            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => true, 'lProviders' => $lProviders, 'mailSuccess' => false, 
                "message" => "Registro guardado con éxito, pero no se pudo enviar el email de notificación", "icon"=> "info"]);
            }

            try {
                SProvidersUtils::notifyProviderToTesoreria($oProvider);
            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => true, 'lProviders' => $lProviders, 'mailSuccess' => false,
                "message" => "Registro guardado con éxito, pero no se pudo enviar el email de notificación al área de tesoreria", "icon"=> "info"]);
            }
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function rejectProvider(Request $request){
        try {
            $mailStatus = '';
            $sendMail = false;

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

            $mailStatus = "RECHAZADO";
            $sendMail = true;

            $lProviders = SProvidersUtils::getProvidersToVobo($oArea);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        if($sendMail){
            try {
                if ($oArea->id_area == $config->fatherArea) {
                    $lUsers = UserUtils::getUsersByArea($oProvider->area_id);
                    foreach ($lUsers as $user) {
                        $email = $user->email;
                        Mail::to($email)->send(new notifyAreasNewProviderAccepted(
                                $oProvider->provider_short_name,
                                $oProvider->provider_rfc,
                                false
                            )
                        );
                    }
                }

                Mail::to($oProvider->provider_email)->send(new voboProviderMail(
                                                        SysConst::MAIL_PROVEEDOR,
                                                        $oProvider->provider_short_name,
                                                        $mailStatus,
                                                        ""
                                                    )
                                                );
            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => true, 'lProviders' => $lProviders, 'mailSuccess' => false, 
                "message" => "Registro guardado con éxito, pero no se pudo enviar el email de notificación", "icon"=> "info"]);
            }
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function requireModifyProvider(Request $request){
        try {
            $mailStatus = '';
            $sendMail = false;

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

            $mailStatus = "MODIFICAR";
            $sendMail = true;

            $lProviders = SProvidersUtils::getProvidersToVobo($oArea);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        if($sendMail){
            try {
                Mail::to($oProvider->provider_email)->send(new voboProviderMail(
                                                        SysConst::MAIL_PROVEEDOR,
                                                        $oProvider->provider_short_name,
                                                        $mailStatus,
                                                        $comments
                                                    )
                                                );
            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => true, 'lProviders' => $lProviders, 'mailSuccess' => false, 
                "message" => "Registro guardado con éxito, pero no se pudo enviar el email de notificación", "icon"=> "info"]);
            }
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    /**
     * Metodo que regresa la vista para que el proveedor modifique sus datos
     */
    public function tempModifyProvider(){
        $config = \App\Utils\Configuration::getConfigurations();
        $oProvider = \Auth::user()->getProviderData();

        $lAreas = Areas::whereIn('id_area', $config->areasToRegisterProvider)
                        ->where('is_active', 1)
                        ->where('is_deleted', 0)->get();

        // $lDocuments = SProvidersUtils::getDocumentsProvider($oProvider->id_provider, $oProvider->area_id);
        $lDocuments = SProvidersUtils::getDocumentsProviderByLastVobo($oProvider->id_provider);
        $lDocs = $lDocuments->where('is_reject', 1);

        // $lDocs = $lDocs->toArray();

        $showAreaRegisterProvider = $config->showAreaRegisterProvider;

        $fiscalRegime = \DB::table('fiscal_regime')
                            ->select(
                                'id',
                                'key',
                                'name'
                            )
                            ->get();

        $lFiscalRegime = [];
        foreach ($fiscalRegime as $fiscal) {
            $lFiscalRegime[] = [ 'id' => $fiscal->id, 'text' => $fiscal->key . ' - '. $fiscal->name ];
        }

        return view('SProviders.tempModifyProvider')->with('oProvider', $oProvider)
                                                    ->with('lAreas', $lAreas)
                                                    ->with('lDocs', $lDocs)
                                                    ->with('showAreaRegisterProvider', $showAreaRegisterProvider)
                                                    ->with('lFiscalRegime', $lFiscalRegime);
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
            $fiscal_id = $request->fiscal_id;
            $config = \App\Utils\Configuration::getConfigurations();
            $sOrders = json_encode($config->orders);
            $lOrders = collect(json_decode($sOrders));

            if(is_null($area_id)){
                if($config->requireAreaRegisterProvider){
                    return json_encode(['success' => false, 'message' => "Debes seleccionar un área de destino", 'icon' => 'info']);
                }else{
                    $area_id = $config->omisionAreaRegisterProvider != null ? $config->omisionAreaRegisterProvider : $config->defaultAreaProvider;
                    if(is_null($area_id)){
                        return json_encode(['success' => false, 'message' => "No se encontró un área de destino", 'icon' => 'info']);
                    }
                }
            }

            $oOrder = $lOrders->where('id', $area_id)->first();
            $orders = $oOrder->orders;

            $oProvider = \Auth::user()->getProviderData();

            // $lDocuments = SProvidersUtils::getDocumentsProvider($oProvider->id_provider, $oProvider->area_id);
            // $lDocuments = SProvidersUtils::getDocumentsProviderByLastVobo($oProvider->id_provider);
            // $lDocs = $lDocuments->where('is_reject', 1);

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

            if($fiscal_id == null || $fiscal_id == ''){
                $message = 'Debes seleccionar un régimen fiscal';
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'info']);
            }

            try {
                $oProvider = SProvider::findOrFail(\Auth::user()->getProviderData()->id_provider);
                \DB::connection('mysqlmngr')->beginTransaction();
                try {
                    $oUser = User::findOrFail($oProvider->user_id);
                    // $oUser->username = $rfc;
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
                    // $oProvider->provider_rfc = $rfc;
                    $oProvider->provider_email = $email;
                    $oProvider->user_id = $oUser->id;
                    $oProvider->area_id = $area_id;
                    $oProvider->provider_fiscal_regime_id = $fiscal_id;
                    // $oProvider->status_provider_id = SysConst::PROVIDER_PENDIENTE;
                    $oProvider->updated_by = \Auth::user()->id;
                    $oProvider->update();

                    $lDocs = RequestTypeDocs::where('is_default', 1)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_request_type_doc',
                                    'name'
                                )
                                ->get();

                    foreach($lDocs as $doc){
                        $docType = 'doc_'.$doc->id_request_type_doc;
                        $pdf = $request->file($docType);
                        if (is_null($pdf)) {
                            continue;
                        }
                        $result = FilesUtils::validateFile($pdf, 'pdf', '5 MB');
                        if(!$result[0]){
                            return json_encode(['success' => false, 'message' => $result[1], 'icon' => 'error']);
                        }
        
                        $fileName = $docType.'_'.$rfc.'_'.time().'.'.$pdf->extension();
        
                        $rutaArchivo = Storage::disk('documents')->putFileAs('/', $pdf, $fileName);

                        $oProvDoc = ProvDocs::where('prov_id', $oProvider->id_provider)
                                            ->where('request_type_doc_id', $doc->id_request_type_doc)
                                            ->where('is_deleted', 0)
                                            ->first();

                        if(is_null($oProvDoc)){
                            $oProvDoc = new ProvDocs();
                            $oProvDoc->request_type_doc_id = $doc->id_request_type_doc;
                            $oProvDoc->prov_id = $oProvider->id_provider;
                            $oProvDoc->is_deleted = 0;
                            $oProvDoc->created_by = 1;
                            $oProvDoc->updated_by = 1;
                            $oProvDoc->save();
                        }
    
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
                            $oVoboDoc->is_accept = 1;
                            $oVoboDoc->is_reject = 0;
                            $oVoboDoc->order = $order->order;
                            $oVoboDoc->check_status = SysConst::VOBO_REVISADO;
                            $oVoboDoc->is_deleted = 0;
                            $oVoboDoc->created_by = 1;
                            $oVoboDoc->updated_by = 1;
                            $oVoboDoc->save();
                        }

                        // se comentaron los pasos de autorizacion al actualizar documentos, si se requiere actualizar descomentar
                        // foreach($orders as $order){
                        //     $oVoboDoc = new VoboDoc();
                        //     $oVoboDoc->doc_url_id = $oDocsUrl->id_doc_url;
                        //     $oVoboDoc->area_id = $order->area;
                        //     $oVoboDoc->is_accept = 0;
                        //     $oVoboDoc->is_reject = 0;
                        //     $oVoboDoc->order = $order->order;
                        //     $oVoboDoc->check_status = $order->order == 1 ? SysConst::VOBO_REVISION : SysConst::VOBO_NO_REVISION;
                        //     $oVoboDoc->is_deleted = 0;
                        //     $oVoboDoc->created_by = 1;
                        //     $oVoboDoc->updated_by = 1;
                        //     $oVoboDoc->save();
                        // }

                        if ($docType == 'doc_4') {
                            $lUsers = UserUtils::getUsersByArea($config->tesoreriaArea);
                            foreach ($lUsers as $oUser) {
                                $email = $oUser->email;
                                Mail::to($email)->send(new notifyTesoreria(
                                                                                $oProvider->provider_short_name,
                                                                                $oProvider->provider_rfc,
                                                                                2
                                                                                )
                                                                            );
                            }
                        }
                    }
    
                    \DB::connection('mysql')->commit();
                } catch (\Throwable $th) {
                    \DB::connection('mysql')->rollBack();
                    throw $th;
                }
            } catch (\Throwable $th) {
                \Log::error($th);
                return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true]);
    }

    public function documentsProviders(){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $oArea = \Auth::user()->getArea();

            $isFatherArea = false;
            if($oArea->id_area != $config->fatherArea){
                $lProviders = SProvidersUtils::getlProviders([$oArea->id_area]);
            }else{
                $lProviders = SProvidersUtils::getlProviders();
                $isFatherArea = true;
            }
            $lProviders = $lProviders->where('status_provider_id', SysConst::PROVIDER_APROBADO)->values();

            $oArea = \Auth::user()->getArea();
            // $lProviders = DocumentsUtils::getNumberPendigDocs($lProviders, $oArea->id_area);
            // $lProviders = DocumentsUtils::havePendigDocs($lProviders, $oArea->id_area);

            $lDocs = RequestTypeDocs::where('is_default', 1)
                                    ->where('is_deleted', 0)
                                    ->select(
                                        'id_request_type_doc',
                                        'name'
                                    )
                                    ->get();

            foreach ($lProviders as $provider) {
                $providerArea = \DB::table('areas')
                                    ->where('id_area', $provider->area_id)
                                    ->first();

                if (!is_null($provider->fiscal_key)) {
                    $provider->fiscal_regime = $provider->fiscal_key . ' - ' . $provider->fiscal_regime_name;
                } else {
                    $provider->fiscal_regime = 'N/D';
                }

                $provider->area = $providerArea->name_area ?? 'N/D';
                $lDocsProvider = SProvidersUtils::getDocumentsProvider($provider->id_provider, $config->fatherArea, [SysConst::VOBO_REVISADO]);
                $provider->number_pen_doc = count($lDocsProvider) . ' de ' . count($lDocs);
            }

            $lConstants = [
                'PROVIDER_PENDIENTE' => SysConst::PROVIDER_PENDIENTE,
                'PROVIDER_APROBADO' => SysConst::PROVIDER_APROBADO,
                'PROVIDER_RECHAZADO' => SysConst::PROVIDER_RECHAZADO,
                'PROVIDER_PENDIENTE_MODIFICAR' => SysConst::PROVIDER_PENDIENTE_MODIFICAR,
                'VOBO_NO_REVISION' => SysConst::VOBO_NO_REVISION,
                'VOBO_REVISION' => SysConst::VOBO_REVISION,
                'VOBO_REVISADO' => SysConst::VOBO_REVISADO,
            ];

            $lAreas = Areas::whereIn('id_area', $config->areasToRegisterProvider)
                            ->where('is_active', 1)
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

        return view('SProviders.documents_providers')->with('lProviders', $lProviders)
                                                    ->with('lConstants', $lConstants)
                                                    ->with('area_id', $oArea->id_area)
                                                    ->with('isFatherArea', $isFatherArea)
                                                    ->with('lDocs', $lDocs)
                                                    ->with('lAreas', $lAreas);
    }

    public static function providerProfile(){
        $oProvider = \Auth::user()->getProviderData();

        $lDocsProvider = SProvidersUtils::getDocumentsProvider($oProvider->id_provider, $oProvider->area_id, [SysConst::VOBO_REVISADO]);
        // $lDocs = $lDocuments->where('is_reject', 1);
        $lDocs = RequestTypeDocs::where('is_default', 1)
                                ->where('is_deleted', 0)
                                ->select(
                                    'id_request_type_doc',
                                    'name'
                                )
                                ->get();

        foreach($lDocs as $doc){
            $oDoc = $lDocsProvider->where('id_request_type_doc', $doc->id_request_type_doc)->first();
            if($oDoc != null){
                $doc->url = $oDoc->url;
            }else{
                $doc->url = null;
            }
        }

        $config = \App\Utils\Configuration::getConfigurations();
        $lAreas = Areas::whereIn('id_area', $config->areasToRegisterProvider)
                        ->where('is_active', 1)
                        ->where('is_deleted', 0)->get();

        $fiscalRegime = \DB::table('fiscal_regime')
                            ->select(
                                'id',
                                'key',
                                'name'
                            )
                            ->get();

        $lFiscalRegime = [];
        foreach ($fiscalRegime as $fiscal) {
            $lFiscalRegime[] = [ 'id' => $fiscal->id, 'text' => $fiscal->key . ' - '. $fiscal->name ];
        }

        return view('SProviders.provider_profile')->with('oProvider', $oProvider)
                                                    ->with('lDocs', $lDocs)
                                                    ->with('lAreas', $lAreas)
                                                    ->with('lFiscalRegime', $lFiscalRegime);
    }

    public function allProvidersDocuments(){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $oArea = \Auth::user()->getArea();
            
            if($oArea->id_area != $config->fatherArea){
                $allProviders = SProvidersUtils::getlProviders([$oArea->id_area]);
            }else{
                $allProviders = SProvidersUtils::getlProviders();
            }

            // foreach($allProviders as $provider){
            //     $provider->lDocs = SProvidersUtils::getDocumentsProvider(
            //         $provider->id_provider,
            //         $oArea->id_area,
            //         [SysConst::VOBO_REVISION, SysConst::VOBO_REVISADO, SysConst::VOBO_NO_REVISION]
            //     );
            // }

            $lTypesDocs = RequestTypeDocs::where('is_deleted', 0)->get();

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => 'no se pudieron obtener los documentos']);
        }

        return json_encode(['success' => true, 'allProviders' => $allProviders, 'lTypesDocs' => $lTypesDocs]);
    }

    public function downloadProvidersDocuments(Request $request){
        try {
            $arrProviders = $request->lProviders;
            $arrTypesDocs = $request->lDocuments;
    
            if (empty($arrTypesDocs)) {
                throw new \Exception("Debe seleccionar al menos un tipo de documento", 1);
            }
            if (empty($arrProviders)) {
                throw new \Exception("Debe seleccionar al menos a un proveedor", 1);
            }
    
            $lProviders = SProvider::whereIn('id_provider', $arrProviders)->get();
            $lTypesDocs = RequestTypeDocs::whereIn('id_request_type_doc', $arrTypesDocs)->get();
    
            $MAX_ZIP_SIZE = 100 * 1024 * 1024; // 100MB máximo por ZIP interno
            $currentSize = 0;
            $currentIndex = 1;
            $timestamp = time();
            $individualZipPaths = [];
    
            // Crear carpeta temporal si no existe
            if (!Storage::exists('temp')) {
                Storage::makeDirectory('temp');
            }
    
            // Función para iniciar un nuevo ZIP
            $startNewZip = function() use (&$currentIndex, &$timestamp) {
                $fileName = "archivos_{$timestamp}_{$currentIndex}.zip";
                $filePath = storage_path("app/temp/$fileName");
                $zip = new ZipArchive;
                $zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                return [$zip, $fileName, $filePath];
            };
    
            list($zip, $zipFileName, $zipPath) = $startNewZip();
    
            $countFiles = 0;
            foreach ($lProviders as $provider) {
                $provider->lDocs = SProvidersUtils::getDocumentsProvider(
                    $provider->id_provider,
                    $provider->area_id,
                    [SysConst::VOBO_REVISADO, SysConst::VOBO_REVISION],
                    $arrTypesDocs
                );
    
                foreach ($provider->lDocs as $doc) {
                    $countFiles++;
                    $filename = basename(parse_url($doc->url, PHP_URL_PATH));
                    $absolutePath = storage_path("app/documents/{$filename}");
    
                    if (file_exists($absolutePath)) {
                        $fileSize = filesize($absolutePath);
    
                        if ($currentSize + $fileSize > $MAX_ZIP_SIZE) {
                            $zip->close();
                            $individualZipPaths[] = $zipPath;
    
                            $currentIndex++;
                            list($zip, $zipFileName, $zipPath) = $startNewZip();
                            $currentSize = 0;
                        }
    
                        $originalName = $lTypesDocs->where('id_request_type_doc', $doc->id_request_type_doc)->first()->name;
                        if($originalName == null){
                            $originalName = $filename;
                        } else {
                            $originalName = str_replace(' ', '_', $originalName) . '_' . $provider->provider_rfc . '.pdf';
                        }
    
                        $relativePath = $provider->provider_name . '/' . $originalName;
                        $zip->addFile($absolutePath, $relativePath);
                        $currentSize += $fileSize;
                    }
                }
            }

            if ($countFiles < 1) {
                \Log::error('No existen archivos para descargar' . json_encode($arrProviders) . ' - ' . json_encode($arrTypesDocs));
                return response()->json([
                    'message' => 'No existen archivos para descargar',
                    'error' => 'No existen archivos para descargar',
                ], 500);
            }
    
            // Cerrar el último zip si está abierto
            if ($zip->status == 0) {
                $zip->close();
                $individualZipPaths[] = $zipPath;
            }
    
            // Ahora empaquetar todos los ZIPs individuales en uno grande
            $finalZipName = "documentos_comprimidos_{$timestamp}.zip";
            $finalZipPath = storage_path("app/temp/$finalZipName");
    
            $finalZip = new ZipArchive;
            $finalZip->open($finalZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
            foreach ($individualZipPaths as $path) {
                $internalName = basename($path);
                $finalZip->addFile($path, $internalName);
            }
    
            $finalZip->close();
    
            // Opcional: Borrar los ZIPs individuales ya que ya están dentro del final
            foreach ($individualZipPaths as $path) {
                unlink($path);
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            if (isset($zip) && $zip->status == 0) {
                $zip->close();
            }
            if (isset($finalZip) && $finalZip->status == 0) {
                $finalZip->close();
            }
            if (isset($path) && file_exists($path)) {
                unlink($path);
            }
            if (isset($zipPath) && file_exists($zipPath)) {
                unlink($zipPath);
            }
            if (isset($finalZipPath) && file_exists($finalZipPath)) {
                unlink($finalZipPath);
            }
            \Log::error($th);
            return response()->json([
                'message' => $th->getMessage(),
                'error' => $th->getMessage(),
            ], 500);
        }

        return response()->download($finalZipPath)->deleteFileAfterSend(true);
    }

    public function getProviderToDocuments(Request $request){
        try {
            $oProvider = SProvidersUtils::getProvider($request->provider_id);
            $oProvider->fiscal_regime_name = $oProvider->fiscal_regime_name ? $oProvider->fiscal_key . ' - ' . $oProvider->fiscal_regime_name : 'N/D';
            $providerArea = \DB::table('areas')
                                ->select('name_area')
                                ->where('id_area', $oProvider->area_id)
                                ->first();
            $oProvider->area = $providerArea->name_area ?? 'N/D';
            $oArea = \Auth::user()->getArea();
            $config = \App\Utils\Configuration::getConfigurations();
            $lDocuments = SProvidersUtils::getDocumentsProvider($request->provider_id, $config->fatherArea);
            foreach ($lDocuments as $doc) {
                $doc->status = $doc->is_accept == true ? 'Aprobado' : ($doc->is_reject == true ? 'Rechazado' : 'Pendiente');
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => false]);
        }

        return json_encode(['success' => true, 'oProvider' => $oProvider, 'lDocuments' => $lDocuments]);
    }

    /**
     * Metodo para actualizar el area del proveedor
     */
    public static function updateAreaProvider(Request $request){
        try {
            if($request->id_area == null){
                return json_encode(['success' => false, 'message' => 'Debes seleccionar una nueva área destino']);
            }

            $oProvider = SProvider::findOrFail($request->id_provider);
            $oProvider->area_id = $request->id_area;
            $oProvider->update();

            $config = \App\Utils\Configuration::getConfigurations();
            $oArea = \Auth::user()->getArea();

            if($oArea->id_area != $config->fatherArea){
                $lProviders = SProvidersUtils::getlProviders([$oArea->id_area]);
            }else{
                $lProviders = SProvidersUtils::getlProviders();
            }
            $lProviders = $lProviders->where('status_provider_id', SysConst::PROVIDER_APROBADO)->values();

            $oArea = \Auth::user()->getArea();
            $lDocs = RequestTypeDocs::where('is_default', 1)
                                    ->where('is_deleted', 0)
                                    ->select(
                                        'id_request_type_doc',
                                        'name'
                                    )
                                    ->get();

            foreach ($lProviders as $provider) {
                $providerArea = \DB::table('areas')
                                    ->where('id_area', $provider->area_id)
                                    ->first();

                if (!is_null($provider->fiscal_key)) {
                    $provider->fiscal_regime = $provider->fiscal_key . ' - ' . $provider->fiscal_regime_name;
                } else {
                    $provider->fiscal_regime = 'N/D';
                }

                $provider->area = $providerArea->name_area;
                $lDocsProvider = SProvidersUtils::getDocumentsProvider($provider->id_provider, $config->fatherArea, [SysConst::VOBO_REVISADO]);
                $provider->number_pen_doc = count($lDocsProvider) . ' de ' . count($lDocs);
            }

            return json_encode(['success' => true, 'lProviders' => $lProviders]);
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => 'No se pudo actualizar el area del proveedor']);
        }
    }
}
