<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SProviders\SProvidersController;
use App\Http\Controllers\SDocs\purchaseOrdersController;
use App\Http\Controllers\Quotations\QuotationsController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\SDocs\estimateRequestController;
use App\Http\Middleware\PermissionsMiddleware;
use App\Http\Controllers\SAccountStates\accountStatesController;
use App\Http\Controllers\SDocs\voboDocsController;
use App\Http\Controllers\SDocs\dpsComplementaryController;
use App\Http\Controllers\SDocs\payComplementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('login'));
});

Route::middleware(('guest'))->group ( function (){
    Route::group(['prefix' => 'sprovider', 'as' => 'registerProvider.'], function(){
        Route::get('/registerProvider', [SProvidersController::class, 'registerProviderIndex'])->name('registerProvider');
        Route::post('/registerProvider/save', [SProvidersController::class, 'saveRegisterProvider'])->name('saveRegister');
        Route::get('/tempProvider/{name}', [SProvidersController::class, 'tempProviderIndex'])->name('tempProvider');
    });
});

Auth::routes();

Route::middleware(['auth'])->group( function() {
    Route::group(['as' => 'registerProvider.'], function(){
        Route::get('/tempModifyProvider', [SProvidersController::class, 'tempModifyProvider'] )->name('tempModifyProvider');
        Route::post('/updateTempProvider', [SProvidersController::class, 'updateTempProvider'] )->name('updateTempProvider');
    });
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'menu', 'app.sprovider'])->group( function () {
    Route::get('/registry', [HomeController::class, 'index'])->name('user_registry');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /** Proveedores */
    Route::group(['as' => 'sproviders.'], function () {
        Route::get('/sproviders/{id?}', [SProvidersController::class, 'index'])->name('index')->middleware('app.middleware:providers,view');
        Route::get('/documentsProviders', [SProvidersController::class, 'documentsProviders'])->name('documentsProv');
        Route::post('/sproviders/getProvider', [SProvidersController::class, 'getProvider'])->name('getProvider')->middleware('app.middleware:providers,show');
        Route::post('/sproviders/approve', [SProvidersController::class, 'approveProvider'])->name('approve');
        Route::post('/sproviders/reject', [SProvidersController::class, 'rejectProvider'])->name('reject');
        Route::post('/sproviders/requireModifyProvider', [SProvidersController::class, 'requireModifyProvider'])->name('requireModify');
        Route::get('/providerProfile', [SProvidersController::class, 'providerProfile'])->name('profile');
    });
    
    /** Usuarios */
    Route::get('/users', [UsersController::class, 'index'])->name('users_index');
    Route::post('/users/create', [UsersController::class, 'createUser'])->name('users_create');
    Route::post('/users/update', [UsersController::class, 'updateUser'])->name('users_update');
    Route::post('/users/delete', [UsersController::class, 'deleteUser'])->name('users_delete');

    /** cotizaciones */
    Route::group(['as' => 'quotations.', 'prefix' => 'quotations'], function () {
        Route::get('/quotations', [QuotationsController::class, 'index'])->name('index');
        Route::post('/uploadQuotation', [QuotationsController::class, 'uploadQuotation'])->name('uploadQuotation');
        Route::get('/showQuotation/{id?}', [QuotationsController::class, 'showQuotation'])->name('showQuotation');
        Route::post('/update', [QuotationsController::class, 'updateQuotation'])->name('updateQuotation');
        Route::post('/delete', [QuotationsController::class, 'deleteQuotation'])->name('delete');
    });

    /** OC */
    Route::group(['as' => 'purchaseOrders.', 'prefix' => 'purchaseOrders'], function () {
        Route::get('/purchaseOrders', [purchaseOrdersController::class, 'index'])->name('index');
        Route::post('/purchaseOrders/getRows', [purchaseOrdersController::class, 'getRows'])->name('getRows');
        Route::post('/purchaseOrders/update', [purchaseOrdersController::class, 'updatePurchaseOrder'])->name('update');
        Route::get('/getPurchaseOrders/{year?}', [purchaseOrdersController::class, 'getPurchaseOrders'])->name('getPurchaseOrders');

        Route::get('/purchaseOrdersManager', [purchaseOrdersController::class, 'purcharseOrdersManager'])->name('indexManager');
        Route::post('/purchaseOrdersManager/getPurchaseOrders', [purchaseOrdersController::class, 'getPurchaseOrdersByProvider'])->name('getPurchaseOrdersManager');
    });

    /** Estimate Request */
    Route::group(['as' => 'estimateRequest.'], function () {
        Route::get('/estimateRequest', [estimateRequestController::class, 'index'])->name('index');
        Route::post('/estimateRequest/getRows', [estimateRequestController::class, 'getRows'])->name('getRows');
        Route::get('/estimateRequest/{year?}', [estimateRequestController::class, 'getEstimateRequest'])->name('getEstimateRequest');

        Route::get('/estimateRequestManager', [estimateRequestController::class, 'estimateRequestManager'])->name('indexERManager');
        Route::post('/estimateRequestManager/getEstimateRequest', [estimateRequestController::class, 'getEstimateRequestByProvider'])->name('getEstimateRequestManager');
    });

    /** Account States */
    Route::group(['as' => 'accountStates.'], function () {
        Route::get('/accountStates', [accountStatesController::class, 'index'])->name('index');
        Route::post('/updateAccountState', [accountStatesController::class, 'updateAccountState'])->name('updateAccount');
        
        Route::get('/accountStatesManager', [accountStatesController::class, 'managerIndex'])->name('managerIndex');
        Route::post('/updateAccountStateManager', [accountStatesController::class, 'updateAccountStateManager'])->name('updateAccountManager');
    });

    /**
     * Vistos buenos documentos
     */
    Route::group(['as' => 'voboDocs.'], function() {
        Route::post('voboDoc', [voboDocsController::class, 'voboDocument'])->name('voboDoc');
        Route::post('voboDoc/update', [voboDocsController::class, 'updateVoboDocument'])->name('updateVoboDoc');
    });

    /**
     * Rutas de complementos de dps
     */
    Route::group(['as' => 'dpsComplementary.'], function() {
        Route::get('complementsManager', [dpsComplementaryController::class, 'complementsManager'])->name('complementsManager');
        Route::post('complementsManager/getComplementsProvider', [dpsComplementaryController::class, 'getComplementsProvider'])->name('getComplementsManager');
        Route::post('complementsManager/getDpsComplementManager', [dpsComplementaryController::class, 'getDpsComplementManager'])->name('getDpsComplementManager');
        Route::post('complementsManager/setVoboComplement', [dpsComplementaryController::class, 'setVoboComplement'])->name('setVoboComplement');

        Route::get('complements', [dpsComplementaryController::class, 'providerIndex'])->name('complements');
        Route::post('complements/save', [dpsComplementaryController::class, 'saveComplementary'])->name('SaveComplements');
        Route::post('complements/getDpsComplement', [dpsComplementaryController::class, 'getDpsComplement'])->name('GetComplements');
        Route::post('complements/getlDpsCompByYear', [dpsComplementaryController::class, 'getlDpsCompByYear'])->name('getCompByYear');
    });

    /**
     * Rutas de complementos de pago
     */
    Route::group(['as' => 'payComplement.'], function() {
        Route::get('payComplementManager', [payComplementController::class, 'payComplementsManager'])->name('payComplementsManager');
        Route::post('payComplementManager/getPayComplementsProvider', [payComplementController::class, 'getPayComplementsProvider'])->name('getPayComplementsProvider');
        Route::post('payComplementManager/getPayComplementManager', [payComplementController::class, 'getPayComplementManager'])->name('getPayComplementManager');
        Route::post('payComplementManager/setVoboPayComplement', [payComplementController::class, 'setVoboPayComplement'])->name('setVoboPayComplement');

        Route::get('payComplement', [payComplementController::class, 'payComplement'])->name('payComplement');
        Route::post('payComplement/savePayComplement', [payComplementController::class, 'savePayComplement'])->name('savePayComplement');
        Route::post('payComplement/getPayComplement', [payComplementController::class, 'getPayComplement'])->name('getPayComplement');
        Route::post('payComplement/getlPayCompByYear', [payComplementController::class, 'getlPayCompByYear'])->name('getlPayCompByYear');
    });
});

Route::get('/unauthorized', function () {
    return view('layouts.unauthorized');
})->name('unauthorized');
