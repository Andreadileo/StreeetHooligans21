<x-layout :title="'Modifica ' . $product->name">
  <section class="container-xxl py-4 py-lg-5">
    <h1 class="h4 mb-4">Modifica prodotto</h1>
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="d-grid gap-4">
      @csrf
      @method('PUT')
      @include('admin.products._form', ['submitLabel' => 'Salva modifiche'])
    </form>
  </section>
</x-layout>
