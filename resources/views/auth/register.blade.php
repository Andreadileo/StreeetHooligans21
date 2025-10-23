<x-layout title="Registrati">
  <section class="container-xxl py-5" style="max-width:640px;">
    <div class="content-card">
      <h1 class="h4 mb-4 text-center">Crea un nuovo account</h1>
      <form method="POST" action="{{ route('register') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
          <label class="form-label fw-semibold" for="name">Nome</label>
          <input id="name" class="form-control" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold" for="last_name">Cognome</label>
          <input id="last_name" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold" for="email">Email</label>
          <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold" for="password">Password</label>
          <input id="password" class="form-control" type="password" name="password" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold" for="password_confirmation">Conferma password</label>
          <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold" for="address">Indirizzo (opzionale)</label>
          <input id="address" class="form-control" name="address" value="{{ old('address') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold" for="birth_date">Data di nascita (opzionale)</label>
          <input id="birth_date" class="form-control" type="date" name="birth_date" value="{{ old('birth_date') }}">
        </div>
        <div class="col-12">
          <button class="btn btn-dark btn-pill w-100" type="submit">Registrati</button>
        </div>
      </form>
      <p class="text-muted-soft small text-center mt-3 mb-0">
        Hai già un account? <a href="{{ route('login') }}">Accedi</a>
      </p>
    </div>
  </section>
</x-layout>
