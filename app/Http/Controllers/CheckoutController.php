<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    private const SESSION_PAYMENT_INTENT = 'stripe.payment_intent_id';

    public function show(Request $request){
        $cart = $request->session()->get('cart', ['items'=>[], 'total_price_cents'=>0]);
        unset($cart['total_cents']);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->withErrors(['cart'=>'Carrello vuoto.']);
        }

        $totalPriceCents = collect($cart['items'])->sum(fn($it) => $it['price_cents'] * $it['qty']);

        $stripePublishableKey = config('services.stripe.key');
        $stripeSecretKey = config('services.stripe.secret');
        $paymentIntentClientSecret = null;
        $stripeError = null;

        if ($stripePublishableKey && $stripeSecretKey) {
            try {
                $stripe = new StripeClient($stripeSecretKey);
                $paymentIntent = $this->preparePaymentIntent($stripe, $request, $cart, $totalPriceCents);
                $paymentIntentClientSecret = $paymentIntent->client_secret;
                $request->session()->put(self::SESSION_PAYMENT_INTENT, $paymentIntent->id);
            } catch (ApiErrorException $exception) {
                report($exception);
                $stripeError = 'Impossibile inizializzare il pagamento Stripe in questo momento. Riprova più tardi.';
            }
        } else {
            $stripeError = 'Configura le chiavi Stripe nel file .env per abilitare i pagamenti.';
        }

        return view('checkout.show', [
            'cart' => $cart,
            'totalPriceCents' => $totalPriceCents,
            'stripePublishableKey' => $stripePublishableKey,
            'paymentIntentClientSecret' => $paymentIntentClientSecret,
            'stripeError' => $stripeError,
        ]);
    }

    public function process(Request $request){
        $cart = $this->getCart($request);
        if (empty($cart['items'])) {
            return $this->emptyCartResponse($request);
        }

        $totalPriceCents = collect($cart['items'])->sum(fn($item) => $item['price_cents'] * $item['qty']);

        $request->validate([
            'payment_intent_id' => 'required|string',
        ], [
            'payment_intent_id.required' => 'Pagamento non valido. Riprova.',
        ]);

        $stripeSecretKey = config('services.stripe.secret');
        if (!$stripeSecretKey) {
            return $this->stripeConfigMissingResponse($request);
        }

        $stripe = new StripeClient($stripeSecretKey);

        try {
            $order = $this->finalizeOrder($stripe, $request, $cart, $totalPriceCents, $request->string('payment_intent_id'));
        } catch (ValidationException $exception) {
            return $this->validationErrorResponse($request, $exception->errors());
        } catch (ApiErrorException $exception) {
            report($exception);
            return $this->validationErrorResponse($request, ['payment' => ['Impossibile verificare il pagamento Stripe.']]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('checkout.success'),
                'order_id' => $order->id,
            ]);
        }

        return redirect()->route('checkout.success')->with('success','Ordine completato.');
    }

    public function success(){
        return view('checkout.success');
    }

    public function cancel(Request $request)
    {
        $this->cancelOutstandingPaymentIntent($request);
        $request->session()->flash('success', 'Pagamento annullato. Il carrello è ancora disponibile per riprovare.');

        return redirect()->route('cart.index');
    }

    public function confirm(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');
        if (!$paymentIntentId) {
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Pagamento non completato.']);
        }

        $cart = $this->getCart($request);
        if (empty($cart['items'])) {
            return $this->emptyCartResponse($request);
        }

        $stripeSecretKey = config('services.stripe.secret');
        if (!$stripeSecretKey) {
            return $this->stripeConfigMissingResponse($request);
        }

        $totalPriceCents = collect($cart['items'])->sum(fn($item) => $item['price_cents'] * $item['qty']);

        $stripe = new StripeClient($stripeSecretKey);

        try {
            $order = $this->finalizeOrder($stripe, $request, $cart, $totalPriceCents, $paymentIntentId);
        } catch (ValidationException $exception) {
            return redirect()->route('checkout.show')->withErrors($exception->errors());
        } catch (ApiErrorException $exception) {
            report($exception);
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Impossibile verificare il pagamento Stripe.']);
        }

        return redirect()->route('checkout.success')->with('success','Ordine completato.');
    }

    private function preparePaymentIntent(StripeClient $stripe, Request $request, array $cart, int $totalPriceCents)
    {
        $existingIntentId = $request->session()->get(self::SESSION_PAYMENT_INTENT);

        if ($existingIntentId) {
            try {
                $existingIntent = $stripe->paymentIntents->retrieve($existingIntentId);
                if (in_array($existingIntent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action'], true)) {
                    if ((int) $existingIntent->amount !== $totalPriceCents) {
                        $stripe->paymentIntents->update($existingIntentId, [
                            'amount' => $totalPriceCents,
                            'metadata' => $this->paymentMetadata($request, $cart),
                        ]);
                        $existingIntent = $stripe->paymentIntents->retrieve($existingIntentId);
                    }

                    return $existingIntent;
                }
            } catch (ApiErrorException $exception) {
                report($exception);
            }

            $request->session()->forget(self::SESSION_PAYMENT_INTENT);
        }

        return $stripe->paymentIntents->create([
            'amount' => $totalPriceCents,
            'currency' => 'eur',
            'description' => 'StreetHooligans order',
            'payment_method_types' => ['card'],
            'metadata' => $this->paymentMetadata($request, $cart),
        ]);
    }

    private function paymentMetadata(Request $request, array $cart): array
    {
        return [
            'cart_items' => collect($cart['items'])->sum('qty'),
            'cart_hash' => sha1(json_encode($cart['items'])),
            'user_id' => optional($request->user())->id,
        ];
    }

    /**
     * @throws ApiErrorException
     * @throws ValidationException
     */
    private function finalizeOrder(StripeClient $stripe, Request $request, array $cart, int $totalPriceCents, string $paymentIntentId): Order
    {
        $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

        if ($paymentIntent->status !== 'succeeded') {
            throw ValidationException::withMessages([
                'payment' => ['Il pagamento non è stato completato correttamente.'],
            ]);
        }

        if ((int) $paymentIntent->amount_received !== $totalPriceCents) {
            $this->refundPayment($stripe, $paymentIntentId, 'Importo incassato diverso dal totale carrello.');

            throw ValidationException::withMessages([
                'payment' => ['Importo del pagamento non coerente con il carrello. È stato richiesto un rimborso automatico.'],
            ]);
        }

        if ($existingOrder = Order::where('payment_ref', $paymentIntent->id)->first()) {
            $request->session()->forget('cart');
            $request->session()->forget(self::SESSION_PAYMENT_INTENT);

            return $existingOrder;
        }

        return DB::transaction(function () use ($request, $cart, $totalPriceCents, $paymentIntent, $stripe) {
            $variants = [];

            foreach ($cart['items'] as $item) {
                $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                if (!$variant || $variant->stock < $item['qty']) {
                    $this->refundPayment($stripe, $paymentIntent->id, "Stock insufficiente per {$item['name']} ({$item['size']}).");

                    throw ValidationException::withMessages([
                        'stock' => ["Stock insufficiente per {$item['name']} ({$item['size']}). È stato richiesto un rimborso automatico."],
                    ]);
                }

                $variants[$item['variant_id']] = $variant;
            }

            $order = Order::create([
                'user_id' => optional($request->user())->id,
                'total_cents' => $totalPriceCents,
                'status' => 'paid',
                'payment_ref' => $paymentIntent->id,
            ]);

            foreach ($cart['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'name' => $item['name'],
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                    'price_cents' => $item['price_cents'],
                    'qty' => $item['qty'],
                ]);

                $variants[$item['variant_id']]->decrement('stock', $item['qty']);
            }

            $request->session()->forget('cart');
            $request->session()->forget(self::SESSION_PAYMENT_INTENT);

            return $order;
        });
    }

    private function refundPayment(StripeClient $stripe, string $paymentIntentId, string $reason): void
    {
        try {
            $stripe->refunds->create([
                'payment_intent' => $paymentIntentId,
                'reason' => 'requested_by_customer',
                'metadata' => ['reason' => $reason],
            ]);
        } catch (ApiErrorException $exception) {
            report($exception);
        }
    }

    private function cancelOutstandingPaymentIntent(Request $request): void
    {
        $paymentIntentId = $request->session()->pull(self::SESSION_PAYMENT_INTENT);
        $stripeSecretKey = config('services.stripe.secret');

        if (!$paymentIntentId || !$stripeSecretKey) {
            return;
        }

        try {
            (new StripeClient($stripeSecretKey))
                ->paymentIntents
                ->cancel($paymentIntentId);
        } catch (ApiErrorException $exception) {
            report($exception);
        }
    }

    private function getCart(Request $request): array
    {
        $cart = $request->session()->get('cart', ['items' => [], 'total_price_cents' => 0]);
        unset($cart['total_cents']);

        return $cart;
    }

    private function emptyCartResponse(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['errors' => ['cart' => ['Carrello vuoto.']]], 422);
        }

        return redirect()->route('cart.index')->withErrors(['cart'=>'Carrello vuoto.']);
    }

    private function stripeConfigMissingResponse(Request $request)
    {
        $message = ['payment' => ['Stripe non è configurato. Aggiungi STRIPE_KEY e STRIPE_SECRET nel file .env.']];

        if ($request->wantsJson()) {
            return response()->json(['errors' => $message], 422);
        }

        return redirect()->route('checkout.show')->withErrors($message);
    }

    private function validationErrorResponse(Request $request, array $errors)
    {
        if ($request->wantsJson()) {
            return response()->json(['errors' => $errors], 422);
        }

        return back()->withErrors($errors);
    }
}
