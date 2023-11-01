@extends('layouts.principal')

@section('headStyles')

@endsection

@section('headJs')

@endsection

@section('content')
  
<div class="card">
    <div class="card-header">
        <h3>Actualizaciones</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <ul>
                <li>
                    <b>
                        Actualización 01-11-2023
                    </b>
                    <ul>
                        <li>
                            Eliminada la opción de área de destino al registrarse un proveedor y al subir un documento 
                            (Las facturas se enviarán al área que corresponda a la serie)
                        </li>
                        <li>
                            Separación de la vista de facturas y la vista de notas de crédito
                        </li>
                        <li>
                            Ahora se puede referenciar a más de una orden de compra al subir una factura
                        </li>
                        <li>
                            Se agregaron notificaciones vía email
                        </li>
                        <li>
                            Se pueden reenviar documentos a otras áreas
                        </li>
                        <li>
                            Se pueden consultar documentos que no tienen área asignada
                        </li>
                        <li>
                            Ahora las vistas de los documentos (Facturas, Notas de crédito, CFDI de pago) 
                            cargan por defecto todos los documentos de todos los proveedores
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection