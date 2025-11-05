<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'Banyumas - UI')</title>

  @php $viteDev = env('VITE_DEV_SERVER_URL', 'http://localhost:5174'); @endphp

  @if (app()->environment('local'))
    <script type="module" src="{{ rtrim($viteDev, '/') }}/@@vite/client"></script>
    <script type="module" src="{{ rtrim($viteDev, '/') }}/resources/js/app.js"></script>
    <link rel="stylesheet" href="{{ rtrim($viteDev, '/') }}/resources/css/app.css">
  @else
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
  @endif
</head>
<body class="bg-gray-50 text-slate-800">
  @yield('content')
</body>
</html>