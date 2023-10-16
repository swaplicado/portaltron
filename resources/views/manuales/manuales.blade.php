@extends('layouts.principal')

@section('headStyles')

@endsection

@section('headJs')

@endsection

@section('content')
  
<div class="card">
    <div class="card-header">
        <h3>Manuales</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="table_purchase_orders" width="100%" cellspacing="0">
                <thead>
                    <th></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{asset('manuales/Altadeproveedores.pdf')}}" target="_blank">
                                <h3>Alta de proveedores</h3>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{asset('manuales/Vistobuenodeproveedores.pdf')}}" target="_blank">
                                <h3>Visto bueno de proveedores</h3>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{asset('manuales/Consultadeestadosdecuenta.pdf')}}" target="_blank">
                                <h3>Consulta de estados de cuenta</h3>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{asset('manuales/Consultadeórdenesdecompra.pdf')}}" target="_blank">
                                <h3>Consulta de órdenes de compra</h3>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{asset('manuales/Solicituddecotización.pdf')}}" target="_blank">
                                <h3>Solicitud de cotización</h3>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{asset('manuales/Facturasynotasdecrédito.pdf')}}" target="_blank">
                                <h3>Facturas y notas de crédito</h3>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{asset('manuales/CFDIdepago.pdf')}}" target="_blank">
                                <h3>CFDI de pago</h3>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection