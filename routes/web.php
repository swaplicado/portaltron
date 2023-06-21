<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Providers\ProvidersController;
use App\Http\Controllers\Users\UsersController;

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

Route::middleware(['auth', 'menu'])->group( function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/registry', [HomeController::class, 'index'])->name('user_registry');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /** Proveedores */
    Route::get('/providers', [ProvidersController::class, 'index'])->name('providers_index');
    Route::post('/providers/create', [ProvidersController::class, 'createProvider'])->name('providers_create');
    Route::post('/providers/update', [ProvidersController::class, 'updateProvider'])->name('providers_update');
    Route::post('/providers/delete', [ProvidersController::class, 'deleteProvider'])->name('providers_delete');
    
    /** Usuarios */
    Route::get('/users', [UsersController::class, 'index'])->name('users_index');
    Route::post('/users/create', [UsersController::class, 'createUser'])->name('users_create');
    Route::post('/users/update', [UsersController::class, 'updateUser'])->name('users_update');
    Route::post('/users/delete', [UsersController::class, 'deleteUser'])->name('users_delete');
});
