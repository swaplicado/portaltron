@extends('layouts.principal')

@section('headStyles')
<style>
    .instruction-container {
        max-width: 70%;
        margin: 20px auto;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .instruction-title {
        color: #4a2c82;
        /* Morado corporativo */
        border-bottom: 2px solid #e1bee7;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .steps-list {
        counter-reset: step-counter;
        padding-left: 20px;
    }

    .step {
        position: relative;
        margin-bottom: 15px;
        padding-left: 30px;
        line-height: 1.6;
    }

    .step:before {
        counter-increment: step-counter;
        content: counter(step-counter);
        position: absolute;
        left: 0;
        top: 0;
        background-color: #4a2c82;
        color: white;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        text-align: center;
        font-size: 14px;
        line-height: 22px;
    }

    .step-note {
        display: block;
        font-size: 0.9em;
        color: #666;
        font-style: italic;
        margin-top: 5px;
    }
    .note {
        font-size: 0.9em;
        color: #666;
        font-style: italic;
        margin-top: 5px;
    }

    .substeps {
        margin-top: 10px;
        padding-left: 20px;
        list-style-type: disc;
    }

    .substeps li {
        margin-bottom: 8px;
    }

    .instruction-img {
        height: 35px;
        vertical-align: middle;
        margin: 0 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .screen-img {
        height: 350px;
        vertical-align: middle;
        margin: 0 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>
@endsection

@section('headJs')
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Cargar facturas</h3>
        </div>
        <div class="card-body">
            <div class="instruction-container">
                <h3 class="instruction-title">¿Cómo cargar una factura?</h3>
                <div class="instruction-content">
                    <h4 class="section-title">Para ingresar a la pantalla:</h4>
                    <ol class="steps-list">
                        <li>
                            <span class="step-text">
                                Presiona el botón 
                                <img src="{{ asset('images/manuales/menu_facturas.png') }}" alt="Botón menu" class="instruction-img"> 
                                del menú lateral izquierdo para ser dirigido a la pantalla de "Facturas" 
                                <span class="note">(Ver la imagen 1)</span>
                            </span>
                        </li>
                    </ol>
            
                    <h4 class="section-title">Para cargar la factura:</h4>
                    <ol class="steps-list">
                        <li>
                            <span class="step-text">
                                Presiona el botón <img src="{{ asset('images/manuales/button_upload.png') }}" alt="Botón Cargar" class="instruction-img"> 
                                para desplegar la ventana con la forma para cargar la factura nueva. <span class="note">(ver la imagen 2)</span>
                            </span>
                        </li>
                        <div style="text-align: center">
                            <img src="{{ asset('images/manuales/pantalla_facturas_2.png') }}" alt="imagen 2" class="screen-img">
                            <span class="step-note">(imagen 2)</span>
                        </div>
                        <li>
                            <span class="step-text">
                                Selecciona el área a la que va dirigida la factura presionando sobre el campo "Área destino", luego 
                                presiona sobre una de las áreas desplegadas.
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Ingresa el número de referencia de la orden de compra ligada a la factura en el campo "Referencia".
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Ingresa la serie y el folio de la factura en el campo "Serie y folio factura".
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Para cargar el PDF de la factura en el campo "PDF" presiona el botón <img src="{{ asset('images/manuales/button_cargar.png') }}" alt="Botón Cargar" class="instruction-img"> 
                                para seleccionar el documento desde tu dispositivo.
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Para cargar el XML de la factura en el campo "XML" presiona el botón <img src="{{ asset('images/manuales/button_cargar.png') }}" alt="Botón Cargar" class="instruction-img"> 
                                para seleccionar el documento desde tu dispositivo.
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Finalmente, presiona el botón 
                                <img src="{{ asset('images/manuales/button_save.png') }}" alt="Botón Guardar" class="instruction-img"> 
                                para guardar la factura en el sistema.
                            </span>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="instruction-container">
                <h3 class="instruction-title">¿Cómo ver una factura ya cargada en sistema?</h3>
                <ol class="steps-list">
                    <li>
                        <span class="step-text">Haz clic sobre el renglón de la tabla de facturas que desees ver.</span>
                        <span class="step-note">(El renglón se pondrá de color morado indicando que está seleccionado)</span>
                    </li>
                    <li>
                        <span class="step-text">Presiona el botón <img
                                src="{{ asset('images/manuales/button_show.png') }}" alt="Botón Ver"
                                class="instruction-img"> para desplegar la ventana con la información de la factura.
                                <span class="note">(ver la imagen 3)</span>
                        </span>
                    </li>
                    <div style="text-align: center">
                        <img src="{{ asset('images/manuales/pantalla_facturas_3.png') }}" alt="imagen 3" class="screen-img">
                        <span class="step-note">(imagen 3)</span>
                    </div>
                    <li>
                        <span class="step-text">Para abrir el PDF o el XML presiona el botón <img
                                src="{{ asset('images/manuales/button_ver2.png') }}" alt="Botón Ver"
                                class="instruction-img"> para abrir una pestaña con el documento correspondiente de la factura.
                        </span>
                    </li>
                    <li>
                        <span class="step-text">Para descargar el PDF o el XML presiona el botón <img
                                src="{{ asset('images/manuales/button_descargar.png') }}" alt="Botón Ver"
                                class="instruction-img">
                        </span>
                    </li>
                </ol>
            </div>
            <div class="instruction-container">
                <h3 class="instruction-title">Pantalla de "Facturas"</h3>
                <div class="instruction-content">
                    <img src="{{ asset('images/manuales/pantalla_facturas.png') }}" alt="Botón Ver" class="screen-img" style="max-width: -webkit-fill-available;">
                    <span class="step-note">(imagen 1)</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
