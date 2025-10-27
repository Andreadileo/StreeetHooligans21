<x-layout title="Catalogo">
  <div class="container-xxl py-4 py-lg-5">
    <section class="mb-4">
      <div class="rounded-4 border bg-light p-4 p-lg-5 position-relative overflow-hidden">
        <span class="badge text-bg-dark text-uppercase mb-2">Nuovi arrivi</span>
        <h1 class="display-6 fw-bold mb-3">Catalogo StreetHooligans</h1>
        <p class="text-muted mb-0">
          Trova le ultime drop selezionate. Usa la ricerca rapida e filtra per taglia per scoprire cosa è disponibile in questo momento.
        </p>
        <div class="position-absolute top-0 end-0 translate-middle-y opacity-25 d-none d-md-block" style="width:180px;height:180px;background:radial-gradient(circle at 30% 30%, rgba(28,75,16,.35), transparent 70%);"></div>
      </div>
    </section>

    <section class="mb-4">
      <form method="GET" class="row g-3 align-items-end bg-white border rounded-4 p-3 p-lg-4 shadow-sm">
        <div class="col-12 col-lg-6">
          <label for="catalog-search" class="form-label text-uppercase small fw-semibold text-muted">Cerca</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-transparent border-end-0 text-muted">🔍</span>
            <input id="catalog-search"
                   type="search"
                   class="form-control border-start-0"
                   name="q"
                   value="{{ $q }}"
                   placeholder="Prodotti, brand o colore">
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <label for="catalog-size" class="form-label text-uppercase small fw-semibold text-muted">Taglia</label>
          <select id="catalog-size" name="size" class="form-select">
            <option value="">Tutte</option>
            @foreach($allSizes as $s)
              <option value="{{ $s }}" @selected($size===$s)>{{ $s }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-lg-3 d-flex gap-2">
          <button class="btn btn-dark flex-grow-1">
            Filtra
          </button>
          <input type="hidden" name="brand" value="{{ $brand }}">
          @if($q || $size || $brand)
            <a class="btn btn-outline-secondary" href="{{ route('catalog.index') }}">Reset</a>
          @endif
        </div>
      </form>
      @if($brand)
        @php
          $brandlessQuery = array_filter(['q' => $q, 'size' => $size], fn ($value) => filled($value));
        @endphp
        <div class="mt-3">
          <div class="active-brand-pill d-inline-flex align-items-center gap-2 px-3 py-2 bg-white border rounded-pill shadow-sm small">
            <span class="text-muted text-uppercase">Brand</span>
            <strong class="text-dark">{{ $brand }}</strong>
            <a class="text-decoration-none text-danger lh-1" href="{{ route('catalog.index', $brandlessQuery) }}">&times;</a>
          </div>
        </div>
      @endif
    </section>

    <section>
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div>
          <span class="text-muted text-uppercase small">Prodotti trovati</span>
          <div class="fw-semibold h5 mb-0">{{ $products->total() }}</div>
        </div>
        @if($products->hasPages())
          <div class="small text-muted">
            Pagina {{ $products->currentPage() }} di {{ $products->lastPage() }}
          </div>
        @endif
      </div>

      @if($products->isEmpty())
        <div class="text-center border rounded-4 py-5 px-3 bg-light">
          <h2 class="h5 mb-2">Ops, niente da mostrare.</h2>
          <p class="text-muted mb-4">Prova a modificare la ricerca o a scegliere un’altra taglia.</p>
          <a class="btn btn-outline-dark" href="{{ route('catalog.index') }}">Mostra tutto</a>
        </div>
      @else
        <div class="row g-4">
          @foreach($products as $product)
            @include('partials.product-card', ['product' => $product])
          @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
          {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
      @endif
    </section>
  </div>
</x-layout>
