<x-layout title="Carrello">
  <section class="container-xxl py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
      <div>
        <span class="feature-chip mb-2">Il tuo carrello</span>
        <h1 class="h4 mb-0">Rivedi i capi selezionati prima del checkout</h1>
      </div>
      <div class="text-muted-soft small">
        Totale articoli: {{ collect($cart['items'] ?? [])->sum('qty') }}
      </div>
    </div>

    @if(session('success')) <div class="alert alert-success mb-4">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger mb-4">{{ $errors->first() }}</div> @endif

    @if(empty($cart['items']))
      <div class="content-card text-center">
        <h2 class="h5 mb-2">Il carrello è ancora vuoto</h2>
        <p class="text-muted-soft mb-4">
          Aggiungi uno o più prodotti dal catalogo e torneranno tutti qui. Le taglie disponibili vengono aggiornate in tempo reale.
        </p>
        <a href="{{ route('catalog.index') }}" class="btn btn-dark btn-pill">Vai al catalogo</a>
      </div>
    @else
      <div class="row g-4">
        <div class="col-lg-8">
          <div class="table-responsive cart-table">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th>Prodotto</th>
                  <th>Taglia</th>
                  <th>Prezzo</th>
                  <th>Qty</th>
                  <th>Subtotale</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($cart['items'] as $it)
                  <tr>
                    <td>
                      <div class="fw-semibold">
                        <a class="text-decoration-none text-dark" href="{{ route('product.show', $it['slug']) }}">
                          {{ $it['name'] }}
                        </a>
                      </div>
                      @if(!empty($it['color']))
                        <div class="text-muted-soft small">{{ $it['color'] }}</div>
                      @endif
                    </td>
                    <td>{{ $it['size'] }}</td>
                    <td>€ {{ number_format($it['price_cents']/100, 2, ',', '.') }}</td>
                    <td style="width:140px;">
                      <form method="POST" action="{{ route('cart.update', $it['variant_id']) }}" class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="qty" min="1" value="{{ $it['qty'] }}" class="form-control text-center">
                        <button class="btn btn-outline-secondary btn-sm btn-pill">↻</button>
                      </form>
                    </td>
                    <td>€ {{ number_format(($it['price_cents']*$it['qty'])/100, 2, ',', '.') }}</td>
                    <td>
                      <form method="POST" action="{{ route('cart.remove', $it['variant_id']) }}" class="table-actions">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm btn-pill">Rimuovi</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="stacked-card h-100">
            <h2 class="h5 mb-3">Riepilogo ordine</h2>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted-soft">Subtotale</span>
            <span>€ {{ number_format(($totalPriceCents ?? 0)/100, 2, ',', '.') }}</span>
          </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted-soft">Spedizione</span>
              <span>Inclusa</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
              <span class="text-muted-soft">Tasse</span>
              <span>Calcolate al checkout</span>
            </div>

          <div class="fw-bold fs-5 d-flex justify-content-between mb-4">
            <span>Totale</span>
            <span>€ {{ number_format(($totalPriceCents ?? 0)/100, 2, ',', '.') }}</span>
          </div>
            <a class="btn btn-dark btn-pill w-100 mb-3" href="{{ route('checkout.show') }}">Procedi al checkout</a>

            <form method="POST" action="{{ route('cart.clear') }}">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline-secondary btn-pill w-100">Svuota carrello</button>
            </form>
          </div>
        </div>
      </div>
    @endif
  </section>
</x-layout>
