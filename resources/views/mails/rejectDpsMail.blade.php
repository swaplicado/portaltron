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

    <!-- CSS principal -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- End CSS principal -->
</head>

<style>
    hr { 
        display: block;
        margin-top: 0.5em;
        margin-bottom: 0.5em;
        margin-left: auto;
        margin-right: auto;
        border-style: inset;
        border-width: 1px;
    }
</style>

<body>
    <div class="main-panel">
        <div class="card">
            <div class="card-body">
                <div>
                    <h3 class="inline">Tu {{$doc_type_name}} {{$dps_folio}} fue cancelada por:</h3>
                </div>
                <div>
                    <h4>{{$comments}}</h4>
                </div>
            </div>
        </div>
    </div>
</body>

</html>