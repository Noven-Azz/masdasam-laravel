<div class="px-4 py-6 max-w-5xl mx-auto">

  {{-- Toast Success --}}
  @if(session('success'))
  <div id="updateToast" class="fixed top-4 right-4 z-50 w-[320px] bg-white border border-green-200 shadow-xl rounded-lg overflow-hidden" role="alert" aria-live="polite">
    <div class="flex items-start gap-3 p-4">
      <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" class="opacity-30"/>
        </svg>
      </div>
      <div class="flex-1">
        <p class="text-sm font-semibold text-green-800">Berhasil dikirim</p>
        <p class="text-sm text-green-700 mt-0.5">{{ session('success') }}</p>
      </div>
      <button type="button" onclick="dismissUpdateToast()" class="text-green-700/70 hover:text-green-800" aria-label="Tutup notifikasi">✕</button>
    </div>
    <div class="h-1 bg-green-200">
      <div id="updateToastBar" class="h-1 bg-green-600" style="width: 0%; transition: width 3s linear;"></div>
    </div>
  </div>
  @endif

  {{-- Errors --}}
  @if($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-red-800">
      <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Date card -->
  <div class="bg-green-700 rounded-lg shadow-md p-4 mb-6">
    <h3 class="text-white text-lg font-semibold mb-3">Tanggal Laporan</h3>
    <input
      type="date"
      name="tanggal"
      form="form-laporan-ksm"
      value="{{ old('tanggal', now()->toDateString()) }}"
      class="w-full max-w-sm bg-white rounded-md px-4 py-3 text-gray-700 font-medium shadow-sm"
      required
    />
  </div>

  <!-- Shortcut Tabs -->
  <nav class="flex flex-wrap justify-center gap-3 mb-6">
    <button type="button" class="shortcut-tab inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm hover:bg-green-50" data-target="#sec-sampah-masuk">Sampah Masuk</button>
    <button type="button" class="shortcut-tab inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm hover:bg-green-50" data-target="#sec-hasil-pilahan">Hasil Pilahan</button>
    <button type="button" class="shortcut-tab inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm hover:bg-green-50" data-target="#sec-pengangkutan">Pengangkutan</button>
    <button type="button" class="shortcut-tab inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm hover:bg-green-50" data-target="#sec-pemusnahan">Pemusnahan</button>
    <button type="button" class="shortcut-tab inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm hover:bg-green-50" data-target="#sec-timbunan">Timbunan</button>
  </nav>

  <form id="form-laporan-ksm" class="space-y-6" method="POST" action="{{ route('ksm.laporan.store') }}" novalidate>
    @csrf

    <!-- Sampah Masuk card -->
    <section id="sec-sampah-masuk" class="bg-green-700 rounded-lg shadow-md p-6 scroll-mt-24">
      <h4 class="text-white text-2xl font-semibold mb-4">Sampah Masuk</h4>
      <div class="mb-4">
        <label class="block text-white mb-2">Total Sampah Masuk</label>
        <input type="number" step="0.01" name="sampah_masuk" value="{{ old('sampah_masuk') }}" inputmode="decimal" placeholder="Contoh: 1.25"
               class="w-full max-w-lg bg-white border border-gray-200 rounded-md px-4 py-3 text-gray-800 shadow-inner">
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Sampah Diolah</label>
          <input type="number" step="0.01" name="sampah_diolah" value="{{ old('sampah_diolah') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Sampah Belum Diolah</label>
          <input type="number" step="0.01" name="sampah_belum_diolah" value="{{ old('sampah_belum_diolah') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
      </div>
    </section>

    <!-- Hasil Pilahan card -->
    <section id="sec-hasil-pilahan" class="bg-green-700 rounded-lg shadow-md p-6 scroll-mt-24">
      <h4 class="text-white text-2xl font-semibold mb-4">Hasil Pilahan</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Bahan RDF</label>
          <input type="number" step="0.01" name="hasil_pilahan_bahan_rdf" value="{{ old('hasil_pilahan_bahan_rdf') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Bursam</label>
          <input type="number" step="0.01" name="hasil_pilahan_bursam" value="{{ old('hasil_pilahan_bursam') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Residu</label>
          <input type="number" step="0.01" name="hasil_pilahan_residu" value="{{ old('hasil_pilahan_residu') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Rongsok</label>
          <input type="number" step="0.01" name="hasil_pilahan_rongsok" value="{{ old('hasil_pilahan_rongsok') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
      </div>
    </section>

    <!-- Pengangkutan card -->
    <section id="sec-pengangkutan" class="bg-green-700 rounded-lg shadow-md p-6 scroll-mt-24">
      <h4 class="text-white text-2xl font-semibold mb-4">Pengangkutan</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Bahan RDF</label>
          <input type="number" step="0.01" name="pengangkutan_bahan_rdf" value="{{ old('pengangkutan_bahan_rdf') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Bursam</label>
          <input type="number" step="0.01" name="pengangkutan_bursam" value="{{ old('pengangkutan_bursam') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Residu</label>
          <input type="number" step="0.01" name="pengangkutan_residu" value="{{ old('pengangkutan_residu') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Rongsok</label>
          <input type="number" step="0.01" name="pengangkutan_rongsok" value="{{ old('pengangkutan_rongsok') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
      </div>
    </section>

    <!-- Pemusnahan card -->
    <section id="sec-pemusnahan" class="bg-green-700 rounded-lg shadow-md p-6 scroll-mt-24">
      <h4 class="text-white text-2xl font-semibold mb-4">Pemusnahan</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Sampah Murni</label>
          <input type="number" step="0.01" name="pemusnahan_sampah_murni" value="{{ old('pemusnahan_sampah_murni') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Bahan RDF</label>
          <input type="number" step="0.01" name="pemusnahan_bahan_rdf" value="{{ old('pemusnahan_bahan_rdf') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div class="md:col-span-2">
          <label class="block text-white mb-2">Residu</label>
          <input type="number" step="0.01" name="pemusnahan_residu" value="{{ old('pemusnahan_residu') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
      </div>
    </section>

    <!-- Timbunan card -->
    <section id="sec-timbunan" class="bg-green-700 rounded-lg shadow-md p-6 scroll-mt-24">
      <h4 class="text-white text-2xl font-semibold mb-4">Timbunan</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Sampah Murni</label>
          <input type="number" step="0.01" name="timbunan_sampah_murni" value="{{ old('timbunan_sampah_murni') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Bahan RDF</label>
          <input type="number" step="0.01" name="timbunan_bahan_rdf" value="{{ old('timbunan_bahan_rdf') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Residu</label>
          <input type="number" step="0.01" name="timbunan_residu" value="{{ old('timbunan_residu') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">RDF</label>
          <input type="number" step="0.01" name="timbunan_rdf" value="{{ old('timbunan_rdf') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Rongsok</label>
          <input type="number" step="0.01" name="timbunan_rongsok" value="{{ old('timbunan_rongsok') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
        <div>
          <label class="block text-white mb-2">Bursam</label>
          <input type="number" step="0.01" name="timbunan_bursam" value="{{ old('timbunan_bursam') }}" inputmode="decimal" placeholder="Contoh: 1.25"
                 class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800">
        </div>
      </div>
    </section>

    <!-- Submit -->
    <div class="flex justify-center">
      <button type="submit"
              class="bg-green-900 hover:bg-green-800 text-white font-semibold px-8 py-3 rounded-md w-full sm:w-80 transition-colors">
        Kirim
      </button>
    </div>
  </form>
</div>

<script>
// Ganti koma menjadi titik sebelum submit
document.getElementById('form-laporan-ksm')?.addEventListener('submit', function () {
  const inputs = this.querySelectorAll('input[type="number"], input[inputmode="decimal"]');
  inputs.forEach(i => {
    if (typeof i.value === 'string') i.value = i.value.replace(',', '.');
  });
});

// Toast: animasi progress 3 detik + auto-hide
(function initToast(){
  const toast = document.getElementById('updateToast');
  if (!toast) return;

  const bar = document.getElementById('updateToastBar');
  if (bar) requestAnimationFrame(() => { bar.style.width = '100%'; });

  const timer = setTimeout(() => { dismissUpdateToast(); }, 3000);
  toast.dataset.timerId = String(timer);
})();

function dismissUpdateToast(){
  const toast = document.getElementById('updateToast');
  if (!toast) return;
  const id = toast.dataset.timerId;
  if (id) { clearTimeout(Number(id)); delete toast.dataset.timerId; }
  toast.style.transition = 'opacity .25s ease, transform .25s ease';
  toast.style.opacity = '0';
  toast.style.transform = 'translateY(-6px)';
  setTimeout(() => toast.remove(), 250);
}

// Shortcut tabs: smooth scroll ke section
document.querySelectorAll('.shortcut-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.getAttribute('data-target');
    const el = document.querySelector(target);
    if (!el) return;
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});
</script>