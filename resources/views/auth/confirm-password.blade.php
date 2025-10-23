<x-layout title="Conferma password">
  <section class="container-xxl py-5" style="max-width:480px;">
    <div class="content-card">
      <h1 class="h5 mb-3 text-center">Conferma la tua password</h1>
      <p class="text-muted-soft small text-center mb-4">
        Per continuare ti chiediamo di inserire nuovamente la password.
      </p>
      <form method="POST" action="{{ route('password.confirm') }}" class="d-grid gap-3">
        @csrf
        <div>
          <label class="form-label fw-semibold" for="password">Password</label>
          <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
        </div>
        <button class="btn btn-dark btn-pill" type="submit">Conferma</button>
      </form>
    </div>
  </section>
</x-layout>
