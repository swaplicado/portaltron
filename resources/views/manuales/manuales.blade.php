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
                    @if (!$is_provider)
                        <tr>
                            <td>
                                <a href="{{asset('manuales/interno/manual_alta_de_proveedores_(interno).pdf')}}" target="_blank">
                                    <h3>Alta de proveedores</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/interno/manual_visto_bueno_de_proveedores_(interno).pdf')}}" target="_blank">
                                    <h3>Visto bueno de proveedores</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/interno/manual_solicitud_de_cotizacion_(interno).pdf')}}" target="_blank">
                                    <h3>Solicitud de cotización</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/interno/manual_ordenes_de_compra_(interno).pdf')}}" target="_blank">
                                    <h3>Órdenes de compra</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/interno/manual_facturas_y_notas_de_credito_(interno).pdf')}}" target="_blank">
                                    <h3>Facturas y notas de crédito</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/interno/manual_cfdi_de_pago_(interno).pdf')}}" target="_blank">
                                    <h3>CFDI de pago</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/interno/manual_estados_de_cuenta_(interno).pdf')}}" target="_blank">
                                    <h3>Estados de cuenta</h3>
                                </a>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>
                                <a href="{{asset('manuales/proveedor/manual_solicitud_de_cotizacion_(proveedor).pdf')}}" target="_blank">
                                    <h3>Solicitud de cotización</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/proveedor/manual_ordenes_de_compra_(proveedor).pdf')}}" target="_blank">
                                    <h3>Órdenes de compra</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/proveedor/manual_facturas_y_notas_de_credito_(proveedor).pdf')}}" target="_blank">
                                    <h3>Facturas y notas de crédito</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/proveedor/manual_cfdi_de_pago_(proveedor).pdf')}}" target="_blank">
                                    <h3>CFDI de pago</h3>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{asset('manuales/proveedor/manual_estados_de_cuenta_(proveedor).pdf')}}" target="_blank">
                                    <h3>Estados de cuenta</h3>
                                </a>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection