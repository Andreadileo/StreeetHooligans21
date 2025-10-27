<x-layout title="Home">
  <section class="container-xxl mb-5">
    <div class="hero-minimal p-4 p-lg-5">
      <div class="row g-4 g-lg-5 align-items-center">
        <div class="col-lg-6">
          <span class="feature-chip mb-3">
            Streetwear selezionato
          </span>
          <h1 class="display-5 fw-bold mb-3">
            Per farti vivere la città come se fossi sempre con la squadra.
          </h1>
          <p class="lead text-muted-soft mb-4">
            StreetHooligans cura drop limitati. Nessun eccesso, solo silhouette pulite e materiali premium.
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

  @php
    $brandSpotlight = [
      ['name' => 'Andy Capp', 'image' => 'images/brands/andy-capp.jpeg', 'brand' => 'Andy Capp'],
      ['name' => 'Burlington', 'image' => 'images/brands/burlington.jpeg', 'brand' => 'Burlington'],
      ['name' => 'Farah', 'image' => 'images/brands/farah.jpeg', 'brand' => 'Farah'],
      ['name' => 'The Casual Thug', 'image' => 'images/brands/casual-thug.jpeg', 'brand' => 'The Casual Thug'],
      ['name' => 'Shambles', 'image' => 'images/brands/shambles.jpeg', 'brand' => 'Shambles'],
      ['name' => 'Lyle & Scott', 'image' => 'images/brands/lyle-scott.jpeg', 'brand' => 'Lyle & Scott'],
      ['name' => 'Marshall Artist', 'image' => 'images/brands/marshall-artist.jpeg', 'brand' => 'Marshall Artist'],
      ['name' => 'Weekend Offender', 'image' => 'images/brands/weekend-offender.jpeg', 'brand' => 'Weekend Offender'],
    ];
  @endphp

  <section class="container-xxl mb-5">
    <div class="section-heading mb-4">
      <h2 class="h4 fw-bold m-0">Brand selezionati</h2>
      <a class="cta-link text-decoration-none" href="{{ route('catalog.index') }}">Vai al catalogo completo →</a>
    </div>
    <div class="brand-grid">
      @foreach($brandSpotlight as $brand)
        <a class="brand-card text-center text-decoration-none" href="{{ route('catalog.index', ['brand' => $brand['brand']]) }}">
          <div class="brand-card__media">
            <img src="{{ asset($brand['image']) }}" alt="Logo {{ $brand['name'] }}" loading="lazy">
          </div>
            <span class="brand-card__label">{{ $brand['name'] }}</span>
        </a>
      @endforeach
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
