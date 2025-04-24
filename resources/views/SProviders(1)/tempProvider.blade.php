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
                        <div class="container-scroller">
                            <div class="container-fluid page-body-wrapper full-page-wrapper">
                                <div class="content-wrapper d-flex align-items-center auth px-0">
                                    <div class="row w-100 mx-0">
                                        <div class="col-lg-6 mx-auto">
                                            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                                                <div class="brand-logo">
                                                    <img src="{{ asset('images/aeth.png') }}" alt="logo">
                                                </div>
                                                <h3>
                                                    Hola {{ $name }}
                                                </h3>
                                                <h4>
                                                    Tu cuenta se encuentra en espera de ser validada.
                                                    </h5>
                                                    <h4>
                                                        Se te notificará cuando el proceso concluya.
                                                    </h4>
                                                    <br>
                                                    <h2>
                                                        <a type="button" class="btn btn-primary"
                                                            href="{{ \App\Utils\Configuration::getConfigurations()->appmanagerRoute }}">Ir
                                                            a la pantalla de inicio de sesión</a>
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
        <script src="{{ asset('myApp/SProviders/vue_guestRegister.js') }}"></script>
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
