<footer class="border-t border-neutral-200 mt-20">
  <div class="container py-10 grid md:grid-cols-4 gap-8 text-sm">
    @foreach (['Assistenza','Info','Azienda','Social'] as $col)
      <div>
        <h5 class="font-semibold mb-3">{{ $col }}</h5>
        <ul class="space-y-2">
          <li><a href="#" class="hover:underline">Contatti</a></li>
          <li><a href="#" class="hover:underline">Spedizioni</a></li>
          <li><a href="#" class="hover:underline">Resi</a></li>
          <li><a href="#" class="hover:underline">Privacy</a></li>
        </ul>
      </div>
    @endforeach
  </div>
  <div class="container py-6 text-xs text-neutral-500">© {{ date('Y') }} Presto</div>
</footer>
