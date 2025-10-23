<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request){
        $cart = $request->session()->get('cart', ['items'=>[], 'total_price_cents'=>0]);
        unset($cart['total_cents']); // legacy key
        $totalPriceCents = $cart['total_price_cents'] ?? $this->calcTotal($cart['items']);

        return view('cart.index', compact('cart', 'totalPriceCents'));
    }

    public function add(Request $request){
        $data = $request->validate([
            'variant_id' => ['required','integer','exists:product_variants,id'],
            'qty' => ['required','integer','min:1']
        ]);

        $variant = ProductVariant::with('product')->findOrFail($data['variant_id']);

        // Stock check
        if ($data['qty'] > $variant->stock) {
            return back()->withErrors(['qty' => 'Quantità superiore allo stock disponibile.']);
        }

        $cart = $request->session()->get('cart', ['items'=>[], 'total_price_cents'=>0]);
        unset($cart['total_cents']);

        $key = (string)$variant->id;
        $price = (int) ($variant->price_cents ?? $variant->product->price_cents);

        if(isset($cart['items'][$key])){
            $newQty = $cart['items'][$key]['qty'] + $data['qty'];
            if ($newQty > $variant->stock) {
                return back()->withErrors(['qty' => 'Stock insufficiente per aumentare la quantità.']);
            }
            $cart['items'][$key]['qty'] = $newQty;
        } else {
            $cart['items'][$key] = [
                'variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'name'       => $variant->product->name,
                'size'       => $variant->size,
                'color'      => $variant->color,
                'price_cents'=> $price,
                'qty'        => $data['qty'],
                'image'      => $variant->product->cover_image_url,
                'slug'       => $variant->product->slug,
            ];
        }

        $cart['total_price_cents'] = $this->calcTotal($cart['items']);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success','Aggiunto al carrello.');
    }

    public function update(Request $request, ProductVariant $variant){
        $data = $request->validate(['qty'=>['required','integer','min:1']]);

        if ($data['qty'] > $variant->stock) {
            return back()->withErrors(['qty' => 'Stock insufficiente.']);
        }

        $cart = $request->session()->get('cart', ['items'=>[], 'total_price_cents'=>0]);
        unset($cart['total_cents']);
        $key = (string)$variant->id;

        if(isset($cart['items'][$key])){
            $cart['items'][$key]['qty'] = $data['qty'];
            $cart['total_price_cents'] = $this->calcTotal($cart['items']);
            $request->session()->put('cart', $cart);
        }

        return back()->with('success','Quantità aggiornata.');
    }

    public function remove(Request $request, ProductVariant $variant){
        $cart = $request->session()->get('cart', ['items'=>[], 'total_price_cents'=>0]);
        unset($cart['total_cents']);
        $key = (string)$variant->id;

        if(isset($cart['items'][$key])){
            unset($cart['items'][$key]);
            $cart['total_price_cents'] = $this->calcTotal($cart['items']);
            $request->session()->put('cart', $cart);
        }

        return back()->with('success','Rimosso dal carrello.');
    }

    public function clear(Request $request){
        $request->session()->forget('cart');
        return back()->with('success','Carrello svuotato.');
    }

    private function calcTotal(array $items): int {
        $sum = 0;
        foreach($items as $it){
            $sum += $it['price_cents'] * $it['qty'];
        }
        return $sum;
    }
}
