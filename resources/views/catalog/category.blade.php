@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Categoria: {{ $category->name }}</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($products as $product)
            <div class="border rounded-lg p-4 shadow">
                <h2 class="text-lg font-semibold mb-2">{{ $product->name }}</h2>
                <p class="text-gray-600 text-sm mb-2">{{ $product->short_description }}</p>
                <p class="text-xl font-bold mb-2">€ {{ number_format($product->price, 2, ',', '.') }}</p>
                <a href="{{ route('catalog.show', $product->slug) }}"
                   class="inline-block mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Dettagli
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
@endsection
