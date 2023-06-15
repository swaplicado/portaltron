<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Skydash Admin</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{asset('varios/feather/feather.css')}}">
  <link rel="stylesheet" href="{{asset('varios/ti-icons/css/themify-icons.css')}}">
  <link rel="stylesheet" href="{{asset('varios/css/vendor.bundle.base.css')}}">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" />

  <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
  <script src="{{ asset('js/myApp/gui/SGui.js') }}"></script>
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="{{asset('images/aeth.png')}}" alt="logo">
              </div>
              <h4>Hola! vamos a comenzar</h4>
              <h6 class="font-weight-light">Inicia sesión para continuar.</h6>
              <form id="login_form" class="pt-3" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                  <label for="exampleInputEmail1">Usuario</label>
                  <input class="form-control form-control-lg @error('username') is-invalid @enderror" id="exampleInputEmail1" placeholder="username" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                  @error('username')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Contraseña</label>
                  <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="exampleInputPassword1" placeholder="Contraseña" name="password" required autocomplete="current-password">
                  @error('password')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Continuar</button>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <a href="#" class="auth-link text-black">¿Olvidaste la contraseña?</a>
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
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{asset('varios/js/vendor.bundle.base.js')}}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{asset('js/principal/off-canvas.js')}}"></script>
  <script src="{{asset('js/principal/hoverable-collapse.js')}}"></script>
  <script src="{{asset('js/principal/template.js')}}"></script>
  <script src="{{asset('js/principal/settings.js')}}"></script>
  <script src="{{asset('js/principal/todolist.js')}}"></script>
  <script src="{{asset('js/myApp/login/Login_js.js')}}"></script>
  <!-- endinject -->
@if(session('message') != null)
    <script>
      let Message = "<?php echo session('message') ?>";
      $(document).ready(function () {
          showMessage(Message);
      });
    </script>
@endif
</body>
</html>