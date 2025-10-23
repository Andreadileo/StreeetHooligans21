@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    @if(empty($cart) || count($cart) === 0)
        <p class="text-gray-600">Il carrello è vuoto. <a href="{{ route('catalog.index') }}" class="text-blue-500">Torna al catalogo</a></p>
    @else
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Riepilogo ordine</h2>

            <ul class="mb-4">
                @foreach($cart as $item)
                    <li class="flex justify-between border-b py-2">
                        <span>{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                        <span>€ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            <h3 class="text-lg font-bold mb-6">Totale: € {{ number_format($total, 2, ',', '.') }}</h3>

            {{-- Qui un form fittizio per i dati cliente --}}
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block">Nome completo</label>
                    <input type="text" id="name" name="name" class="w-full border rounded px-3 py-2" required>
                </div>

                <div class="mb-4">
                    <label for="address" class="block">Indirizzo</label>
                    <input type="text" id="address" name="address" class="w-full border rounded px-3 py-2" required>
                </div>

                <div class="mb-4">
                    <label for="payment" class="block">Metodo di pagamento</label>
                    <select id="payment" name="payment" class="w-full border rounded px-3 py-2">
                        <option value="card">Carta di credito</option>
                        <option value="paypal">PayPal</option>
                        <option value="cod">Contrassegno</option>
                    </select>
                </div>

                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Conferma ordine
                </button>
            </form>

        </div>
    @endif
</div>
@endsection
