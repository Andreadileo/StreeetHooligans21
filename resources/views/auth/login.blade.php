<x-layout title="Accedi">
  <section class="container-xxl py-5" style="max-width:540px;">
    <div class="content-card">
      <h1 class="h4 mb-4 text-center">Accedi al tuo account</h1>
      <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
        @csrf
        <div>
          <label class="form-label fw-semibold" for="email">Email</label>
          <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div>
          <label class="form-label fw-semibold" for="password">Password</label>
          <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Ricordami</label>
          </div>
          <a class="text-muted-soft small" href="{{ route('password.request') }}">Password dimenticata?</a>
        </div>
        <button class="btn btn-dark btn-pill" type="submit">Accedi</button>
        <p class="text-muted-soft small text-center m-0">
          Non hai un account? <a href="{{ route('register') }}">Registrati</a>
        </p>
      </form>
    </div>
  </section>
</x-layout>
