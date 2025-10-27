<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $q     = $request->string('q')->toString();
        $size  = $request->string('size')->toString();
        $brand = $request->string('brand')->toString();

        $products = Product::with('variants')
            ->when($q, fn($qq) => $qq->where(function($w) use ($q){
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('brand', 'like', "%{$q}%")
                  ->orWhere('color', 'like', "%{$q}%");
            }))
            ->when($brand, fn($qb) => $qb->where('brand', $brand))
            // mostra solo prodotti che hanno almeno una variante con stock > 0
            ->whereHas('variants', fn($v) => $v->when($size, fn($sv) => $sv->where('size', $size)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // taglie disponibili (per filtro)
        $allSizes = Product::query()
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.stock', '>', 0)
            ->distinct()
            ->orderBy('product_variants.size')
            ->pluck('product_variants.size');

        return view('catalog.index', compact('products', 'q', 'size', 'brand', 'allSizes'));
    }

    public function show(Product $product)
    {
        // assicuriamoci le varianti
        $product->load('variants');

        // prodotti correlati (stesso brand o categoria se ce l’hai)
        $related = Product::with('variants')
            ->where('id', '!=', $product->id)
            ->where('brand', $product->brand)
            ->latest()
            ->take(4)
            ->get();

        return view('catalog.show', compact('product', 'related'));
    }
}
