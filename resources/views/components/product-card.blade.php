@props(['product'])

@php
  $firstVariant = $product->variants->firstWhere('stock', '>', 0);
  $ratioVariant = $product->getImageRatioVariant();
  $mediaModifier = $ratioVariant !== 'standard' ? ' product-card__media--' . $ratioVariant : '';
@endphp

<div class="col-12 col-sm-6 col-lg-4 col-xl-3">
  <article class="product-card">
    <a href="{{ $product->product_url }}" class="product-card__media d-block{{ $mediaModifier }}">
      <img src="{{ $product->cover_image_url }}"
           alt="Foto di {{ $product->name }}"
           loading="lazy"
           width="960" height="1200">
    </a>

    <div class="product-card__body">
      <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
        <a href="{{ $product->product_url }}" class="text-decoration-none text-dark">
          <h3 class="h6 mb-0">{{ $product->name }}</h3>
        </a>
        @if($product->price_compare)
          <span class="badge-sale">
            -€ {{ number_format(max($product->price_compare - $product->price, 0), 2, ',', '.') }}
          </span>
        @endif
      </div>

      <p class="text-muted-soft small mb-3">
        {{ str($product->description)->stripTags()->squish()->limit(110) }}
      </p>

      <div class="product-card__footer">
        <div>
          <div class="fw-semibold">€ {{ number_format($product->price, 2, ',', '.') }}</div>
          @if($product->price_compare)
            <div class="text-decoration-line-through small text-muted-soft">
              € {{ number_format($product->price_compare, 2, ',', '.') }}
            </div>
          @endif
        </div>
        @if($firstVariant)
          <button type="submit" form="add-{{ $product->id }}" class="btn btn-dark btn-sm btn-pill">Acquista</button>
        @else
          <a class="btn btn-outline-dark btn-sm btn-pill" href="{{ $product->product_url }}">Scegli</a>
        @endif
      </div>
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
