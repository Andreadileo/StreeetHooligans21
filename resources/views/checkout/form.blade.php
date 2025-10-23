<x-app-layout>
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Checkout</h1>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        <div class="grid lg:grid-cols-2 gap-6">
            <!-- Riepilogo ordine -->
            <div>
                <h2 class="font-semibold mb-3">Riepilogo</h2>
                <ul class="divide-y">
                    @foreach($items as $row)
                        <li class="py-2 flex justify-between">
                            <span>{{ $row['product']->name }} × {{ $row['quantity'] }}</span>
                            <span>€ {{ number_format($row['line_total'], 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 space-y-1">
                    <div class="flex justify-between"><span>Subtotale</span><span>€ {{ number_format($subtotal,2,',','.') }}</span></div>
                    <div class="flex justify-between"><span>Spedizione</span><span>€ {{ number_format($shipping,2,',','.') }}</span></div>
                    <div class="flex justify-between"><span>IVA</span><span>€ {{ number_format($tax,2,',','.') }}</span></div>
                    <hr>
                    <div class="flex justify-between font-bold"><span>Totale</span><span>€ {{ number_format($total,2,',','.') }}</span></div>
                </div>
            </div>

            <!-- Dati cliente -->
            <div>
                <h2 class="font-semibold mb-3">Indirizzi</h2>
                <form action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-medium mb-1">Indirizzo di spedizione</label>
                        <textarea name="shipping_address" class="w-full border rounded p-2" required>{{ old('shipping_address') }}</textarea>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Indirizzo di fatturazione</label>
                        <textarea name="billing_address" class="w-full border rounded p-2" required>{{ old('billing_address') }}</textarea>
                    </div>
                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Conferma ordine
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
