<x-app-layout>
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Dashboard Admin</h1>
        <ul class="list-disc ms-6">
            <li><a href="{{ route('admin.orders.index') }}" class="text-blue-600 underline">Gestione Ordini</a></li>
            <li><a href="{{ route('admin.products.index') }}" class="text-blue-600 underline">Gestione Prodotti</a></li>
        </ul>
    </div>
</x-app-layout>
