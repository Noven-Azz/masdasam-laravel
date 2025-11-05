<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'Dashboard UPKP')</title>

  @php $viteDev = env('VITE_DEV_SERVER_URL', 'http://localhost:5173'); @endphp

  @if (app()->environment('local'))
    <script type="module" src="{{ rtrim($viteDev, '/') }}@@vite/client"></script>
    <script type="module" src="{{ rtrim($viteDev, '/') }}/resources/js/app.js"></script>
    <link rel="stylesheet" href="{{ rtrim($viteDev, '/') }}/resources/css/app.css">
  @else
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
  @endif
</head>
<body class="bg-gray-50">
  <div class="flex flex-col min-h-screen">
    {{-- Sidebar --}}
    @include('components.upkp-dlh-sidebar', ['roleName' => $roleName ?? 'UPKP'])

    {{-- Main Content --}}
    <main class="flex-1 lg:ml-64 xl:ml-80 overflow-y-auto py-6 px-4 mt-20 md:mt-28 lg:mt-0">
      @yield('content')
    </main>
  </div>

  @stack('scripts')
</body>
</html>