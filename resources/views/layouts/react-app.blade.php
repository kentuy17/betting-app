<!DOCTYPE html>
<html lang="en" class="dark" style="color-scheme: dark;">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('img/sabong-aficionado.ico') }}" type="image/x-icon">
  <title>{{ config('app.name', 'Sabong Aficionado') }}</title>
  @vite(['resources/js/app.js', 'resources/css/app.css'])
  {{-- 'resources/sass/app.scss',  --}}
  @yield('additional-styles')
</head>

<body>
  <div id="app">
    <main>
      @yield('content')
    </main>
  </div>

  @yield('additional-scripts')
</body>

</html>
