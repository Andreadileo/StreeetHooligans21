<x-layout title="Home">
  <section class="container-xxl mb-5">
    <div class="hero-minimal p-4 p-lg-5">
      <div class="row g-4 g-lg-5 align-items-center">
        <div class="col-lg-6">
          <span class="feature-chip mb-3">
            Streetwear selezionato
          </span>
          <h1 class="display-5 fw-bold mb-3">
            Outfit essenziali per vivere la città con stile minimale.
          </h1>
          <p class="lead text-muted-soft mb-4">
            StreetHooligans cura drop limitati e capi evergreen. Nessun eccesso, solo silhouette pulite e materiali premium.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('catalog.index') }}" class="btn btn-dark btn-pill btn-lg">
              Esplora il catalogo
            </a>
            <a href="{{ route('cart.index') }}" class="btn btn-outline-dark btn-pill">
              Vai al carrello
            </a>
          </div>

          <div class="d-flex flex-column flex-sm-row gap-3 mt-4 pt-2">
            <div class="floating-pill">
              Nuove release ogni settimana
            </div>
            <div class="floating-pill">
              Spedizioni rapide 24/48h
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="position-relative">
            <img src="{{ asset('images/rare.jpg') }}" alt="Lookbook StreetHooligans" class="w-100">
            <div class="hero-minimal__shape"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="container-xxl mb-5">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="stacked-card h-100">
          <span class="status-pill mb-3">Capsule Selezionate</span>
          <h2 class="h4">Una curatela minimale</h2>
          <p class="text-muted-soft mb-0">
            Ogni capo è scelto per materiali, palette neutre e vestibilità fluida. Costruisci il tuo guardaroba essenziale senza pensieri.
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stacked-card h-100">
          <span class="status-pill mb-3">Pagamenti sicuri</span>
          <h2 class="h4">Checkout lineare</h2>
          <p class="text-muted-soft mb-0">
            Gestione carrello semplificata, stock in tempo reale e checkout guidato. Il tutto pronto per integrazioni future di pagamento.
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stacked-card h-100">
          <span class="status-pill mb-3">Supporto rapido</span>
          <h2 class="h4">Assistenza umana</h2>
          <p class="text-muted-soft mb-0">
            Una volta consegnato il progetto potrai aggiungere live chat, FAQ dinamiche e automazioni personalizzate.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="container-xxl">
    <div class="section-heading mb-4">
      <h2 class="h3 fw-bold m-0">Ultimi drop caricati</h2>
      <a class="cta-link text-decoration-none" href="{{ route('catalog.index') }}">Vedi il catalogo completo →</a>
    </div>

    @if($products->isEmpty())
      <div class="text-center border rounded-4 py-5 px-3 bg-white shadow-sm">
        <h3 class="h5 mb-2">Ancora nessun prodotto</h3>
        <p class="text-muted-soft mb-4">
          Aggiungi articoli dal seeder o dall’area admin e compariranno qui in automatico.
        </p>
        <a class="btn btn-dark btn-pill" href="{{ route('catalog.index') }}">Vai alla gestione catalogo</a>
      </div>
    @else
      <div class="row g-4">
        @foreach($products as $product)
          @include('partials.product-card', ['product' => $product])
        @endforeach
      </div>
    @endif
  </section>
</x-layout>
