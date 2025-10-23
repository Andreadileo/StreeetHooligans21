<x-layout title="Nuovo prodotto">
  <section class="container-xxl py-4 py-lg-5">
    <h1 class="h4 mb-4">Crea un nuovo prodotto</h1>
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="d-grid gap-4">
      @csrf
      @include('admin.products._form', ['submitLabel' => 'Crea prodotto'])
    </form>
  </section>
</x-layout>
