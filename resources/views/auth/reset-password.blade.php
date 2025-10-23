<x-layout title="Reimposta password">
  <section class="container-xxl py-5" style="max-width:520px;">
    <div class="content-card">
      <h1 class="h4 mb-3 text-center">Imposta una nuova password</h1>
      <form method="POST" action="{{ route('password.store') }}" class="d-grid gap-3">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
          <label class="form-label fw-semibold" for="email">Email</label>
          <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
        </div>
        <div>
          <label class="form-label fw-semibold" for="password">Nuova password</label>
          <input id="password" class="form-control" type="password" name="password" required>
        </div>
        <div>
          <label class="form-label fw-semibold" for="password_confirmation">Conferma password</label>
          <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
        </div>
        <button class="btn btn-dark btn-pill" type="submit">Aggiorna password</button>
      </form>
    </div>
  </section>
</x-layout>
