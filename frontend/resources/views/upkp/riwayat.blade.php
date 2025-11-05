@extends('layouts.dashboard')

@section('title', 'Riwayat Laporan UPKP')

@section('content')
<div class="py-4 px-4 sm:py-6 sm:px-6">
  {{-- Stats Cards Container --}}
  <div class="w-full max-w-7xl mx-auto 2xl:max-w-full">
    <div class="bg-white shadow-md rounded-2xl overflow-hidden">
      <div class="flex flex-col md:flex-row md:divide-x-2 divide-y-2 md:divide-y-0">
        {{-- Total KSM --}}
        <div class="flex-1 p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-medium text-gray-600">Total KSM</h3>
              <p class="text-2xl font-bold text-green-900" id="totalKsm">{{ $stats['total_ksm'] ?? 0 }}</p>
              <p class="text-xs text-gray-500 mt-1 truncate">{{ $upkpName ?? '-' }}</p>
            </div>
          </div>
        </div>

        {{-- Laporan Masuk (Hari Ini) --}}
        <div class="flex-1 p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-700" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-medium text-gray-600">Laporan Masuk</h3>
              <p class="text-2xl font-bold text-blue-900" id="laporanMasukToday">{{ $statsToday['laporan_masuk'] ?? 0 }}</p>
              <p class="text-xs text-gray-500 mt-1" id="todayLabel">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
            </div>
          </div>
        </div>

        {{-- Total Sampah (Hari Ini) --}}
        <div class="flex-1 p-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-orange-700" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-medium text-gray-600">Total Sampah (m³)</h3>
              <p class="text-2xl font-bold text-orange-900" id="totalSampahToday">{{ number_format($statsToday['total_sampah_m3'] ?? 0, 2) }}</p>
              <p class="text-xs text-gray-500 mt-1" id="todayLabel2">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Detail View (Hidden by default) --}}
  <div id="detailView" class="hidden mt-6 p-5 rounded-lg shadow-lg bg-white">
    @include('components.detail-data-sampah')
  </div>

  {{-- List View --}}
  <div id="listView">
    {{-- Actions & Filters --}}
    <div class="flex flex-col gap-4 mt-6 md:flex-row md:justify-between md:items-start">
      {{-- Left Section --}}
      <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:flex-wrap sm:items-center">
        {{-- Download Button --}}
        <button 
          onclick="openExportDialog()"
          class="py-2.5 px-4 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Unduh
        </button>

        {{-- KSM Filter --}}
        <select 
          id="filterKsm" 
          onchange="handleKsmFilter(this.value)"
          class="w-full sm:w-auto sm:min-w-[200px] border border-gray-300 rounded-lg px-3 py-2">
          <option value="">Pilih KSM</option>
          @foreach($listKsm as $ksm)
            <option value="{{ $ksm }}">{{ $ksm }}</option>
          @endforeach
        </select>
      </div>

      {{-- Right Section - Date Range --}}
      <button 
        onclick="openDateRangePicker()"
        class="w-full md:w-auto px-4 py-2.5 rounded-lg text-left font-medium bg-green-700 text-white hover:bg-green-800 transition-colors flex items-center justify-between md:justify-center gap-2 min-w-[180px]">
        <span>Rentang Tanggal</span>
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </button>
    </div>

    {{-- Error State --}}
    <div id="errorState" class="hidden mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
      <div class="font-medium">Error:</div>
      <div id="errorMessage"></div>
      <button 
        onclick="window.location.reload()"
        class="mt-2 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
        Coba Lagi
      </button>
    </div>

    {{-- Data Table --}}
    <div class="w-full mt-6 p-5 rounded-lg shadow-lg bg-white">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">No</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama KSM</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">UPKP</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tanggal</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Total Sampah</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
            @foreach($rows as $index => $row)
              <tr class="hover:bg-gray-50" data-ksm="{{ $row['nama'] }}">
                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['nama'] }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['nama_upkp'] }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['tanggal'] }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['total'] }}</td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 text-xs font-medium rounded-full {{ $row['status'] === 'Terverifikasi' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $row['status'] }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <button 
                    onclick="viewDetail({{ $row['id'] }})"
                    class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                    Lihat
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden text-center py-12 text-gray-500">
          <div class="text-6xl mb-4">📋</div>
          <h3 class="text-lg font-medium mb-2">Tidak ada data</h3>
          <p>Tidak ada laporan untuk rentang tanggal yang dipilih</p>
        </div>
      </div>
    </div>

    {{-- Info Footer --}}
    <div class="text-sm text-gray-500 mt-2">
      Rentang aktif: <span id="dateRangeLabel">{{ $headerDateLabel ?? '-' }}</span> | 
      Total data: <span id="totalRows">{{ count($rows) }}</span> laporan
    </div>
  </div>
</div>

{{-- Date Range Picker Modal --}}
<div id="dateRangeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
    <h3 class="text-lg font-bold mb-4">Pilih Rentang Tanggal</h3>
    
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
        <input 
          type="date" 
          id="dateStart" 
          value="{{ $selectedRange['start'] ?? date('Y-m-01') }}"
          class="w-full border border-gray-300 rounded px-3 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Tanggal Akhir</label>
        <input 
          type="date" 
          id="dateEnd" 
          value="{{ $selectedRange['end'] ?? date('Y-m-d') }}"
          class="w-full border border-gray-300 rounded px-3 py-2">
      </div>
    </div>

    <div class="flex gap-3 mt-6">
      <button 
        onclick="closeDateRangePicker()"
        class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
        Batal
      </button>
      <button 
        onclick="applyDateRange()"
        class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Terapkan
      </button>
    </div>
  </div>
</div>

{{-- Export Dialog --}}
<div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-96">
    <h3 class="text-lg font-bold mb-4">Pilih Rentang Export</h3>

    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
        <input 
          type="date" 
          id="exportStart" 
          value="{{ $selectedRange['start'] ?? date('Y-m-01') }}"
          class="w-full border border-gray-300 rounded px-3 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Tanggal Akhir</label>
        <input 
          type="date" 
          id="exportEnd" 
          value="{{ $selectedRange['end'] ?? date('Y-m-d') }}"
          class="w-full border border-gray-300 rounded px-3 py-2">
      </div>
    </div>

    <div class="flex gap-3 mt-6">
      <button 
        onclick="closeExportDialog()"
        class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
        Batal
      </button>
      <button 
        onclick="confirmExport()"
        class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Download
      </button>
    </div>
  </div>
</div>

@push('scripts')
<script>
// State
let allRows = @json($rows);
let filteredRows = [...allRows];
let currentKsmFilter = '';

// Date Range Picker
function openDateRangePicker() {
  document.getElementById('dateRangeModal').classList.remove('hidden');
}

function closeDateRangePicker() {
  document.getElementById('dateRangeModal').classList.add('hidden');
}

function applyDateRange() {
  const start = document.getElementById('dateStart').value;
  const end = document.getElementById('dateEnd').value;
  
  if (start && end) {
    // Reload dengan query params (atau fetch via AJAX)
    window.location.href = `{{ url('/riwayat-upkp') }}?start=${start}&end=${end}`;
  }
}

// Export Dialog
function openExportDialog() {
  document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportDialog() {
  document.getElementById('exportModal').classList.add('hidden');
}

function confirmExport() {
  const start = document.getElementById('exportStart').value;
  const end = document.getElementById('exportEnd').value;
  
  if (start && end) {
    window.location.href = `/api/upkp/history/export.csv?start=${start}&end=${end}`;
    closeExportDialog();
  }
}

// KSM Filter
function handleKsmFilter(ksm) {
  currentKsmFilter = ksm;
  applyFilters();
}

function applyFilters() {
  const tbody = document.getElementById('tableBody');
  const emptyState = document.getElementById('emptyState');
  
  filteredRows = allRows.filter(row => {
    if (currentKsmFilter && row.nama !== currentKsmFilter) return false;
    return true;
  });

  tbody.innerHTML = '';
  
  if (filteredRows.length === 0) {
    tbody.classList.add('hidden');
    emptyState.classList.remove('hidden');
    return;
  }

  tbody.classList.remove('hidden');
  emptyState.classList.add('hidden');

  filteredRows.forEach((row, index) => {
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50';
    tr.innerHTML = `
      <td class="px-4 py-3 text-sm text-gray-900">${index + 1}</td>
      <td class="px-4 py-3 text-sm font-medium text-gray-900">${row.nama}</td>
      <td class="px-4 py-3 text-sm text-gray-600">${row.nama_upkp}</td>
      <td class="px-4 py-3 text-sm text-gray-600">${row.tanggal}</td>
      <td class="px-4 py-3 text-sm text-gray-600">${row.total}</td>
      <td class="px-4 py-3">
        <span class="px-2 py-1 text-xs font-medium rounded-full ${row.status === 'Terverifikasi' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
          ${row.status}
        </span>
      </td>
      <td class="px-4 py-3 text-center">
        <button onclick="viewDetail(${row.id})" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
          Lihat
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });

  document.getElementById('totalRows').textContent = filteredRows.length;
}

function viewDetail(id) {
  // Redirect ke halaman detail atau show modal
  window.location.href = `/riwayat-upkp/detail/${id}`;
}

// Initialize
applyFilters();
</script>
@endpush
@endsection