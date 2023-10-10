<!DOCTYPE html>
<html lang="es">

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Portal proveedores</title>
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
    @yield('headJs')
    <!-- end Header scripts section-->
    <!-- End header scripts -->

</head>

<body class="sidebar-dark">
    <!-- Page container -->
    <div class="container-scroller">

        <!-- Topbar -->
        @include('layouts.topbar')
        <!-- End Topbar -->

        <!-- Main container -->
        <div class="container-fluid page-body-wrapper">

            <!-- Float button -->
            <div class="theme-setting-wrapper">
                <div id="settings-trigger"><i class="ti-settings"></i></div>
                <div id="theme-settings" class="settings-panel">
                    <i class="settings-close ti-close"></i>
                    <p class="settings-heading">SIDEBAR SKINS</p>
                    <div class="sidebar-bg-options selected" id="sidebar-light-theme">
                        <div class="img-ss rounded-circle bg-light border mr-3"></div>Light
                    </div>
                    <div class="sidebar-bg-options" id="sidebar-dark-theme">
                        <div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark
                    </div>
                    <p class="settings-heading mt-2">HEADER SKINS</p>
                    <div class="color-tiles mx-0 px-4">
                        <div class="tiles success"></div>
                        <div class="tiles warning"></div>
                        <div class="tiles danger"></div>
                        <div class="tiles info"></div>
                        <div class="tiles dark"></div>
                        <div class="tiles default"></div>
                    </div>
                </div>
            </div>
            <!-- End Float button-->

            <!-- Right sidebar -->
            @include('layouts.right_sidebar')
            <!-- End Right sidebar -->

            <!-- Menu sidebar -->
            @include('layouts.aside')
            <!-- End Menu sidebar -->

            <!-- Panel principal-->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="loader"></div>
                    <div class="hiddeToLoad">
                        <!-- Panel content -->
                        @yield('content')
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
