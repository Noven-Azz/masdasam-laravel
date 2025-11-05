{{-- Detail Data Sampah Component --}}
<div class="bg-white rounded-lg shadow-md p-6">
  {{-- Header --}}
  <div class="flex items-center justify-between mb-6 pb-4 border-b">
    <div>
      <h2 class="text-2xl font-bold text-green-900">Detail Laporan Sampah</h2>
      <p class="text-gray-600 mt-1">{{ $data['nama_ksm'] ?? 'KSM Unknown' }}</p>
    </div>
    <button 
      onclick="closeDetail()" 
      class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
      ← Kembali
    </button>
  </div>

  {{-- Status Badge --}}
  @php
    $isVerified = ($data['sudah_verifikasi'] ?? false) === true || ($data['sudah_verifikasi'] ?? false) === 'true';
    $statusClass = $isVerified ? 'bg-green-100 text-green-800 border-green-300' : 'bg-yellow-100 text-yellow-800 border-yellow-300';
    $statusText = $isVerified ? '✓ Terverifikasi' : '⏳ Belum Diverifikasi';
  @endphp
  <div class="mb-6">
    <span class="{{ $statusClass }} text-sm font-semibold px-4 py-2 rounded-full border">
      {{ $statusText }}
    </span>
  </div>

  {{-- Info Grid --}}
  <div class="grid md:grid-cols-2 gap-6 mb-6">
    <div>
      <h3 class="text-sm font-semibold text-gray-500 mb-2">INFORMASI UMUM</h3>
      <div class="space-y-3 bg-gray-50 rounded-lg p-4">
        <div class="flex justify-between">
          <span class="text-gray-700">Tanggal Laporan:</span>
          <span class="font-semibold">
            {{ isset($data['tanggal_laporan']) ? \Carbon\Carbon::parse($data['tanggal_laporan'])->format('d F Y') : '-' }}
          </span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-700">UPKP:</span>
          <span class="font-semibold">{{ $data['nama_upkp'] ?? '-' }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-700">KSM:</span>
          <span class="font-semibold">{{ $data['nama_ksm'] ?? '-' }}</span>
        </div>
      </div>
    </div>

    <div>
      <h3 class="text-sm font-semibold text-gray-500 mb-2">SAMPAH MASUK</h3>
      <div class="bg-green-50 rounded-lg p-4">
        <div class="text-center">
          <div class="text-4xl font-bold text-green-700">{{ number_format($data['sampah_masuk'] ?? 0, 2) }}</div>
          <div class="text-gray-600 mt-1">m³</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Hasil Pilahan --}}
  @if(isset($data['hasil_pilahan']))
  <div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-500 mb-3">HASIL PILAHAN</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-blue-50 rounded-lg p-4 text-center">
        <div class="text-2xl font-bold text-blue-700">{{ number_format($data['hasil_pilahan']['bahan_rdf'] ?? 0, 2) }}</div>
        <div class="text-sm text-gray-600 mt-1">Bahan RDF (m³)</div>
      </div>
      <div class="bg-purple-50 rounded-lg p-4 text-center">
        <div class="text-2xl font-bold text-purple-700">{{ number_format($data['hasil_pilahan']['bursam'] ?? 0, 2) }}</div>
        <div class="text-sm text-gray-600 mt-1">Bursam (m³)</div>
      </div>
      <div class="bg-orange-50 rounded-lg p-4 text-center">
        <div class="text-2xl font-bold text-orange-700">{{ number_format($data['hasil_pilahan']['residu'] ?? 0, 2) }}</div>
        <div class="text-sm text-gray-600 mt-1">Residu (m³)</div>
      </div>
      <div class="bg-teal-50 rounded-lg p-4 text-center">
        <div class="text-2xl font-bold text-teal-700">{{ number_format($data['hasil_pilahan']['rongsok'] ?? 0, 2) }}</div>
        <div class="text-sm text-gray-600 mt-1">Rongsok (m³)</div>
      </div>
    </div>
  </div>
  @endif

  {{-- Pengangkutan --}}
  @if(isset($data['pengangkutan']))
  <div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-500 mb-3">PENGANGKUTAN</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-gray-50 rounded-lg p-3">
        <div class="text-lg font-bold">{{ number_format($data['pengangkutan']['bahan_rdf'] ?? 0, 2) }} m³</div>
        <div class="text-sm text-gray-600">Bahan RDF</div>
      </div>
      <div class="bg-gray-50 rounded-lg p-3">
        <div class="text-lg font-bold">{{ number_format($data['pengangkutan']['bursam'] ?? 0, 2) }} m³</div>
        <div class="text-sm text-gray-600">Bursam</div>
      </div>
      <div class="bg-gray-50 rounded-lg p-3">
        <div class="text-lg font-bold">{{ number_format($data['pengangkutan']['residu'] ?? 0, 2) }} m³</div>
        <div class="text-sm text-gray-600">Residu</div>
      </div>
      <div class="bg-gray-50 rounded-lg p-3">
        <div class="text-lg font-bold">{{ number_format($data['pengangkutan']['rongsok'] ?? 0, 2) }} m³</div>
        <div class="text-sm text-gray-600">Rongsok</div>
      </div>
    </div>
  </div>
  @endif

  {{-- Pemusnahan --}}
  @if(isset($data['pemusnahan']))
  <div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-500 mb-3">PEMUSNAHAN</h3>
    <div class="grid sm:grid-cols-3 gap-4">
      <div class="bg-red-50 rounded-lg p-3 text-center">
        <div class="text-xl font-bold text-red-700">{{ number_format($data['pemusnahan']['sampah_murni'] ?? 0, 2) }}</div>
        <div class="text-sm text-gray-600 mt-1">Sampah Murni (m³)</div>
      </div>
      <div class="bg-red-50 rounded-lg p-3 text-center">
        <div class="text-xl font-bold text-red-700">{{ number_format($data['pemusnahan']['bahan_rdf'] ?? 0, 2) }}</div>
        <div class="text-sm text-gray-600 mt-1">Bahan RDF (m³)</div>
      </div>
      <div class="bg-red-50 rounded-lg p-3 text-center">
        <div class="text-xl font-bold text-red-700">{{ number_format($data['pemusnahan']['residu'] ?? 0, 2) }}</div>
        <div class="text-sm text-gray-600 mt-1">Residu (m³)</div>
      </div>
    </div>
  </div>
  @endif

  {{-- Timbunan --}}
  @if(isset($data['timbunan']))
  <div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-500 mb-3">TIMBUNAN</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-amber-50 rounded-lg p-3 text-center">
        <div class="text-lg font-bold text-amber-700">{{ number_format($data['timbunan']['sampah_murni'] ?? 0, 2) }}</div>
        <div class="text-xs text-gray-600 mt-1">Sampah Murni</div>
      </div>
      <div class="bg-amber-50 rounded-lg p-3 text-center">
        <div class="text-lg font-bold text-amber-700">{{ number_format($data['timbunan']['bahan_rdf'] ?? 0, 2) }}</div>
        <div class="text-xs text-gray-600 mt-1">Bahan RDF</div>
      </div>
      <div class="bg-amber-50 rounded-lg p-3 text-center">
        <div class="text-lg font-bold text-amber-700">{{ number_format($data['timbunan']['residu'] ?? 0, 2) }}</div>
        <div class="text-xs text-gray-600 mt-1">Residu</div>
      </div>
      <div class="bg-amber-50 rounded-lg p-3 text-center">
        <div class="text-lg font-bold text-amber-700">{{ number_format($data['timbunan']['rdf'] ?? 0, 2) }}</div>
        <div class="text-xs text-gray-600 mt-1">RDF</div>
      </div>
      <div class="bg-amber-50 rounded-lg p-3 text-center">
        <div class="text-lg font-bold text-amber-700">{{ number_format($data['timbunan']['rongsok'] ?? 0, 2) }}</div>
        <div class="text-xs text-gray-600 mt-1">Rongsok</div>
      </div>
    </div>
  </div>
  @endif

  {{-- Action Buttons --}}
  @if(!$isVerified)
  <div class="flex gap-3 justify-end pt-4 border-t">
    <button 
      onclick="rejectReport({{ $data['id'] ?? 0 }})"
      class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
      ✗ Tolak
    </button>
    <button 
      onclick="verifyReport({{ $data['id'] ?? 0 }})"
      class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
      ✓ Verifikasi
    </button>
  </div>
  @endif
</div>

<script>
function closeDetail() {
  document.getElementById('detailView').classList.add('hidden');
  document.getElementById('listView').classList.remove('hidden');
}

function verifyReport(id) {
  if (confirm('Verifikasi laporan ini?')) {
    // TODO: integrate with backend API
    alert('Fitur verifikasi akan segera tersedia');
  }
}

function rejectReport(id) {
  if (confirm('Tolak laporan ini?')) {
    // TODO: integrate with backend API
    alert('Fitur penolakan akan segera tersedia');
  }
}
</script>