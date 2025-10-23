<x-layout title="Password dimenticata">
  <section class="container-xxl py-5" style="max-width:520px;">
    <div class="content-card">
      <h1 class="h4 mb-3 text-center">Recupera password</h1>
      <p class="text-muted-soft small text-center mb-4">
        Inserisci la tua email, riceverai il link per reimpostare la password.
      </p>
      <form method="POST" action="{{ route('password.email') }}" class="d-grid gap-3">
        @csrf
        <div>
          <label class="form-label fw-semibold" for="email">Email</label>
          <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <button class="btn btn-dark btn-pill" type="submit">Invia link di reset</button>
      </form>
      <p class="text-muted-soft small text-center mt-3 mb-0">
        <a href="{{ route('login') }}">Torna al login</a>
      </p>
    </div>
  </section>
</x-layout>
