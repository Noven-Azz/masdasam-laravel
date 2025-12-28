@php
  $isVerified = ($data['sudah_verifikasi'] ?? false) === true || ($data['sudah_verifikasi'] ?? false) === 'true';
  $profile = session('profile', []);
  $role = $profile['role'] ?? 'ksm';
  $canEdit = ($role === 'ksm' && !$isVerified) || $role === 'upkp';

  // Helper nilai default
  $hp = $data['hasil_pilahan'] ?? [];
  $pg = $data['pengangkutan'] ?? [];
  $pm = $data['pemusnahan'] ?? [];
  $tb = $data['timbunan'] ?? [];

  // Tanggal tampil (dd/mm/YYYY) dan nilai input (Y-m-d)
  $tanggalIso = isset($data['tanggal_laporan']) ? \Carbon\Carbon::parse($data['tanggal_laporan'])->format('Y-m-d') : '';
  $tanggalHuman = isset($data['tanggal_laporan']) ? \Carbon\Carbon::parse($data['tanggal_laporan'])->format('d/m/Y') : '-';
@endphp

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
  {{-- Header --}}
  <div class="bg-gradient-to-r from-green-700 to-green-600 p-6 text-white">
    <div class="flex items-center justify-between gap-3">
      <div>
        <h2 class="text-2xl font-bold mb-1">Detail Laporan Sampah</h2>
        <p class="text-green-100">
          {{ $data['nama_ksm'] ?? 'KSM' }} • {{ $tanggalHuman }}
        </p>
      </div>
      <button onclick="window.history.back()" class="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-lg transition">
        ← Kembali
      </button>
    </div>
  </div>

  <div class="p-6">
    {{-- Toast Success --}}
    @if(session('success'))
    <div id="updateToast" class="fixed top-4 right-4 z-50 w-[320px] bg-white border border-green-200 shadow-xl rounded-lg overflow-hidden">
      <div class="flex items-start gap-3 p-4">
        <div class="flex-shrink-0">
          <svg class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" class="opacity-30"/>
          </svg>
        </div>
        <div class="flex-1">
          <p class="text-sm font-semibold text-green-800">Berhasil diperbarui</p>
          <p class="text-sm text-green-700 mt-0.5">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="dismissUpdateToast()" class="text-green-700/70 hover:text-green-800">✕</button>
      </div>
      <div class="h-1 bg-green-200">
        <div id="updateToastBar" class="h-1 bg-green-600" style="width: 0%"></div>
      </div>
    </div>
    @endif

    {{-- Toast Container (untuk notifikasi dinamis) --}}
    <div id="toastContainer" class="fixed top-4 right-4 z-50"></div>

    {{-- Modal Konfirmasi Verifikasi --}}
    <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all scale-95 opacity-0" id="confirmModalContent">
        <div class="p-6">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900">Verifikasi Laporan</h3>
              <p class="text-sm text-gray-500 mt-0.5">Konfirmasi tindakan Anda</p>
            </div>
          </div>
          
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r">
            <div class="flex items-start">
              <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">Perhatian!</p>
                <p class="text-sm text-yellow-700 mt-1">Data yang sudah diverifikasi tidak dapat diubah oleh KSM dan akan dipindahkan ke halaman Riwayat.</p>
              </div>
            </div>
          </div>

          <p class="text-gray-700 mb-6">Apakah Anda yakin ingin memverifikasi laporan ini?</p>

          <div class="flex gap-3">
            <button type="button" onclick="closeConfirmModal()" 
                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
              Batal
            </button>
            <button type="button" onclick="confirmVerification()" 
                    class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
              Ya, Verifikasi
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Status --}}
    <div class="mb-6">
      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
        {{ $isVerified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
        {{ $isVerified ? '✓ Terverifikasi' : '⏳ Menunggu Verifikasi' }}
      </span>
    </div>

    {{-- Satu form: view = readonly, edit = aktif --}}
    <form id="reportForm" method="POST" action="{{ $role === 'upkp' ? route('upkp.update-laporan', $data['id']) : route('ksm.update-laporan', $data['id']) }}" class="space-y-6">
      @csrf
      @method('PUT')

      {{-- Tanggal - TIDAK BISA DI-EDIT --}}
      <div class="bg-green-700 rounded-lg shadow-md p-4">
        <h3 class="text-white text-lg font-semibold mb-3">Tanggal Laporan</h3>
        <div class="w-full max-w-sm bg-white rounded-md px-4 py-3 text-gray-700 font-medium shadow-sm">
          {{ $tanggalHuman }}
        </div>
        {{-- Hidden input untuk submit form (tidak bisa diubah) --}}
        <input type="hidden" name="tanggal" value="{{ $tanggalIso }}">
      </div>

      {{-- Sampah Masuk --}}
      <section class="bg-green-700 rounded-lg shadow-md p-6">
        <h4 class="text-white text-2xl font-semibold mb-4">Sampah Masuk</h4>
        <div class="mb-4">
          <label class="block text-white mb-2">Total Sampah Masuk</label>
          <input type="number" step="0.01" name="sampah_masuk" value="{{ $data['sampah_masuk'] ?? 0 }}"
                 class="w-full max-w-lg bg-white border border-gray-200 rounded-md px-4 py-3 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                 readonly>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-white mb-2">Sampah Diolah</label>
            <input type="number" step="0.01" name="sampah_diolah" value="{{ $data['sampah_diolah'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Sampah Belum Diolah</label>
            <input type="number" step="0.01" name="sampah_belum_diolah" value="{{ $data['sampah_belum_diolah'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
        </div>
      </section>

      {{-- Hasil Pilahan --}}
      <section class="bg-green-700 rounded-lg shadow-md p-6">
        <h4 class="text-white text-2xl font-semibold mb-4">Hasil Pilahan</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-white mb-2">Bahan RDF</label>
            <input type="number" step="0.01" name="hasil_pilahan_bahan_rdf" value="{{ $hp['bahan_rdf'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Bursam</label>
            <input type="number" step="0.01" name="hasil_pilahan_bursam" value="{{ $hp['bursam'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Residu</label>
            <input type="number" step="0.01" name="hasil_pilahan_residu" value="{{ $hp['residu'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Rongsok</label>
            <input type="number" step="0.01" name="hasil_pilahan_rongsok" value="{{ $hp['rongsok'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
        </div>
      </section>

      {{-- Pengangkutan --}}
      <section class="bg-green-700 rounded-lg shadow-md p-6">
        <h4 class="text-white text-2xl font-semibold mb-4">Pengangkutan</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-white mb-2">Bahan RDF</label>
            <input type="number" step="0.01" name="pengangkutan_bahan_rdf" value="{{ $pg['bahan_rdf'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Bursam</label>
            <input type="number" step="0.01" name="pengangkutan_bursam" value="{{ $pg['bursam'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Residu</label>
            <input type="number" step="0.01" name="pengangkutan_residu" value="{{ $pg['residu'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Rongsok</label>
            <input type="number" step="0.01" name="pengangkutan_rongsok" value="{{ $pg['rongsok'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
        </div>
      </section>

      {{-- Pemusnahan --}}
      <section class="bg-green-700 rounded-lg shadow-md p-6">
        <h4 class="text-white text-2xl font-semibold mb-4">Pemusnahan</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-white mb-2">Sampah Murni</label>
            <input type="number" step="0.01" name="pemusnahan_sampah_murni" value="{{ $pm['sampah_murni'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Bahan RDF</label>
            <input type="number" step="0.01" name="pemusnahan_bahan_rdf" value="{{ $pm['bahan_rdf'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div class="md:col-span-2">
            <label class="block text-white mb-2">Residu</label>
            <input type="number" step="0.01" name="pemusnahan_residu" value="{{ $pm['residu'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
        </div>
      </section>

      {{-- Timbunan --}}
      <section class="bg-green-700 rounded-lg shadow-md p-6">
        <h4 class="text-white text-2xl font-semibold mb-4">Timbunan</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-white mb-2">Sampah Murni</label>
            <input type="number" step="0.01" name="timbunan_sampah_murni" value="{{ $tb['sampah_murni'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Bahan RDF</label>
            <input type="number" step="0.01" name="timbunan_bahan_rdf" value="{{ $tb['bahan_rdf'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Residu</label>
            <input type="number" step="0.01" name="timbunan_residu" value="{{ $tb['residu'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">RDF</label>
            <input type="number" step="0.01" name="timbunan_rdf" value="{{ $tb['rdf'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Rongsok</label>
            <input type="number" step="0.01" name="timbunan_rongsok" value="{{ $tb['rongsok'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
          <div>
            <label class="block text-white mb-2">Bursam</label>
            <input type="number" step="0.01" name="timbunan_bursam" value="{{ $tb['bursam'] ?? 0 }}"
                   class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800 read-only:opacity-90 read-only:cursor-default"
                   readonly>
          </div>
        </div>
      </section>

      {{-- Actions --}}
      <div class="flex gap-3 justify-end pt-2">
        @if($role === 'upkp' && !$isVerified)
          <button type="button" onclick="verifyReport('{{ $data['id'] }}')" id="btnVerify"
                  class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
            ✓ Verifikasi
          </button>
        @endif

        @if($canEdit)
          <button type="button" onclick="enableEdit()" id="btnEdit"
                  class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
            ✎ Edit
          </button>
          <button type="button" onclick="cancelEdit()" id="btnCancel"
                  class="hidden px-6 py-2.5 bg-gray-400 hover:bg-gray-500 text-white rounded-lg font-medium transition">
            Batal
          </button>
          <button type="submit" id="btnSave"
                  class="hidden px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
            💾 Simpan
          </button>
        @endif
      </div>
    </form>
  </div>
</div>

<script>
(function initReadonlySnapshot() {
  const form = document.getElementById('reportForm');
  if (!form) return;

  // simpan nilai awal angka
  form.querySelectorAll('input[type="number"]').forEach(el => { el.dataset.original = el.value ?? ''; });

  // simpan nilai awal tanggal
  const tInput = document.getElementById('tanggalInput');
  if (tInput) tInput.dataset.original = tInput.value ?? '';

  // Auto-hide toast
  const toast = document.getElementById('updateToast');
  const bar = document.getElementById('updateToastBar');
  if (toast) {
    let start = null;
    const duration = 3000;
    function step(ts){
      if (!start) start = ts;
      const elapsed = ts - start;
      const pct = Math.min(100, (elapsed / duration) * 100);
      if (bar) bar.style.width = pct + '%';
      if (elapsed < duration) requestAnimationFrame(step);
      else dismissUpdateToast();
    }
    requestAnimationFrame(step);
  }
})();

function enableEdit() {
  const form = document.getElementById('reportForm');
  form.querySelectorAll('input[type="number"]').forEach(el => el.removeAttribute('readonly'));

  // Tanggal TIDAK BISA DI-EDIT - tetap tampilkan sebagai teks
  
  document.getElementById('btnEdit')?.classList.add('hidden');
  document.getElementById('btnVerify')?.classList.add('hidden');
  document.getElementById('btnCancel')?.classList.remove('hidden');
  document.getElementById('btnSave')?.classList.remove('hidden');
}

function cancelEdit() {
  const form = document.getElementById('reportForm');
  form.querySelectorAll('input[type="number"]').forEach(el => {
    el.value = el.dataset.original ?? '';
    el.setAttribute('readonly', 'readonly');
  });

  document.getElementById('btnEdit')?.classList.remove('hidden');
  document.getElementById('btnVerify')?.classList.remove('hidden');
  document.getElementById('btnCancel')?.classList.add('hidden');
  document.getElementById('btnSave')?.classList.add('hidden');
}

function dismissUpdateToast(){
  const toast = document.getElementById('updateToast');
  if (!toast) return;
  toast.style.transition = 'opacity .25s ease, transform .25s ease';
  toast.style.opacity = '0';
  toast.style.transform = 'translateY(-6px)';
  setTimeout(()=> toast.remove(), 250);
}

// Ganti koma jadi titik sebelum submit
document.getElementById('reportForm')?.addEventListener('submit', (e) => {
  const nums = e.target.querySelectorAll('input[type="number"]');
  nums.forEach(el => { if (typeof el.value === 'string') el.value = el.value.replace(',', '.'); });
});

// Fungsi untuk menampilkan toast notification
function showToast(type, title, message) {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const toastId = 'toast-' + Date.now();
  const colors = {
    success: {
      bg: 'bg-white border-green-200',
      icon: 'text-green-600',
      titleColor: 'text-green-800',
      msgColor: 'text-green-700',
      barBg: 'bg-green-200',
      barFill: 'bg-green-600',
      icon: `<path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" class="opacity-30"/>`
    },
    error: {
      bg: 'bg-white border-red-200',
      icon: 'text-red-600',
      titleColor: 'text-red-800',
      msgColor: 'text-red-700',
      barBg: 'bg-red-200',
      barFill: 'bg-red-600',
      icon: `<path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" class="opacity-30"/>`
    }
  };

  const style = colors[type] || colors.success;
  
  const toast = document.createElement('div');
  toast.id = toastId;
  toast.className = `${style.bg} border shadow-xl rounded-lg overflow-hidden mb-3 w-[340px] transform transition-all duration-300 opacity-0 translate-y-2`;
  toast.innerHTML = `
    <div class="flex items-start gap-3 p-4">
      <div class="flex-shrink-0">
        <svg class="w-6 h-6 ${style.icon}" viewBox="0 0 24 24" fill="none">
          ${style.icon}
        </svg>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold ${style.titleColor}">${title}</p>
        <p class="text-sm ${style.msgColor} mt-0.5">${message}</p>
      </div>
      <button type="button" onclick="dismissToast('${toastId}')" class="${style.titleColor} opacity-70 hover:opacity-100 transition-opacity">✕</button>
    </div>
    <div class="h-1 ${style.barBg}">
      <div class="toast-bar h-1 ${style.barFill} transition-all duration-[3000ms] ease-linear" style="width: 0%"></div>
    </div>
  `;

  container.appendChild(toast);

  // Animate in
  requestAnimationFrame(() => {
    toast.classList.remove('opacity-0', 'translate-y-2');
    toast.classList.add('opacity-100', 'translate-y-0');
  });

  // Start progress bar
  const bar = toast.querySelector('.toast-bar');
  if (bar) {
    requestAnimationFrame(() => {
      bar.style.width = '100%';
    });
  }

  // Auto dismiss after 3 seconds
  setTimeout(() => dismissToast(toastId), 3000);
}

function dismissToast(toastId) {
  const toast = document.getElementById(toastId);
  if (!toast) return;
  
  toast.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
  toast.style.opacity = '0';
  toast.style.transform = 'translateY(-6px)';
  setTimeout(() => toast.remove(), 250);
}

// Modal Konfirmasi
let pendingVerificationId = null;

function verifyReport(id) {
  pendingVerificationId = id;
  openConfirmModal();
}

function openConfirmModal() {
  const modal = document.getElementById('confirmModal');
  const content = document.getElementById('confirmModalContent');
  
  modal.classList.remove('hidden');
  requestAnimationFrame(() => {
    content.classList.remove('scale-95', 'opacity-0');
    content.classList.add('scale-100', 'opacity-100');
  });
}

function closeConfirmModal() {
  const modal = document.getElementById('confirmModal');
  const content = document.getElementById('confirmModalContent');
  
  content.classList.remove('scale-100', 'opacity-100');
  content.classList.add('scale-95', 'opacity-0');
  
  setTimeout(() => {
    modal.classList.add('hidden');
    pendingVerificationId = null;
  }, 200);
}

function confirmVerification() {
  if (!pendingVerificationId) return;
  
  closeConfirmModal();
  
  // Disable button saat proses
  const btn = document.getElementById('btnVerify');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Memproses...';
  }
  
  fetch(`/upkp/verify-laporan/${pendingVerificationId}`, {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json', 
      'X-CSRF-TOKEN': '{{ csrf_token() }}' 
    }
  })
  .then(res => res.json())
  .then(data => { 
    if (data.success) { 
      showToast('success', 'Berhasil Diverifikasi', 'Laporan telah diverifikasi dan dipindahkan ke Riwayat');
      // Redirect ke halaman riwayat setelah 2 detik
      setTimeout(() => {
        window.location.href = '/upkp/riwayat';
      }, 2000); 
    } else {
      showToast('error', 'Gagal Memverifikasi', data.message || 'Terjadi kesalahan saat memverifikasi laporan');
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '✓ Verifikasi';
      }
    }
  })
  .catch(err => {
    console.error('Error:', err);
    showToast('error', 'Terjadi Kesalahan', 'Tidak dapat terhubung ke server. Silakan coba lagi.');
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '✓ Verifikasi';
    }
  });
}
</script>