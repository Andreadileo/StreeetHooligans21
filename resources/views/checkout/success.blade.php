<x-layout title="Ordine completato">
  <section class="container-xxl py-5 text-center">
    <div class="content-card mx-auto" style="max-width: 600px;">
      <div class="display-6 mb-3 text-success">Ordine confermato</div>
      <p class="lead text-muted-soft mb-4">
        Abbiamo registrato il pagamento di prova e salvato l’ordine nel database.
        Troverai il riepilogo nella pagina di successo o potrai integrarlo con e-mail transazionali.
      </p>
      <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
        <a class="btn btn-dark btn-pill" href="{{ route('catalog.index') }}">Torna al catalogo</a>
        <a class="btn btn-outline-secondary btn-pill" href="{{ route('home') }}">Vai alla home</a>
      </div>
    </div>
  </section>
</x-layout>
