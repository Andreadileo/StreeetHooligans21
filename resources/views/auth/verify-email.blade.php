<x-layout title="Verifica email">
  <section class="container-xxl py-5" style="max-width:520px;">
    <div class="content-card text-center">
      <h1 class="h4 mb-3">Verifica il tuo indirizzo email</h1>
      <p class="text-muted-soft mb-4">
        Ti abbiamo inviato un link di verifica. Se non lo trovi, richiedi un nuovo invio.
      </p>
      @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success mb-4">Nuovo link inviato alla tua email.</div>
      @endif
      <form method="POST" action="{{ route('verification.send') }}" class="d-grid gap-3">
        @csrf
        <button class="btn btn-dark btn-pill" type="submit">Invia nuovamente</button>
      </form>
      <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="btn btn-outline-secondary btn-pill" type="submit">Esci</button>
      </form>
    </div>
  </section>
</x-layout>
