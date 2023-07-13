<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SProviders\SProvidersController;
use App\Http\Controllers\Quotations\QuotationsController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Middleware\PermissionsMiddleware;

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

Auth::routes();

Route::middleware(['auth', 'menu', 'app.sprovider'])->group( function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/registry', [HomeController::class, 'index'])->name('user_registry');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /** Proveedores */
    Route::group(['as' => 'sproviders.'], function () {
        Route::get('/sproviders/{id?}', [SProvidersController::class, 'index'])->name('index')->middleware('app.middleware:providers,view');
        Route::post('/sproviders/create', [SProvidersController::class, 'createProvider'])->name('create')->middleware('app.middleware:providers,create');
        Route::post('/sproviders/update', [SProvidersController::class, 'updateProvider'])->name('update')->middleware('app.middleware:providers,edit');
        Route::post('/sproviders/delete', [SProvidersController::class, 'deleteProvider'])->name('delete')->middleware('app.middleware:providers,delete');
        Route::post('/sproviders/getProvider', [SProvidersController::class, 'getProvider'])->name('getProvider')->middleware('app.middleware:providers,show');
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
});

Route::get('/unauthorized', function () {
    return view('layouts.unauthorized');
})->name('unauthorized');
