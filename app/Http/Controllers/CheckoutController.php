<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show(Request $request){
        $cart = $request->session()->get('cart', ['items'=>[], 'total_price_cents'=>0]);
        unset($cart['total_cents']);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->withErrors(['cart'=>'Carrello vuoto.']);
        }

        $totalPriceCents = collect($cart['items'])->sum(fn($it) => $it['price_cents'] * $it['qty']);

        return view('checkout.show', compact('cart', 'totalPriceCents'));
    }

    public function process(Request $request){
        $cart = $request->session()->get('cart', ['items'=>[], 'total_price_cents'=>0]);
        unset($cart['total_cents']);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->withErrors(['cart'=>'Carrello vuoto.']);
        }

        $totalPriceCents = collect($cart['items'])->sum(fn($item) => $item['price_cents'] * $item['qty']);

        // Simula pagamento (ok)
        return DB::transaction(function() use ($request, $cart, $totalPriceCents) {

            // Validazione stock **al momento del checkout**
            foreach($cart['items'] as $it){
                $variant = ProductVariant::lockForUpdate()->find($it['variant_id']);
                if (!$variant || $variant->stock < $it['qty']) {
                    return back()->withErrors(['stock'=>"Stock insufficiente per {$it['name']} ({$it['size']})."]);
                }
            }

            $order = Order::create([
                'user_id'     => optional($request->user())->id,
                'total_cents' => $totalPriceCents,
                'status'      => 'paid',
                'payment_ref' => 'TEST-' . strtoupper(uniqid()),
            ]);

            foreach($cart['items'] as $it){
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $it['product_id'],
                    'product_variant_id' => $it['variant_id'],
                    'name'               => $it['name'],
                    'size'               => $it['size'] ?? null,
                    'color'              => $it['color'] ?? null,
                    'price_cents'        => $it['price_cents'],
                    'qty'                => $it['qty'],
                ]);

                // scala stock
                $variant = ProductVariant::lockForUpdate()->find($it['variant_id']);
                $variant->decrement('stock', $it['qty']);
            }

            // svuota carrello
            $request->session()->forget('cart');

            return redirect()->route('checkout.success')->with('success','Ordine completato.');
        });
    }

    public function success(){
        return view('checkout.success');
    }

    public function cancel(Request $request)
    {
        $request->session()->flash('success', 'Pagamento annullato. Il carrello è ancora disponibile per riprovare.');

        return redirect()->route('cart.index');
    }
}
