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
            <h3>Atender órdenes de compra</h3>
        </div>
        <div class="card-body">
            <div class="instruction-container">
                <h3 class="instruction-title">¿Cómo atender una orden de compra?</h3>
                <div class="instruction-content">
                    <ol class="steps-list">
                        <li>
                            <span class="step-text">
                                Para ingresar a la pantalla "Órdenes de compra" presiona sobre el botón 
                                <img src="{{ asset('images/manuales/menu_oc.png') }}" alt="Botón Ver" class="instruction-img"> 
                                del menú lateral izquierdo <span class="note">(ver la imagen 1)</span>
                            </span>
                        </li>
                        <li>
                            <span class="step-text">Haz clic sobre el renglón de la tabla de órdenes de compra que desees atender.</span>
                            <span class="step-note">(El renglón se pondrá de color morado indicando que está seleccionado)</span>
                        </li>
                        <li>
                            <span class="step-text">Presiona el botón <img
                                    src="{{ asset('images/manuales/button_show.png') }}" alt="Botón Ver"
                                    class="instruction-img"> para desplegar la ventana con la información y partidas de la orden.
                                    <span class="note">(ver la imagen 2)</span>
                            </span>
                        </li>
                        <div style="text-align: center">
                            <img src="{{ asset('images/manuales/pantalla_oc_1.png') }}" alt="imagen 2" class="screen-img">
                            <span class="step-note">(imagen 2)</span>
                        </div>
                        <li>
                            <span class="step-text">Para marcar la orden como atendida:</span>
                            <ul class="substeps">
                                <li>Ingresa una fecha de entrega presionando <img
                                        src="{{ asset('images/manuales/button_date.png') }}" alt="Botón Fecha"
                                        class="instruction-img"> y seleccionando el día en el calendario.</li>
                                <li>Ingresa un comentario en el recuadro designado.</li>
                            </ul>
                        </li>
                        <li>
                            <span class="step-text">Finalmente, presiona el botón <img
                                    src="{{ asset('images/manuales/button_save.png') }}" alt="Botón Guardar"
                                    class="instruction-img"> para guardar los cambios.</span>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="instruction-container">
                <h3 class="instruction-title">Pantalla de "Órdenes de compra"</h3>
                <div class="instruction-content">
                    <img src="{{ asset('images/manuales/pantalla_oc.png') }}" alt="Botón Ver" class="screen-img">
                    <span class="step-note">(imagen 1)</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
