<?php

namespace App\Http\Controllers\UserManuals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class userManualsController extends Controller
{
    public function index(){
        $is_provider = \Auth::user()->is_provider();

        return view('manuales.manuales')->with('is_provider', $is_provider);
    }

    public function getManual($id){
        $is_provider = \Auth::user()->is_provider();

        switch ($id) {
            case 'dataProvider':
                return view('manuales.manual_datos_proveedor');
                break;
            case 'ocProveedor':
                return view('manuales.manual_oc_proveedor');
                break;
            case 'facturasProveedor':
                return view('manuales.manual_facturas_proveedor');
                break;
            case 'creditoProveedor':
                return view('manuales.manual_notas_credito');
                break;
            case 'cfdiProveedor':
                return view('manuales.manual_cfdi_pagos_proveedor');
                break;
            default:
                # code...
                break;
        }
    }
}
