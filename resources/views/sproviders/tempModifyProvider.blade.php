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
    @yield('headStyles')
    <!-- End CSS section -->
    <!-- End CSS files -->

    <!-- Icon browser -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <!-- End icon browser -->

    <!-- Header scripts -->
    <script src="{{ asset('jquery/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vue/vue.js') }}"></script>
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/myApp/gui/SGui.js') }}"></script>
    <script src="{{ asset('moment/moment.js') }}"></script>
    <script src="{{ asset('moment/moment-with-locales.js') }}"></script>
    <!-- Header scripts section -->
    <script>
        function GlobalData() {
            this.oProvider = <?php echo json_encode($oProvider); ?>;
            this.updateRoute = <?php echo json_encode(route('registerProvider.updateTempProvider')); ?>;
            this.lDocs = <?php echo json_encode($lDocs); ?>;
        }
        var oServerData = new GlobalData();
    </script>
    <!-- end Header scripts section-->
    <!-- End header scripts -->

</head>

<body class="sidebar-dark">
    <!-- Page container -->
    <div class="container-scroller">

        <!-- Topbar -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row navbar-dark">

            <!-- logo -->
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="index.html"><img src="{{ asset('images/aeth.png') }}"
                        class="mr-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="index.html"><img
                        src="{{ asset('images/aeth_mini.png') }}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

                <ul class="navbar-nav navbar-nav-right">

                    <!-- Perfil -->
                    <li class="nav-item">
                        <span style="color: white">{{ \Auth::user()->username }}</span>
                    </li>
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            <img src="{{ is_null(\Auth::user()->img_path) ? \App\Utils\Configuration::getConfigurations()->appmanagerRoute . '/ImagesProfiles/default.png' : \App\Utils\Configuration::getConfigurations()->appmanagerRoute . '/' . \Auth::user()->img_path }}"
                                alt="profile" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a href="{{ route('logout') }}" class="dropdown-item">
                                <i class="ti-power-off text-primary"></i>
                                Salir
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- End Topbar -->

        <!-- Main container -->
        <div>
            <!-- Panel principal-->
            <div id="registerProvider">
                <div class="content-wrapper">
                    <div class="loader"></div>
                    <div class="hiddeToLoad">
                        <!-- Panel content -->
                        <div class="container-scroller" v-if="!successUpdate">
                            <div class="container-fluid page-body-wrapper full-page-wrapper">
                                <div class="content-wrapper d-flex align-items-center auth px-0">
                                    <div class="row w-100 mx-0">
                                        <div class="col-lg-6 mx-auto">
                                            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                                                <div class="brand-logo">
                                                    <img src="{{ asset('images/aeth.png') }}" alt="logo">
                                                </div>
                                                <h4>Hola! @{{ name }}</h4>
                                                <br>
                                                <h6 class="font-weight-light" style="color: red">
                                                    <ul>
                                                        <li>
                                                            @{{ comments }}
                                                        </li>
                                                    </ul>
                                                </h6>
                                                <br>
                                                <br>
                                                <form action="#">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group sm-form-group row">
                                                                <label
                                                                    class="col-sm-3 my-col-sm-3 col-form-label ">Razón
                                                                    social</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control"
                                                                        id="name" placeholder="Razón social"
                                                                        v-model="name">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group sm-form-group row">
                                                                <label
                                                                    class="col-sm-3 my-col-sm-3 col-form-label ">Nombre
                                                                    comercial</label>
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
                                                                    class="col-sm-3 my-col-sm-3 col-form-label ">RFC</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control"
                                                                        id="rfc" placeholder="RFC" v-model="rfc">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group sm-form-group row">
                                                                <label
                                                                    class="col-sm-3 my-col-sm-3 col-form-label ">Email</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control"
                                                                        id="email" placeholder="Email"
                                                                        v-model="email">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group sm-form-group row">
                                                                <label class="col-sm-3 my-col-sm-3 col-form-label ">Área</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control" v-model="area_id"
                                                                    style="color: black">
                                                                    <option value="" disabled selected hidden>Selecciona área</option>
                                                                        @foreach($lAreas as $area)
                                                                            <option value="{{$area->id_area}}">{{$area->name_area}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            @foreach($lDocs as $doc)
                                            @if($doc->is_reject)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group sm-form-group row">
                                                            <label class="col-sm-3 my-col-sm-3 col-form-label ">
                                                                {{$doc->name}}
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <input type="file" id="doc_{{$doc->id_request_type_doc}}" name="doc_{{$doc->id_request_type_doc}}"
                                                                    class="file-upload-default" accept=".pdf">
                                                                <div class="input-group col-xs-12">
                                                                    <input type="text"
                                                                        class="form-control file-upload-info" disabled
                                                                        placeholder="Cargar archivo">
                                                                    <span class="input-group-append">
                                                                        <button
                                                                            class="file-upload-browse btn btn-info"
                                                                            type="button">Cargar</button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @endforeach


                                                    <div class="row">
                                                        <div class="col-md-6">

                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <button type="button" class="btn btn-primary"
                                                                v-on:click="save()">Guardar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- content-wrapper ends -->
                            </div>
                            <!-- page-body-wrapper ends -->
                        </div>
                        <div class="container-scroller" v-else>
                            <div class="container-fluid page-body-wrapper full-page-wrapper">
                                <div class="content-wrapper d-flex align-items-center auth px-0">
                                    <div class="row w-100 mx-0">
                                        <div class="col-lg-6 mx-auto">
                                            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                                                <div class="brand-logo">
                                                    <img src="{{ asset('images/aeth.png') }}" alt="logo">
                                                </div>
                                                <h2>
                                                    Tu registro se ha enviado con éxito!!
                                                </h2>
                                                <h4>
                                                    Tu cuenta será revisada para su autorización a la brevedad
                                                </h4>
                                                <br>
                                                <h2>
                                                    <a type="button" class="btn btn-primary"
                                                        href="{{ route('logout') }}">Ir a la pantalla de inicio de sesión</a>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Panel content -->
                    </div>
                    <!-- Footer -->
                    @include('layouts.footer')
                    <!-- End Footer -->
                </div>
                <!-- End Panel principal-->
            </div>
            <!-- End Main container -->
        </div>
    <!-- End Page container -->

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
    <script src="{{ asset('myApp/SProviders/vue_tempModifyProvider.js') }}"></script>
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
