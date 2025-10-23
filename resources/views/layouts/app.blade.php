<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="color-scheme" content="light dark">
  <title>@yield('title','Store')</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  @stack('head')
</head>
<body class="font-sans text-neutral-900 bg-white antialiased">
  {{-- HEADER stile Nike --}}
  @include('partials.header')

  {{-- Flash messages (es. “Prodotto aggiunto al carrello”) --}}
  @if(session('success'))
    <div class="container mt-4">
      <div class="rounded-xl bg-green-50 text-green-700 px-4 py-3">
        {{ session('success') }}
      </div>
    </div>
  @endif
  @if($errors->any())
    <div class="container mt-4">
      <div class="rounded-xl bg-red-50 text-red-700 px-4 py-3">
        {{ $errors->first() }}
      </div>
    </div>
  @endif

  <main class="min-h-screen">
    @yield('content')
  </main>

  @include('partials.footer')

  @stack('scripts')
</body>
</html>
