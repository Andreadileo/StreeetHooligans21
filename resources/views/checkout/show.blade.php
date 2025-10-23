<x-layout title="Checkout">
  <section class="container-xxl py-4 py-lg-5">
    <div class="row g-4 align-items-start">
      <div class="col-lg-7">
        <div class="content-card h-100">
          <span class="feature-chip mb-3">Riepilogo ordine</span>
          <h1 class="h4 mb-3">Pagamento simulato per la demo</h1>
          <p class="text-muted-soft mb-4">
            Confermando l’ordine verrà generata una entry nella tabella <code>orders</code>,
            verrà scalato lo stock delle varianti e il carrello verrà svuotato.
          </p>

          <div class="stacked-card bg-white border-0 p-0">
            <ul class="list-group list-group-flush">
              @foreach($cart['items'] as $it)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fw-semibold">{{ $it['name'] }}</div>
                    <div class="text-muted-soft small">
                      {{ $it['size'] }} {{ $it['color'] ? ' / '.$it['color'] : '' }} × {{ $it['qty'] }}
                    </div>
                  </div>
                  <strong>€ {{ number_format(($it['price_cents']*$it['qty'])/100, 2, ',', '.') }}</strong>
                </li>
              @endforeach
              <li class="list-group-item d-flex justify-content-between">
                <span class="fw-semibold">Totale</span>
                <strong>€ {{ number_format(($totalPriceCents ?? 0)/100, 2, ',', '.') }}</strong>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="content-card">
          <h2 class="h5 mb-3">Conferma e completa</h2>
          <p class="text-muted-soft mb-4">
            È una simulazione: puoi integrare gateway reali (Stripe, PayPal, ecc.) collegando
            questo step alla tua logica di pagamento.
          </p>

          <form method="POST" action="{{ route('checkout.process') }}" class="d-grid gap-3">
            @csrf
            <button class="btn btn-dark btn-pill btn-lg" type="submit">Conferma ordine</button>
            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-pill">Torna al carrello</a>
          </form>
        </div>
      </div>
    </div>
  </section>
</x-layout>
