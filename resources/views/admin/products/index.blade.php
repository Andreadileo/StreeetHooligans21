<x-layout title="Gestione prodotti">
  <section class="container-xxl py-4 py-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h4 mb-1">Prodotti</h1>
        <p class="text-muted-soft mb-0">Da qui puoi creare, modificare o rimuovere gli articoli del negozio.</p>
      </div>
      <a class="btn btn-dark btn-pill" href="{{ route('admin.products.create') }}">Nuovo prodotto</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="content-card">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Prezzo</th>
              <th>Attivo</th>
              <th>Varianti</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($products as $product)
              <tr>
                <td>
                  <div class="fw-semibold">{{ $product->name }}</div>
                  <div class="text-muted-soft small">{{ $product->brand ?: '—' }}</div>
                </td>
                <td>€ {{ number_format($product->price, 2, ',', '.') }}</td>
                <td>
                  @if($product->is_active)
                    <span class="badge text-bg-success">Attivo</span>
                  @else
                    <span class="badge text-bg-secondary">Nascosto</span>
                  @endif
                </td>
                <td>{{ $product->variants_count }}</td>
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-2">
                    <a class="btn btn-outline-secondary btn-sm btn-pill" href="{{ route('admin.products.edit', $product) }}">Modifica</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Eliminare questo prodotto?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm btn-pill">Elimina</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted-soft py-4">Ancora nessun prodotto. Creane uno con il pulsante in alto.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $products->links() }}
      </div>
    </div>
  </section>
</x-layout>
