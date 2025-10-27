<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'StreetHooligans' }}</title>
  @vite(['resources/js/app.js']) {{-- importa anche lo SCSS --}}
</head>
<body>
  @php
    $shippingMsg = 'Ordini sotto i 99€ → spedizione a pagamento · Supera la soglia e la consegna è gratuita · StreetHooligans';
  @endphp
  <div class="announcement-bar">
    <div class="announcement-track">
      <span class="announcement-text">{{ $shippingMsg }}</span>
    </div>
  </div>
  <header class="py-3">
    <nav class="navbar navbar-expand-lg bg-white navbar-light container-xxl">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}" style="padding-left: .75rem;">
        <img src="{{ asset('images/Hooligan con cappello e bottiglia.png') }}" alt="StreetHooligans logo" width="36" height="36" class="rounded-circle">
        <span>StreetHooligans</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      @php
        $cart = session('cart', ['items'=>[]]);
        $cartCount = is_array($cart['items'] ?? null) ? collect($cart['items'])->sum('qty') : 0;
      @endphp

      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-3">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('catalog.index') }}">Catalogo</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('about') }}">Chi siamo</a>
          </li>
          <li class="nav-item">
            <a class="nav-link position-relative" href="{{ route('cart.index') }}">
              Carrello
              @if($cartCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-white">
                  {{ $cartCount }}
                </span>
              @endif
            </a>
          </li>
          @auth
            @if(auth()->user()->is_admin)
              <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.products.index') }}">Gestisci prodotti</a>
              </li>
            @endif
          @endauth
        </ul>
      </div>
    </nav>
  </header>

  <main class="py-4 py-md-5">
    {{ $slot }}
  </main>

  <footer class="py-5">
    <div class="container-xxl d-flex flex-column flex-lg-row justify-content-between gap-3 small text-muted">
      <div>© {{ date('Y') }} StreetHooligans — demo store.</div>
      <div>Design minimale curato per la consegna del progetto.</div>
    </div>
  </footer>
@stack('script')
</body>
</html>
