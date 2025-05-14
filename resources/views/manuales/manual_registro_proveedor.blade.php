<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SIIE APP</title>
    <!-- End meta tags -->

    <!-- CSS files-->
    <link rel="stylesheet" href="{{ asset('varios/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('select2js/css/select2.min.css') }}">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('datatables/datatables.css') }}">
    <!-- End datatables CSS -->

    <!-- CSS principal -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- End CSS principal -->

    <!-- CSS section -->
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
    <!-- End CSS section -->
    <!-- End CSS files -->

    <!-- Icon browser -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <!-- End icon browser -->

</head>

<body class="sidebar-dark">

    <!-- Page container -->
    <div class="container-scroller">

        <!-- Topbar -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row navbar-dark">
            <!-- logo -->
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="{{ \App\Utils\Configuration::getConfigurations()->appmanagerRoute }}"><img src="{{ asset('images/aeth.png') }}"
                        class="mr-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="{{ \App\Utils\Configuration::getConfigurations()->appmanagerRoute }}"><img
                        src="{{ asset('images/aeth_mini.png') }}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

            </div>
        </nav>


    <div class="card">
        <div class="card-header">
            <h3>Registrarse como proveedor</h3>
        </div>
        <div class="card-body">
            <div class="instruction-container">
                <h3 class="instruction-title">¿Cómo registrarme como proveedor?</h3>
                <div class="instruction-content">
                    <h4 class="section-title">Para ingresar a la pantalla:</h4>
                    <ol class="steps-list">
                        <li>
                            <span class="step-text">
                                Presiona el botón 
                                <img src="{{ asset('images/manuales/menu_registrarse.png') }}" alt="Botón menu" class="instruction-img"> 
                                ubicado en la parte inferior derecha de la pantalla de inicio de sesión.
                                <span class="note">(Ver la imagen 1)</span>
                            </span>
                        </li>
                    </ol>
            
                    <h4 class="section-title">Para registrarse:</h4>
                    <ol class="steps-list">
                        <li>
                            <span class="step-text">
                                Ingresa la razón social de tu entidad comercial en el campo "Razón social*".
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Ingresa el nombre de tu entidad comercial en el campo "Nombre comercial*".
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Ingresa el RFC de tu entidad comercial en el campo "RFC*".
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Ingresa la dirección de correo electronico en el que recibirás las notificaciones del sistema en el campo "Correo*".
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Ingresa la contraseña con la cual ingresarás al sistema en el campo "Contraseña*", para mostrar la contraseña que introduces presiona 
                                el botón <img src="{{ asset('images/manuales/button_show_password.png') }}" alt="Botón Guardar" class="instruction-img">
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Confirma la contraseña introducida anteriormente en el campo "Contraseña*" introduciendo la misma en el campo "Confirmar contraseña*", ambas 
                                contraseñas deben coincidir.
                            </span>
                        </li>
                        <li>
                            <span class="step-text">
                                Selecciona el área de destino al cual va dirigido el registro, presiona sobre el campo "Área destino" para desplegar las opciones, luego 
                                presiona sobre el área deseada.
                            </span>
                        </li>
                    </ol>

                    <h4 class="section-title">Para cargar tus documentos:</h4>
                    <ol>
                        <li>
                            <span class="step-text">Presiona el botón <img src="{{ asset('images/manuales/button_cargar.png') }}" alt="Botón Cargar" class="instruction-img"> para cargar cada documento:</span>
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
                            <span class="step-text">Finalmente, presiona el botón <img src="{{ asset('images/manuales/button_save2.png') }}" alt="Botón Guardar" class="instruction-img"> 
                               para registrarte como proveedor.
                            </span>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="instruction-container">
                <h3 class="instruction-title">Pantalla de "Registro de nuevo proveedor AETH"</h3>
                <div class="instruction-content" style="text-align: center">
                    <img src="{{ asset('images/manuales/pantalla_registro.png') }}" alt="pantalla datos proveedor" class="screen-img">
                    <span class="step-note">(imagen 1)</span>
                </div>
            </div>
        </div>
    </div>
    </div>

        <!-- JS files -->
        <script src="{{ asset('varios/js/vendor.bundle.base.js') }}"></script>
        <script src="{{ asset('varios/chart.js/Chart.min.js') }}"></script>
        <!-- Datatables js -->
        <script src="{{ asset('datatables/datatables.js') }}"></script>
        <!-- End datatables js -->
        <script src="{{ asset('js/principal/off-canvas.js') }}"></script>
        <script src="{{ asset('js/principal/hoverable-collapse.js') }}"></script>
        <script src="{{ asset('js/principal/template.js') }}"></script>
        <script src="{{ asset('js/principal/settings.js') }}"></script>
        <script src="{{ asset('js/principal/todolist.js') }}"></script>
        <script src="{{ asset('js/principal/Chart.roundedBarCharts.js') }}"></script>
        <script src="{{ asset('axios/axios.min.js') }}"></script>
        <script src="{{ asset('varios/select2/select2.min.js') }}"></script>
        <script src="{{ asset('myApp/SProviders/vue_guestRegister.js') }}"></script>
        <script src="{{ asset('js/principal/file-upload.js') }}"></script>
        <!-- JS section -->
        @yield('scripts')
        <!-- End JS section -->
    
        <script>
            window.onload = function() {
    
                const loader = document.querySelector('.loader');
                loader.style.opacity = 0; /* Cambia la opacidad a 0 para que el círculo desaparezca */
    
                var elementos = document.getElementsByClassName("hiddeToLoad");
                for (var i = 0; i < elementos.length; i++) {
                    // Establecer el estilo "display" de cada elemento a "block"
                    elementos[i].style.display = 'block';
                }
                loader.style.display = 'none'; /* Oculta el círculo después de una pequeña transición */
    
            };
        </script>
        <!-- End JS files -->
    
    </body>
    
    </html>
    