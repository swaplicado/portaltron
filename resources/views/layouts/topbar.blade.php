<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row navbar-dark">

      <!-- logo -->
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{route('home')}}"><img src="{{asset(session()->get('companie_logo'))}}" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="{{route('home')}}"><img src="{{asset(session()->get('companie_logo_mini'))}}" alt="logo"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

      <!-- icono Menu -->
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-search d-none d-lg-block">
            <h3 style="color:white">{{session()->get('companie_name')}}</h3>
          </li>
        </ul>

        <ul class="navbar-nav navbar-nav-right">
          <!-- Perfil -->
          <li class="nav-item">
            <span style="color: white">
              @if(!\Auth::user()->is_provider())
                  {{ \Auth::user()->username }}
              @else
                  {{\Auth::user()->getProviderData()->provider_short_name}}
              @endif
            </span>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="{{ is_null(\Auth::user()->img_path) ? \App\Utils\Configuration::getConfigurations()->appmanagerRoute . '/ImagesProfiles/default.png' : \App\Utils\Configuration::getConfigurations()->appmanagerRoute . '/' . \Auth::user()->img_path }}" alt="profile" />
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a href="{{ \App\Utils\Configuration::getConfigurations()->appmanagerProfileRoute }}" target="_blank" class="dropdown-item">
                <i class="ti-settings text-primary"></i>
                Mi perfil
              </a>
              <a href="{{ route('logout') }}" class="dropdown-item">
                <i class="ti-power-off text-primary"></i>
                Salir
              </a>
            </div>
          </li>

          <!-- boton side derecha -->
          
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
</nav>