@php
  use Illuminate\Support\Str;

  $old = fn(string $key, $default = null) => old($key, $default);
@endphp

<div class="content-card">
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label fw-semibold" for="name">Nome prodotto</label>
      <input id="name" name="name" value="{{ $old('name', $product->name) }}" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold" for="brand">Brand</label>
      <input id="brand" name="brand" value="{{ $old('brand', $product->brand) }}" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold" for="color">Colore</label>
      <input id="color" name="color" value="{{ $old('color', $product->color) }}" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold" for="price">Prezzo (€)</label>
      <input id="price" name="price" value="{{ $old('price', $product->price) }}" class="form-control" type="number" step="0.01" min="0" required>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold" for="price_compare">Prezzo barrato (€)</label>
      <input id="price_compare" name="price_compare" value="{{ $old('price_compare', $product->price_compare) }}" class="form-control" type="number" step="0.01" min="0">
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold" for="image">Immagine principale</label>
      <input id="image" name="image" type="file" class="form-control" accept="image/*">
      @if($product->image || $product->image_url)
        <div class="mt-2">
          <span class="text-muted-soft small d-block mb-1">Immagine attuale:</span>
          <img src="{{ $product->cover_image_url }}" alt="Anteprima" class="rounded" style="max-width:180px; height:auto;">
        </div>
      @endif
      <small class="text-muted-soft d-block">Se preferisci usare un link esterno, incollalo qui sotto:</small>
      <input name="image_url" value="{{ $old('image_url', $product->image_url) }}" class="form-control mt-2" placeholder="https://...">
    </div>
    <div class="col-12">
      <label class="form-label fw-semibold" for="description">Descrizione</label>
      <textarea id="description" name="description" class="form-control" rows="4">{{ $old('description', $product->description) }}</textarea>
    </div>
    <div class="col-12">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked($old('is_active', $product->is_active))>
        <label class="form-check-label" for="is_active">Visibile nel catalogo</label>
      </div>
    </div>
  </div>
</div>

<div class="content-card mt-4">
  <h2 class="h5 mb-3">Varianti (taglie)</h2>
  <p class="text-muted-soft small">Spunta le taglie che vuoi offrire e inserisci stock e prezzo specifico (lascia vuoto il prezzo per usare quello base).</p>
  <div class="row fw-semibold text-muted-soft border-bottom pb-2 mb-2">
    <div class="col-2">Taglia</div>
    <div class="col-5 col-sm-4 col-md-3">Stock</div>
    <div class="col-5 col-sm-4 col-md-3">Prezzo personalizzato</div>
  </div>

  @foreach($sizes as $size)
    @php
      $variant = $product->variants->firstWhere('size', $size) ?? null;
      $enabled = (bool) $old("variants.$size.enabled", $variant !== null);
      $stock = $old("variants.$size.stock", $variant->stock ?? 0);
      $priceFromVariant = $variant && $variant->price_cents !== null
        ? number_format($variant->price_cents / 100, 2, '.', '')
        : null;
      $price = $old("variants.$size.price", $priceFromVariant);
    @endphp
    <div class="row g-2 align-items-center mb-3">
      <div class="col-12 col-sm-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="variants[{{ $size }}][enabled]" id="variant-{{ $size }}" value="1" @checked($enabled)>
          <label class="form-check-label fw-semibold" for="variant-{{ $size }}">{{ $size }}</label>
        </div>
      </div>
      <div class="col-6 col-sm-5 col-md-3">
        <input class="form-control" type="number" min="0" name="variants[{{ $size }}][stock]" value="{{ $stock }}">
      </div>
      <div class="col-6 col-sm-5 col-md-3">
        <input class="form-control" type="number" step="0.01" min="0" name="variants[{{ $size }}][price]" value="{{ $price }}">
      </div>
    </div>
  @endforeach
</div>

<div class="content-card mt-4">
  <h2 class="h5 mb-3">Galleria immagini</h2>
  <p class="text-muted-soft small">Carica più immagini dal tuo computer oppure incolla link esterni (uno per riga).</p>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label fw-semibold" for="gallery">Carica file</label>
      <input id="gallery" name="gallery[]" type="file" class="form-control" accept="image/*" multiple>
    </div>
    <div class="col-md-6">
      <label class="form-label fw-semibold" for="gallery_urls">URL esterni (uno per riga)</label>
      <textarea id="gallery_urls" name="gallery_urls" class="form-control" rows="4" placeholder="https://...">{{ old('gallery_urls') }}</textarea>
    </div>
  </div>

  @if($product->exists && !empty($product->images))
    <div class="mt-4">
      <h3 class="h6 mb-3">Immagini già caricate</h3>
      <div class="row g-3">
        @foreach($product->images as $img)
          <div class="col-6 col-md-3 text-center">
            <div class="ratio ratio-1x1 bg-light rounded overflow-hidden mb-2">
              <img src="{{ Str::startsWith($img, ['http://', 'https://']) ? $img : asset('storage/'.$img) }}" alt="Immagine" class="w-100 h-100 object-fit-cover">
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remove_gallery[]" value="{{ $img }}" id="remove-gallery-{{ md5($img) }}">
              <label class="form-check-label small" for="remove-gallery-{{ md5($img) }}">Rimuovi</label>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div>

<div class="d-flex justify-content-end gap-3 mt-4">
  <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-pill">Annulla</a>
  <button class="btn btn-dark btn-pill" type="submit">{{ $submitLabel ?? 'Salva' }}</button>
</div>
