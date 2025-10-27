<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with('variants')
            ->active()
            ->latest()
            ->take(12)
            ->get()
            ->shuffle()
            ->take(4);

        return view('home', compact('products'));
    }
}
