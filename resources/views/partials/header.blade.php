<header x-data="{ open:false }" class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-neutral-200">
  <nav class="container flex items-center justify-between h-16">
    <a href="{{ route('home') }}" class="font-bold text-xl tracking-tight">PRESTO</a>

    <ul class="hidden md:flex items-center gap-6">
      <li><a class="text-sm hover:opacity-80" href="{{ route('catalog.index') }}">Catalogo</a></li>
      <li><a class="text-sm hover:opacity-80" href="{{ route('cart.index') }}">Carrello</a></li>
    </ul>

    <div class="flex items-center gap-3">
      @auth
        <a href="{{ route('dashboard') }}" class="text-sm hover:opacity-80">Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="text-sm hover:opacity-80">Accedi</a>
      @endauth
      <button class="md:hidden" @click="open=!open" aria-label="Apri menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
        </svg>
      </button>
    </div>
  </nav>

  <div x-cloak x-show="open" x-transition class="md:hidden border-t border-neutral-200 bg-white">
    <ul class="p-4 space-y-3">
      <li><a href="{{ route('catalog.index') }}" class="block py-2">Catalogo</a></li>
      <li><a href="{{ route('cart.index') }}" class="block py-2">Carrello</a></li>
      @auth
        <li><a href="{{ route('dashboard') }}" class="block py-2">Dashboard</a></li>
      @else
        <li><a href="{{ route('login') }}" class="block py-2">Accedi</a></li>
      @endauth
    </ul>
  </div>
</header>
