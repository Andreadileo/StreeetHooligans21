<x-layout :title="$product->name">
  <div class="container-xxl py-4 py-lg-5">

    {{-- Breadcrumb --}}
    <nav class="small text-muted mb-4">
      <a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a>
      <span class="mx-2">/</span>
      <a href="{{ route('catalog.index') }}" class="text-muted text-decoration-none">Catalogo</a>
      <span class="mx-2">/</span>
      <span>{{ $product->name }}</span>
    </nav>

    <div class="row g-5 align-items-start">
      {{-- COLONNA SINISTRA: GALLERY --}}
      <div class="col-lg-6">
        @php
          $ratioVariant = $product->getImageRatioVariant();
          $viewerModifier = $ratioVariant !== 'standard' ? ' product-viewer--' . $ratioVariant : '';
        @endphp
        <div class="content-card p-3">
          <div class="product-viewer rounded-4 overflow-hidden{{ $viewerModifier }}">
            <img id="mainImage"
                 src="{{ $product->cover_image_url }}"
                 alt="{{ $product->name }}"
                 class="w-100 h-100 object-fit-cover">
          </div>
        </div>

        @php
          // immagini extra: se hai $product->images come JSON/array di path
          $normaliseImg = static function ($value) {
              if (filter_var($value, FILTER_VALIDATE_URL)) {
                  return $value;
              }

              $trimmed = ltrim($value, '/');

              if (\Illuminate\Support\Str::startsWith($trimmed, ['storage/', 'images/', 'img/', 'assets/', 'build/'])) {
                  return asset($trimmed);
              }

              return asset('storage/' . $trimmed);
          };

          $thumbs = collect($product->images ?? [])
              ->filter()
              ->map($normaliseImg)
              ->values();

          // metti comunque la cover come prima thumb
          $cover = $product->cover_image_url ?? null;
          if ($cover) {
              $thumbs = collect([$cover])->concat($thumbs)->unique()->values();
          }
        @endphp

        @if($thumbs->isNotEmpty())
          <div class="mt-3 d-flex gap-2 flex-wrap">
            @foreach($thumbs as $img)
              <button type="button" class="p-0 border-0 bg-transparent thumb-btn" data-src="{{ $img }}">
                <div class="gallery-thumb overflow-hidden">
                  <img src="{{ $img }}" class="w-100 h-100 object-fit-cover" alt="Anteprima" loading="lazy">
                </div>
              </button>
            @endforeach
          </div>
        @endif
      </div>

      {{-- COLONNA DESTRA: INFO + ACQUISTO --}}
      <div class="col-lg-6">
        <div class="content-card">
          <div class="d-flex align-items-center gap-3 mb-3">
            <span class="status-pill">Disponibile ora</span>
            @if($product->price_compare && $product->price_compare > $product->price)
              <span class="badge-sale">-€ {{ number_format(max($product->price_compare - $product->price, 0), 2, ',', '.') }}</span>
            @endif
          </div>

          <h1 class="h3 fw-bold mb-2">{{ $product->name }}</h1>
          @if($product->brand)
            <div class="text-muted-soft mb-3">
              {{ $product->brand }} @if($product->color) • {{ $product->color }} @endif
            </div>
          @endif

          <div class="d-flex align-items-baseline gap-3 mb-4">
            <span class="fs-3 fw-semibold">€ {{ number_format($product->price, 2, ',', '.') }}</span>
            @if($product->price_compare)
              <span class="text-muted-soft text-decoration-line-through">
                € {{ number_format($product->price_compare, 2, ',', '.') }}
              </span>
            @endif
          </div>

          @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
          @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

          <form method="POST" action="{{ route('cart.add') }}" id="addToCartForm" class="form-elevated mt-3">
            @csrf

            <div class="mb-3">
              <label class="form-label fw-semibold">Taglia</label>
              <select name="variant_id" id="variantSelect" class="form-select" required>
                @foreach($product->variants as $v)
                  @php $pCents = $v->price_cents ?? (int) round($product->price * 100); @endphp
                  <option value="{{ $v->id }}"
                          data-stock="{{ $v->stock }}"
                          data-price="{{ $pCents }}"
                          @disabled($v->stock < 1)>
                    {{ $v->size }} — € {{ number_format($pCents/100, 2, ',', '.') }}
                    ({{ $v->stock > 0 ? "Disponibili: $v->stock" : "Esaurito" }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Quantità</label>
              <div class="input-group" style="max-width:220px">
                <button class="btn btn-outline-secondary" type="button" id="qtyMinus">−</button>
                <input type="number" name="qty" id="qtyInput" class="form-control text-center"
                       value="1" min="1" step="1" aria-label="Quantità">
                <button class="btn btn-outline-secondary" type="button" id="qtyPlus">+</button>
              </div>
              <div class="form-text" id="stockHint"></div>
            </div>

            <button class="btn btn-dark btn-pill w-100" type="submit" id="addBtn">Aggiungi al carrello</button>
          </form>

          @if(!empty($product->description))
            <div class="divider"></div>
            <div class="text-muted-soft">{!! $product->description !!}</div>
          @endif
        </div>
      </div>
    </div>

    {{-- Correlati --}}
    @if(!empty($related) && $related->isNotEmpty())
      <div class="divider"></div>
      <h2 class="h5 mb-3">Potrebbe piacerti</h2>
      <div class="row g-4">
        @foreach($related as $p)
          @include('partials.product-card', ['product' => $p])
        @endforeach
      </div>
    @endif
  </div>

  {{-- JS inline: NIENTE @push, così funziona anche se il layout non ha @stack --}}
  <script>
  (function(){
    // thumbs -> cambia immagine
    var btns = document.querySelectorAll('.thumb-btn');
    var main = document.getElementById('mainImage');
    btns.forEach(function(b){
      b.addEventListener('click', function(){
        var src = b.getAttribute('data-src');
        if (src && main) main.setAttribute('src', src);
      });
    });

    // qty +/− con limite stock della variante selezionata
    var select = document.getElementById('variantSelect');
    var qty    = document.getElementById('qtyInput');
    var minus  = document.getElementById('qtyMinus');
    var plus   = document.getElementById('qtyPlus');
    var hint   = document.getElementById('stockHint');
    var addBtn = document.getElementById('addBtn');

    function clampQty() {
      var opt = select.options[select.selectedIndex];
      var stock = parseInt(opt ? opt.getAttribute('data-stock') : '0', 10);
      var val = parseInt(qty.value || '1', 10);
      if (isNaN(val) || val < 1) val = 1;
      if (stock > 0 && val > stock) val = stock;
      qty.value = val;

      hint.textContent = stock > 0 ? ('Disponibili: ' + stock) : 'Esaurito';
      qty.setAttribute('max', stock > 0 ? String(stock) : '1');
      addBtn.disabled = stock < 1;
    }

    if (select && qty) {
      select.addEventListener('change', clampQty);
      qty.addEventListener('input', clampQty);
      if (minus) minus.addEventListener('click', function(){ qty.value = Math.max(1, (parseInt(qty.value||'1',10)-1)); clampQty(); });
      if (plus)  plus.addEventListener('click',  function(){ qty.value = (parseInt(qty.value||'1',10)+1); clampQty(); });
      clampQty(); // init
    }
  })();
  </script>
</x-layout>
