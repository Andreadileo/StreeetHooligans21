<x-layout title="Bacheca">
  <section class="container-xxl py-4 py-lg-5">
    <div class="row g-4">
      <div class="col-12">
        <div class="content-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
          <div>
            <h1 class="h4 mb-1">Bentornato, {{ auth()->user()->name }}!</h1>
            <p class="text-muted-soft mb-0">Da qui puoi gestire il negozio oppure tornare subito al catalogo pubblico.</p>
          </div>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary btn-pill" href="{{ route('home') }}">Vai al sito</a>
            <a class="btn btn-outline-secondary btn-pill" href="{{ route('catalog.index') }}">Mostra catalogo</a>
            <a class="btn btn-outline-secondary btn-pill" href="{{ route('cart.index') }}">Apri carrello</a>
          </div>
        </div>
      </div>

      @if(auth()->user()->is_admin)
        <div class="col-12 col-lg-8">
          <div class="content-card h-100">
            <h2 class="h5 mb-3">Gestione rapida</h2>
            <p class="text-muted-soft small mb-4">Operazioni principali che usi più spesso.</p>
            <div class="d-grid gap-3">
              <a class="btn btn-dark btn-pill" href="{{ route('admin.products.index') }}">Gestisci prodotti</a>
              <a class="btn btn-outline-dark btn-pill" href="{{ route('admin.products.create') }}">Aggiungi nuovo prodotto</a>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="content-card h-100">
            <h2 class="h6 mb-2">Account admin</h2>
            <p class="text-muted-soft small mb-3">Se vuoi delegare il pannello, crea un nuovo utente e imposta <code>is_admin</code> a <code>true</code>.</p>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="btn btn-outline-danger btn-pill w-100" type="submit">Esci</button>
            </form>
          </div>
        </div>
      @else
        <div class="col-12 col-lg-4">
          <div class="content-card h-100">
            <h2 class="h6 mb-2">Il tuo account</h2>
            <p class="text-muted-soft small mb-3">Aggiorna dati personali o lascia un feedback.</p>
            <div class="d-grid gap-2">
              <a class="btn btn-outline-dark btn-pill" href="{{ route('profile.edit') }}">Modifica profilo</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-secondary btn-pill" type="submit">Esci</button>
              </form>
            </div>
          </div>
        </div>
      @endif
    </div>
  </section>
</x-layout>
