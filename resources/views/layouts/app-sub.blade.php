<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('img/sabong-aficionado.ico') }}" type="image/x-icon">
  <title>{{ config('app.name', 'Sabong Aficionado') }}</title>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">

  <!-- Scripts -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" type="text/css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" type="text/css">
  {{--
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css" />
  --}}
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  {{--
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-L6BFWJNTWB"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-L6BFWJNTWB');
  </script>
  @vite(['resources/sass/app.scss', 'public/css/app-sub.scss', 'resources/js/app.js', 'resources/css/app.css'])

  <!-- Custom -->
  <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}" type="text/css">
  @yield('additional-styles')
  <style>
    .active-nav {
      color: var(--bs-link-hover-color);
      font-weight: 700;
    }

    .icon-logo {
      max-height: 2.5rem;
    }

    .logo-container {
      display: flex;
      flex-direction: row;
      flex-wrap: nowrap;
      align-items: center;
    }

    .dataTables_wrapper .dataTables_paginate a.paginate_button {
      margin: 0px;
    }

    #dropdown-menu {
      inset: unset !important;
      transform: none !important;
    }

    #menu>ul>li>a.active {
      color: var(--bs-link-hover-color);
    }

    @media (max-width:767.98px) {
      #site-name {
        display: none;
      }

      .logo-container {
        align-content: center;
      }

      #notif-nav {
        display: block !important;
      }

    }

    .select2-results>ul>li {
      color: black !important;
    }
  </style>

</head>

