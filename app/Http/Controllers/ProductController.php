<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->latest('id')->paginate(12);
        return view('products.index', compact('products'));
    }

    public function show(Product $product) // arriva già risolto via slug
    {
        return view('products.show', compact('product'));
    }
}
