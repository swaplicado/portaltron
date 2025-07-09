<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PP</title>
    <link rel="stylesheet" href="{{ asset('varios/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('varios/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('select2js/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('headStyles')
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <script src="{{ asset('jquery/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vue/vue.js') }}"></script>
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/myApp/gui/SGui.js') }}"></script>
    <script src="{{ asset('moment/moment.js') }}"></script>
    <script src="{{ asset('moment/moment-with-locales.js') }}"></script>
    <script>
        function GlobalData() {
            this.rfc = <?php echo json_encode($rfc); ?>;
            this.key = <?php echo json_encode($key); ?>;
            this.registryRoute = <?php echo json_encode(route('account.registryAndActivateProdviderAccount')); ?>;
            this.loginRoute = <?php echo json_encode(route('login')); ?>;
            this.name = <?php echo json_encode($name); ?>;
            this.shortName = <?php echo json_encode($shortName); ?>;
            this.email = <?php echo json_encode($email); ?>;
            this.fiscal_id = <?php echo json_encode($fiscal_id); ?>;
        }
        var oServerData = new GlobalData();
    </script>

</head>
<body class="sidebar-dark">
    <div class="container-scroller">
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row navbar-dark">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="{{ \App\Utils\Configuration::getConfigurations()->appmanagerRoute }}"><img src="{{ asset('images/aeth.png') }}"
                        class="mr-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="{{ \App\Utils\Configuration::getConfigurations()->appmanagerRoute }}"><img
                        src="{{ asset('images/aeth_mini.png') }}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

            </div>
        </nav>
        <div id="providerAccountActivation">
            <div class="content-wrapper">
                <div class="loader"></div>
                <div class="hiddeToLoad">
                    <div class="container-scroller" v-if="!successRegister">
                        <div class="container-fluid page-body-wrapper full-page-wrapper">
                            <div class="content-wrapper d-flex align-items-center auth px-0">
                                <div class="row w-100 mx-0">
                                    <div class="col-lg-6 mx-auto">
                                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                                            <div class="brand-logo">
                                                <div style="display: inline">
                                                    <img src="{{ asset('images/aeth.png') }}" alt="logo">
                                                    <b style="float: right; font-size: large;">
                                                        Activación de cuenta proveedor
                                                    </b>
                                                </div>
                                            </div>
                                            <h4>!Hola! Vamos a comenzar</h4>
                                            <div class="row">
                                                <div class="col-6">
                                                    <h6 class="font-weight-light">
                                                        Ingresa todos los datos para registrarte como proveedor.
                                                    </h6>
                                                </div>
                                                <div class="col-6" style="text-align: end">
                                                    <a href="{{route('manual_register')}}" style="font-size: small" target="_blank">Click aqui para ver el manual de registro</a>
                                                </div>
                                            </div>
                                            <br>
                                            <form action="#">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group sm-form-group row">
                                                            <label class="col-sm-3 my-col-sm-3 col-form-label ">Razón
                                                                social*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control"
                                                                    id="name" placeholder="Razón social"
                                                                    v-model="name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group sm-form-group row">
                                                            <label class="col-sm-3 my-col-sm-3 col-form-label ">Nombre
                                                                comercial*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control"
                                                                    id="shortName" placeholder="Nombre comercial"
                                                                    v-model="shortName">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group sm-form-group row">
                                                            <label
                                                                class="col-sm-3 my-col-sm-3 col-form-label ">RFC*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control"
                                                                    id="rfc" placeholder="RFC" v-model="rfc" disabled style="background-color: #e9ecef; color: black">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group sm-form-group row">
                                                            <label
                                                                class="col-sm-3 my-col-sm-3 col-form-label ">Correo*</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control"
                                                                    id="email" placeholder="Email" v-model="email">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group sm-form-group row">
                                                            <label class="col-sm-3 my-col-sm-3 col-form-label ">Régimen fiscal</label>
                                                            <div class="col-sm-9">
                                                                <select class="form-control" v-model="fiscal_id"
                                                                style="color: black">
                                                                <option value="" disabled selected hidden>Selecciona régimen fiscal</option>
                                                                    @foreach($lFiscalRegime as $fiscal)
                                                                        <option value="{{$fiscal['id']}}">{{$fiscal['text']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group sm-form-group row">
                                                            <label
                                                                class="col-sm-3 my-col-sm-3 col-form-label ">Contraseña*</label>
                                                            <div class="col-sm-9">
                                                                <div class="input-group">
                                                                    <input :type="typeInputPass" class="form-control"
                                                                        placeholder="Contraseña" id="password"
                                                                        v-model="password">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-sm btn-inverse-dark"
                                                                            type="button" v-on:click="showPass()">
                                                                            <i
                                                                                :class="[showPassword ? 'bx bx-show bx-sm' :
                                                                                    'bx bx-hide bx-sm'
                                                                                ]"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group sm-form-group row">
                                                            <label
                                                                class="col-sm-3 my-col-sm-3 col-form-label ">Confirmar
                                                                contraseña*</label>
                                                            <div class="col-sm-9">
                                                                <input :type="typeInputPass" class="form-control"
                                                                    id="confirmPassword" placeholder="confirmPassword"
                                                                    v-model="confirmPassword">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">

                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <button type="button" class="btn btn-primary" id="btnSave"
                                                            v-on:click="save()">Guardar</button>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
    <script src="{{ asset('varios/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('varios/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('datatables/datatables.js') }}"></script>
    <script src="{{ asset('js/principal/off-canvas.js') }}"></script>
    <script src="{{ asset('js/principal/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/principal/template.js') }}"></script>
    <script src="{{ asset('js/principal/settings.js') }}"></script>
    <script src="{{ asset('js/principal/todolist.js') }}"></script>
    <script src="{{ asset('js/principal/Chart.roundedBarCharts.js') }}"></script>
    <script src="{{ asset('axios/axios.min.js') }}"></script>
    <script src="{{ asset('varios/select2/select2.min.js') }}"></script>
    <script src="{{ asset('myApp/SProviders/vue_providerAccountActivation.js') }}"></script>
    <script src="{{ asset('js/principal/file-upload.js') }}"></script>
    @yield('scripts')
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
</body>

</html>
