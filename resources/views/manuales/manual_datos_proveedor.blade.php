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
            <h3>Actualizar mis datos de proveedor</h3>
        </div>
        <div class="card-body">
            <div class="instruction-container">
                <h3 class="instruction-title">¿Cómo actualizar mis datos de proveedor?</h3>
                <div class="instruction-content">
                    <h4 class="section-title">Para ingresar a la pantalla:</h4>
                    <ol class="steps-list">
                        <li>
                            <span class="step-text">
                                Presiona el botón 
                                <img src="{{ asset('images/manuales/menu_datos_proveedor.png') }}" alt="Botón menu" class="instruction-img"> 
                                del menú lateral izquierdo para ser dirigido a la pantalla de "Mis datos de proveedor" 
                                <span class="note">(Ver la imagen 1)</span>
                            </span>
                        </li>
                    </ol>

                    <div class="info-box">
                        <p class="info-text">Los siguientes campos no son editables:</p>
                        <ul class="non-editable-fields">
                            <li>Razón social</li>
                            <li>Nombre comercial</li>
                            <li>RFC</li>
                            <li>Email para notificaciones</li>
                        </ul>
                    </div>
            
                    <h4 class="section-title">Para actualizar documentos:</h4>
                    <ol class="steps-list">
                        <li>
                            <span class="step-text">Presiona el botón <img src="{{ asset('images/manuales/button_cargar.png') }}" alt="Botón Cargar" class="instruction-img"> para cargar nuevos documentos:</span>
                            <ul class="document-list">
                                <li>Opinión del cumplimiento de obligaciones fiscales</li>
                                <li>Constancia de situación fiscal</li>
                                <li>Comprobante de domicilio</li>
                                <li>Carátula de estado de cuenta bancario</li>
                                <li>Carta de confirmación de datos proporcionados</li>
                            </ul>
                            <span class="step-note">(Los documentos listados están sujetos a cambios)</span>
                        </li>
                        <li>
                            <span class="step-text">Selecciona el documento desde tu dispositivo.</span>
                        </li>
                        <li>
                            <span class="step-text">Guarda los cambios con el botón <img src="{{ asset('images/manuales/button_save2.png') }}" alt="Botón Guardar" class="instruction-img">.</span>
                        </li>
                    </ol>
            
                    <div class="view-docs">
                        <h4 class="section-title">Para ver documentos existentes:</h4>
                        <p>Presiona el botón <img src="{{ asset('images/manuales/button_ver.png') }}" alt="Botón Ver" class="instruction-img"> junto al documento que deseas visualizar.</p>
                    </div>
                </div>
            </div>
            <div class="instruction-container">
                <h3 class="instruction-title">Pantalla de "Mis datos de proveedor"</h3>
                <div class="instruction-content" style="text-align: center">
                    <img src="{{ asset('images/manuales/pantalla_datos_proveedor.png') }}" alt="pantalla datos proveedor" class="screen-img">
                    <span class="step-note">(imagen 1)</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
