@php
  $productUrl = $product->product_url;
  $firstVariant = $product->variants->firstWhere('stock', '>', 0);
  $coverImage = $product->cover_image_url;
  $ratioVariant = $product->getImageRatioVariant();
  $mediaModifier = $ratioVariant !== 'standard' ? ' product-card__media--' . $ratioVariant : '';

  $price = $product->price;
  $compare = $product->price_compare ?? null;
  $onSale = $compare && $compare > $price;
  $save = $onSale ? ($compare - $price) : 0;
@endphp

<div class="col-12 col-sm-6 col-lg-4 col-xl-3">
  <article class="product-card">
    <a href="{{ $productUrl }}" class="product-card__media d-block{{ $mediaModifier }}">
      <img
        src="{{ $coverImage }}"
        alt="{{ $product->name }}"
        loading="lazy"
        width="960" height="1200"
        sizes="(max-width: 576px) 100vw, (max-width: 992px) 50vw, 25vw"
      >
    </a>

    <div class="product-card__body">
      <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
        <a href="{{ $productUrl }}" class="text-decoration-none text-dark">
          <h3 class="h6 mb-0">{{ $product->name }}</h3>
        </a>
        @if($onSale)
          <span class="badge-sale">-€ {{ number_format($save, 2, ',', '.') }}</span>
        @endif
      </div>

      @if(!empty($product->brand) || !empty($product->color))
        <div class="product-card__meta mb-2">
          {{ $product->brand ?? 'StreetHooligans' }}
          @if(!empty($product->color))
            • {{ $product->color }}
          @endif
        </div>
      @endif

      <p class="text-muted-soft small mb-3">
        {{ str($product->description)->stripTags()->squish()->limit(110) }}
      </p>

      <div class="product-card__footer">
        <div>
          <div class="fw-semibold">€ {{ number_format($price, 2, ',', '.') }}</div>
          @if($onSale)
            <div class="text-decoration-line-through small text-muted-soft">
              € {{ number_format($compare, 2, ',', '.') }}
            </div>
          @endif
        </div>

        @if($firstVariant)
          <button type="submit" form="add-{{ $product->id }}" class="btn btn-dark btn-sm btn-pill">
            Acquista
          </button>
        @else
          <a class="btn btn-outline-dark btn-sm btn-pill" href="{{ $productUrl }}">Scegli</a>
        @endif
      </div>

      @if($firstVariant)
        <div class="small text-muted mt-2">Disponibili: {{ $firstVariant->stock }}</div>
      @else
        <div class="small text-danger mt-2">Non disponibile</div>
      @endif
    </div>

    @if($firstVariant)
      <form id="add-{{ $product->id }}" method="POST" action="{{ route('cart.add') }}" class="d-none">
        @csrf
        <input type="hidden" name="variant_id" value="{{ $firstVariant->id }}">
        <input type="hidden" name="qty" value="1">
      </form>
    @endif
  </article>
</div>