<body class="dark-mode" style="background-image: url({{ asset('/img/bg2.jpg') }})">
  <div class="top-bar">
    <i class="nav__toggle fa fa-bars" style="color: #fff" aria-hidden="true"></i>
    <a class="nav__username">{{ Auth::user()->username }}</a>
  </div>
  <div class="mobile-mask"></div>
  <header>
    <nav class="nav">
      <div class="m-logo">
        <img class="logo__icon" src="{{ asset('img/sabong-aficionado-icon.png') }}">
        <strong>ISP Console</strong>
      </div>
      @if (hasAccess('Admin'))
        <label class="nav__label">Admin</label>
        <a class="nav__item" href="/admin">
          <i class="fa fa-users" aria-hidden="true"></i>
          <span>USERS</span>
        </a>
        <a class="nav__item" href="/fight">
          <i class="fa-solid fa-gamepad" aria-hidden="true"></i>
          <span>Fight</span>
        </a>
        <a class="nav__item" href="/transactions">
          <i class="fa-solid fa-hand-holding-dollar" aria-hidden="true"></i>
          <span>CI/CO</span>
        </a>
        <a class="nav__item" href="/summary-bet">
          <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
          <span>Bets Summary</span>
        </a>
        <a class="nav__item" href="/reports/betso">
          <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
          <span>Betso88</span>
        </a>
        <a class="nav__item" href="/admin/agents">
          <i class="fa-solid fa-person-harassing" aria-hidden="true"></i>
          <span>Agents</span>
        </a>
        <a class="nav__item" href="/event">
          <i class="fa-solid fa-table-cells" aria-hidden="true"></i>
          <span>Events</span>
        </a>
      @endif

      @if (hasAccess('Guest Admin'))
        <label class="nav__label">Guest Admin</label>
        <a class="nav__item" href="/fight">
          <i class="fa-solid fa-gamepad" aria-hidden="true"></i>
          <span>Fight</span>
        </a>
        <a class="nav__item" href="/transactions">
          <i class="fa-solid fa-hand-holding-dollar" aria-hidden="true"></i>
          <span>Transactions</span>
        </a>
        <a class="nav__item" href="/reports/betso">
          <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
          <span>Bet Summary</span>
        </a>
        <a class="nav__item" href="/admin/agents">
          <i class="fa-solid fa-person-harassing" aria-hidden="true"></i>
          <span>Agents</span>
        </a>
        <a class="nav__item" href="/event">
          <i class="fa-solid fa-table-cells" aria-hidden="true"></i>
          <span>Events</span>
        </a>
      @endif

      @if (Auth::user()->agent)
        <label class="nav__label">Agent</label>
        <a class="nav__item" href="/master-agent">
          <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
          <span>Dashboard</span><span style="color: red" class="hidden badge badge-danger text-red">New</span>
        </a>
      @endif

      <label data-toggle="collapse" data-target="#collapseExample" aria-expanded="true" aria-controls="collapseExample" class="nav__label">Player</label>
      <div class="visible show" id="collapseExample">
        <a class="nav__item" href="/landing">
          <i class="fas fa-home" aria-hidden="true"></i>
          <span>Home</span>
        </a>
        <a class="nav__item" href="/play">
          <i class="fa-solid fa-play" aria-hidden="true"></i>
          <span>Play</span>
        </a>
        <a class="nav__item" href="/user/profile">
          <i class="fa fa-link" aria-hidden="true"></i>
          <span>Profile</span>
        </a>
        <a class="nav__item" href="/deposit">
          <i class="fa-solid fa-money-bill-transfer" aria-hidden="true"></i>
          <span>Cashin</span>
        </a>
        <a class="nav__item" href="/withdraw">
          <i class="fa-solid fa-sack-dollar" aria-hidden="true"></i>
          <span>Cashout</span>
        </a>
        <a class="nav__item" href="/play/history">
          <i class="fa-regular fa-chart-bar" aria-hidden="true"></i>
          <span>Bet history</span>
        </a>
        <a class="nav__item" href="/playertransaction">
          <i class="fas fa-coins" aria-hidden="true"></i>
          <span>Transactions</span>
        </a>
        <a class="nav__item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
          <span>{{ __('Logout') }}</span></a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </div>
      <a href="https://5888112.com/share/SFB.html?disu=5065592" target="_blank">
        <img style="bottom:15px;left:25px;height:100px;position:absolute;" src="{{ asset('img/scatter.png') }}" class="d-none" alt="sabung" /></a>
    </nav>
  </header>
  {{-- <div class="nav-footer">
    <i class="fa fa-angle-left" aria-hidden="true"></i>
  </div> --}}
  <div id="app">
    {{-- <main>
      @yield('content')
    </main> --}}
    <main class="main">
      <div class="content-area">
        @yield('content')
      </div>
    </main>
    @auth
      @if (Auth::user()->user_role->name == 'Player' && Auth::user()->legit)
        <!-- @include('layouts.components.floating-chat') -->
        @include('layouts.components.messenger')
        <script>
          var _user_id = {!! Auth::user()->id !!}
        </script>
      @endif
    @endauth
  </div>
</body>

</html>
{{-- <script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript"></script> --}}
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js" type="text/javascript"></script>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script> --}}
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js" type="text/javascript" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" type="text/javascript"></script>

@yield('additional-scripts')
{{-- @vite('resources/js/socket-vue.js') --}}
<script>
  // const button = document.querySelector('#menu-button');
  // const menu = document.querySelector('#menu');
  // button.addEventListener('click', () => {
  //   menu.classList.toggle('hidden');
  // });

  $(function() {
    setTimeout(function() {
      $('.fade-message').slideUp();
    }, 5000);

    $('[data-bs-toggle="tooltip"]').tooltip();
    // $('#menu-button').tooltip('show');

    // let pathName = window.location.pathname;
    // if(pathName.split('/')[1] == 'landing') {
    //   $('.tooltip-inner').css('background-color','#f00').css('color','black');
    // }

    //display flyout mobile-menu
    $('.nav__toggle').on('click', function() {
      $('.nav, .mobile-mask').toggleClass('show');
    });

    $('.mobile-mask').on('click', function() {
      $('.nav, .mobile-mask').removeClass('show');
    });

    var route = window.location.pathname
    var link = document.querySelector('[href="' + route + '"]')
    link.classList.add('overview')
  });
</script>
