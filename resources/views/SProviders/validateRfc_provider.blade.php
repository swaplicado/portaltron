<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PP</title>
    <link rel="stylesheet" href="{{asset('varios/feather/feather.css')}}">
    <link rel="stylesheet" href="{{asset('varios/ti-icons/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('varios/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" />
    <script type="text/javascript" src="{{ asset('vue/vue.js') }}"></script>

    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/myApp/gui/SGui.js') }}"></script>

    <script>
        function GlobalData() {
            this.checkProviderToRegister = <?php echo json_encode(route('account.checkProviderToRegister')); ?>;
        }
        var oServerData = new GlobalData();
    </script>
</head>

<body>
    <div class="container-scroller" id="validateRfc">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5" v-if="!sendMail">
                            <div class="brand-logo">
                                <img src="{{asset('images/aeth.png')}}" alt="logo">
                            </div>
                            <h6 class="font-weight-light">Ingresa tu RFC para continuar, lo validaremos con nuestros
                                registros para que puedas continuar.</h6>
                                <div class="form-group">
                                    <label for="inputRfc">RFC</label>
                                    <input class="form-control form-control-lg" id="inputRfc" placeholder="RFC" name="RFC" autofocus v-model="rfc"/>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" v-on:click="save()" id="btnSave">
                                        Continuar
                                    </button>
                                </div>
                        </div>
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5" v-else>
                            <div class="brand-logo">
                                <img src="{{asset('images/aeth.png')}}" alt="logo">
                            </div>
                            <h6 class="font-weight-light">
                                Te hemos enviado un correo electrónico con un link para que puedas continuar con tu
                                registro, por favor revisa tu bandeja de entrada o tu carpeta de spam.
                                <br>
                                <br>
                                <a class="btn btn-block btn-primary btn-sx font-weight-medium auth-form-btn" href="{{route('home')}}">Regresar</a>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('varios/js/vendor.bundle.base.js')}}"></script>
    <script src="{{asset('js/principal/off-canvas.js')}}"></script>
    <script src="{{asset('js/principal/hoverable-collapse.js')}}"></script>
    <script src="{{asset('js/principal/template.js')}}"></script>
    <script src="{{asset('js/principal/settings.js')}}"></script>
    <script src="{{asset('js/principal/todolist.js')}}"></script>
    <script src="{{ asset('axios/axios.min.js') }}"></script>
    <script src="{{asset('myApp/SProviders/vue_validateRfcProvider.js')}}"></script>
</body>

</html>