<x-layout title="Chi siamo">
  <section class="container-xxl mb-5">
    <div class="row g-4 align-items-center">
      <div class="col-lg-6">
        <span class="feature-chip mb-3">Chi siamo</span>
        <h1 class="display-6 fw-bold mb-3">Raccontiamo il minimalismo urbano.</h1>
        <p class="lead text-muted-soft mb-3">
          StreetHooligans nasce per selezionare capi essenziali, con un focus su materiali responsabili e palette neutre.
          Lavoriamo con piccoli laboratori e brand indipendenti per proporre drop limitati e senza tempo.
        </p>
        <p class="text-muted-soft mb-4">
          Curare un catalogo digitale ci permette di sperimentare feature rapide, checkout intuitivi e storytelling pensato
          per chi vive la città tutti i giorni.
        </p>
        <a href="{{ route('catalog.index') }}" class="btn btn-dark btn-pill btn-lg">Scopri i prodotti</a>
      </div>
      <div class="col-lg-6">
        <div class="stacked-card h-100">
          <h2 class="h4 mb-3">Cosa ci guida</h2>
          <ul class="list-unstyled text-muted-soft mb-0 d-grid gap-3">
            <li>Ricerca continua di tessuti premium e silhouette pulite.</li>
            <li>Esperienza d'acquisto lineare, pronta per integrazioni future.</li>
            <li>Supporto umano e trasparente, dalla scoperta al post-vendita.</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="container-xxl">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="stacked-card h-100">
          <span class="status-pill mb-3">Valori</span>
          <p class="text-muted-soft mb-0">Minimalismo funzionale, qualità tracciabile e storytelling sincero.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stacked-card h-100">
          <span class="status-pill mb-3">Community</span>
          <p class="text-muted-soft mb-0">Collaboriamo con creativi indipendenti per editoriale, foto e styling.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stacked-card h-100">
          <span class="status-pill mb-3">Next</span>
          <p class="text-muted-soft mb-0">Stiamo testando funzionalità per personal shopping e fidelity digitale.</p>
        </div>
      </div>
    </div>
  </section>
</x-layout>
