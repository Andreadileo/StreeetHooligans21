<x-app-layout>
    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-xl font-bold mb-4">Ordini</h1>
        <table class="w-full border">
            <thead class="bg-gray-50">
            <tr><th class="p-2">#</th><th class="p-2">Utente</th><th class="p-2">Totale</th><th class="p-2">Stato</th><th></th></tr>
            </thead>
            <tbody>
            @foreach($orders as $o)
                <tr class="border-t">
                    <td class="p-2">{{ $o->number }}</td>
                    <td class="p-2">{{ $o->user?->email }}</td>
                    <td class="p-2">€ {{ number_format($o->total,2,',','.') }}</td>
                    <td class="p-2">{{ $o->status }}</td>
                    <td class="p-2">
                        <a href="{{ route('admin.orders.show',$o) }}" class="text-blue-600 underline">Vedi</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
