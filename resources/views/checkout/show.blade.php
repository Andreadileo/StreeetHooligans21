<x-layout title="Checkout">
  <section class="container-xxl py-4 py-lg-5">
    <div class="row g-4 align-items-start">
      <div class="col-lg-7">
        <div class="content-card h-100">
          <span class="feature-chip mb-3">Riepilogo ordine</span>
          <h1 class="h4 mb-3">Completa il pagamento con Stripe</h1>
          <p class="text-muted-soft mb-4">
            Il totale viene addebitato tramite Stripe in modalità live o test in base alle chiavi configurate.
            L’ordine viene salvato nel database soltanto a pagamento riuscito.
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
            Inserisci il metodo di pagamento e conferma. Al termine verrai reindirizzato alla pagina di successo.
            In caso di autenticazione 3D Secure, segui le istruzioni visualizzate.
          </p>

          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $message)
                  <li>{{ $message }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if($stripeError)
            <div class="alert alert-warning">{{ $stripeError }}</div>
          @endif

          <form method="POST"
                action="{{ route('checkout.process') }}"
                id="payment-form"
                class="d-grid gap-3"
                data-stripe-key="{{ $stripePublishableKey }}"
                data-client-secret="{{ $paymentIntentClientSecret }}"
                data-return-url="{{ route('checkout.confirm') }}">
            @csrf

            <div id="payment-element" class="bg-white border rounded p-3" style="display: {{ $paymentIntentClientSecret ? 'block' : 'none' }};"></div>

            <div id="payment-messages" class="small text-danger" role="status"></div>

            <button class="btn btn-dark btn-pill btn-lg"
                    type="submit"
                    @if(!$paymentIntentClientSecret) disabled @endif>
              Paga con Stripe
            </button>
            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-pill">Torna al carrello</a>
          </form>
        </div>
      </div>
    </div>
  </section>
</x-layout>

@if($paymentIntentClientSecret)
  @push('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('payment-form');
        if (!form) {
          return;
        }

        const stripeKey = form.dataset.stripeKey;
        const clientSecret = form.dataset.clientSecret;
        const returnUrl = form.dataset.returnUrl;
        if (!stripeKey || !clientSecret || !returnUrl) {
          return;
        }

        const stripe = Stripe(stripeKey);
        const elements = stripe.elements({clientSecret});
        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const submitButton = form.querySelector('button[type="submit"]');
        const messages = document.getElementById('payment-messages');

        const setLoading = (isLoading) => {
          if (!submitButton) {
            return;
          }

          submitButton.disabled = isLoading;
          submitButton.textContent = isLoading ? 'Elaborazione…' : 'Paga con Stripe';
        };

        form.addEventListener('submit', async (event) => {
          event.preventDefault();
          if (!submitButton) {
            return;
          }

          setLoading(true);
          messages.textContent = '';

          const {error, paymentIntent} = await stripe.confirmPayment({
            elements,
            confirmParams: {
              return_url: returnUrl,
            },
          });

          if (error) {
            messages.textContent = error.message || 'Si è verificato un errore durante la conferma del pagamento.';
            setLoading(false);
            return;
          }

          if (paymentIntent && paymentIntent.status === 'succeeded') {
            window.location.href = `${returnUrl}?payment_intent=${encodeURIComponent(paymentIntent.id)}&redirect_status=succeeded`;
            return;
          }

          messages.textContent = 'Pagamento in attesa di completamento. Se non vieni reindirizzato automaticamente, aggiorna la pagina.';
          setLoading(false);
        });
      });
    </script>
  @endpush
@endif
