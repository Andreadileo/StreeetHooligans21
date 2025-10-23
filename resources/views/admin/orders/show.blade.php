<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-xl font-bold mb-4">Ordine {{ $order->number }}</h1>
        <p>Utente: {{ $order->user?->email }}</p>
        <p>Totale: € {{ number_format($order->total,2,',','.') }}</p>
        <p>Stato attuale: {{ $order->status }}</p>

        <form method="POST" action="{{ route('admin.orders.update',$order) }}" class="mt-4">
            @csrf @method('PUT')
            <select name="status" class="border rounded p-2">
                <option value="pending" @selected($order->status=='pending')>In attesa</option>
                <option value="paid" @selected($order->status=='paid')>Pagato</option>
                <option value="shipped" @selected($order->status=='shipped')>Spedito</option>
                <option value="cancelled" @selected($order->status=='cancelled')>Annullato</option>
            </select>
            <button class="bg-blue-600 text-white px-3 py-1 rounded">Aggiorna</button>
        </form>

        <h2 class="mt-6 font-semibold">Articoli</h2>
        <ul class="list-disc ms-6">
            @foreach($order->items as $i)
                <li>{{ $i->name }} × {{ $i->quantity }} ({{ $i->sku }}) – € {{ number_format($i->line_total,2,',','.') }}</li>
            @endforeach
        </ul>
    </div>
</x-app-layout>
