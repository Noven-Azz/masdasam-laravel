@extends('layouts.dashboard')

@section('title', 'Konfirmasi Laporan UPKP')

@section('content')
<div class="p-4">
  {{-- Header Info --}}
  <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-gray-800">Dashboard UPKP - Konfirmasi Laporan</h2>
        <p class="text-sm text-gray-600">{{ $profile['name'] ?? 'Admin UPKP' }} • {{ strtoupper($profile['role'] ?? 'UPKP') }}</p>
      </div>
      <button onclick="window.location.reload()" 
              class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors">
        Refresh
      </button>
    </div>
  </div>

  {{-- List View --}}
  <div id="listView">
    {{-- Search Bar --}}
    <div class="mx-auto max-w-2xl mb-4">
      <div class="relative">
        <input 
          type="text" 
          id="searchInput"
          placeholder="Cari nama KSM..."
          class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
          oninput="handleSearch(this.value)"
        >
        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>
    </div>

    {{-- Filter Buttons --}}
    <div class="flex gap-2 mt-4 ml-6 flex-wrap">
      <button 
        onclick="handleFilterVerifikasi(undefined)"
        id="filterAll"
        class="px-4 py-2 rounded-lg text-sm font-medium bg-green-700 text-white transition-colors">
        Semua (<span id="countAll">{{ count($laporanData) }}</span>)
      </button>
      <button 
        onclick="handleFilterVerifikasi(false)"
        id="filterPending"
        class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
        Belum Verifikasi (<span id="countPending">{{ collect($laporanData)->where('sudah_verifikasi', false)->count() }}</span>)
      </button>
      <button 
        onclick="handleFilterVerifikasi(true)"
        id="filterVerified"
        class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
        Sudah Verifikasi (<span id="countVerified">{{ collect($laporanData)->where('sudah_verifikasi', true)->count() }}</span>)
      </button>
    </div>

    {{-- Title --}}
    <h1 class="text-green-900 font-bold text-xl mt-12 ml-6">
      Laporan Masuk
      <span id="filterInfo" class="text-gray-500 font-normal text-base ml-2 hidden">
        - Menampilkan <span id="displayCount">0</span> dari <span id="totalCount">{{ count($laporanData) }}</span> laporan
      </span>
    </h1>

    {{-- Loading State --}}
    <div id="loadingState" class="hidden flex justify-center items-center py-12">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-700 mx-auto"></div>
        <p class="mt-4 text-gray-600">Memuat data laporan...</p>
      </div>
    </div>

    {{-- Error State --}}
    <div id="errorState" class="hidden mx-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
      <div class="font-semibold">❌ Error: <span id="errorMessage"></span></div>
      <div class="flex gap-2 mt-3">
        <button onclick="window.location.reload()" 
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
          Coba Lagi
        </button>
      </div>
    </div>

    {{-- Empty State (All) --}}
    <div id="emptyStateAll" class="hidden text-center py-12 text-gray-500">
      <div class="text-6xl mb-4">📋</div>
      <h3 class="text-lg font-medium mb-2">Tidak ada laporan</h3>
      <p class="mb-1">Belum ada laporan masuk untuk UPKP ini</p>
      <button onclick="window.location.reload()" 
              class="mt-4 px-6 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors">
        🔄 Refresh Data
      </button>
    </div>

    {{-- Empty State (Filtered) --}}
    <div id="emptyStateFiltered" class="hidden text-center py-12 text-gray-500">
      <div class="text-6xl mb-4">🔍</div>
      <h3 class="text-lg font-medium mb-2">Tidak ada hasil</h3>
      <p class="mb-1" id="emptyMessage">Tidak ada laporan sesuai filter</p>
      <div class="flex gap-2 justify-center mt-4">
        <button onclick="resetFilters()" 
                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
          Reset Filter
        </button>
      </div>
    </div>

    {{-- Cards Grid --}}
    <div id="cardsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 max-w-screen-lg mx-auto">
      @foreach($laporanData as $laporan)
        <x-card-konfirmasi
          :data="$laporan"
          :report-id="$laporan['id']"
        />
      @endforeach
    </div>
  </div>
</div>

@push('scripts')
<script>
// State
let allData = @json($laporanData);
let filteredData = [...allData];
let currentFilter = undefined;
let searchTerm = '';

// Filter logic
function applyFilters() {
  let result = [...allData];

  // Filter by verification status
  if (currentFilter !== undefined) {
    result = result.filter(item => {
      const isVerified = item.sudah_verifikasi === true || item.sudah_verifikasi === 'true';
      return currentFilter ? isVerified : !isVerified;
    });
  }

  // Filter by search term
  if (searchTerm.trim()) {
    const search = searchTerm.toLowerCase();
    result = result.filter(item => 
      (item.nama_ksm || '').toLowerCase().includes(search)
    );
  }

  filteredData = result;
  updateUI();
}

function handleSearch(value) {
  searchTerm = value;
  applyFilters();
}

function handleFilterVerifikasi(status) {
  currentFilter = status;
  
  // Update button styles
  const buttons = {
    filterAll: document.getElementById('filterAll'),
    filterPending: document.getElementById('filterPending'),
    filterVerified: document.getElementById('filterVerified')
  };

  Object.values(buttons).forEach(btn => {
    btn.classList.remove('bg-green-700', 'text-white', 'bg-orange-500', 'bg-green-500');
    btn.classList.add('bg-gray-200', 'text-gray-700');
  });

  if (status === undefined) {
    buttons.filterAll.classList.remove('bg-gray-200', 'text-gray-700');
    buttons.filterAll.classList.add('bg-green-700', 'text-white');
  } else if (status === false) {
    buttons.filterPending.classList.remove('bg-gray-200', 'text-gray-700');
    buttons.filterPending.classList.add('bg-orange-500', 'text-white');
  } else {
    buttons.filterVerified.classList.remove('bg-gray-200', 'text-gray-700');
    buttons.filterVerified.classList.add('bg-green-500', 'text-white');
  }

  applyFilters();
}

function resetFilters() {
  searchTerm = '';
  currentFilter = undefined;
  document.getElementById('searchInput').value = '';
  handleFilterVerifikasi(undefined);
}

function updateUI() {
  const grid = document.getElementById('cardsGrid');
  const emptyAll = document.getElementById('emptyStateAll');
  const emptyFiltered = document.getElementById('emptyStateFiltered');
  const filterInfo = document.getElementById('filterInfo');

  // Hide all states
  grid.classList.add('hidden');
  emptyAll.classList.add('hidden');
  emptyFiltered.classList.add('hidden');
  filterInfo.classList.add('hidden');

  if (allData.length === 0) {
    emptyAll.classList.remove('hidden');
    return;
  }

  if (filteredData.length === 0) {
    emptyFiltered.classList.remove('hidden');
    let message = 'Tidak ada laporan sesuai filter';
    if (searchTerm) {
      message = `Tidak ada laporan dengan KSM "${searchTerm}"`;
    } else if (currentFilter !== undefined) {
      message = `Tidak ada laporan ${currentFilter ? 'yang sudah' : 'yang belum'} diverifikasi`;
    }
    document.getElementById('emptyMessage').textContent = message;
    return;
  }

  // Show grid and filter info
  grid.classList.remove('hidden');
  if (currentFilter !== undefined || searchTerm) {
    filterInfo.classList.remove('hidden');
    document.getElementById('displayCount').textContent = filteredData.length;
  }

  // Re-render cards
  grid.innerHTML = '';
  filteredData.forEach(laporan => {
    const card = createCard(laporan);
    grid.appendChild(card);
  });
}

function createCard(data) {
  const div = document.createElement('div');
  div.className = 'bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 p-5 cursor-pointer border border-gray-100 hover:border-green-500';
  div.onclick = () => viewDetail(data.id);
  
  const isVerified = data.sudah_verifikasi === true || data.sudah_verifikasi === 'true';
  const statusClass = isVerified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
  const statusText = isVerified ? 'Terverifikasi' : 'Belum Diverifikasi';
  
  const date = data.tanggal_laporan ? new Date(data.tanggal_laporan).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-';
  
  div.innerHTML = `
    <div class="flex items-start justify-between mb-4">
      <div class="flex-1 min-w-0">
        <h3 class="font-bold text-lg text-green-900 truncate mb-1">${data.nama_ksm || 'KSM Unknown'}</h3>
        <p class="text-sm text-gray-600">${data.nama_upkp || 'UPKP Unknown'}</p>
      </div>
      <span class="${statusClass} text-xs font-semibold px-3 py-1.5 rounded-full whitespace-nowrap ml-2">${statusText}</span>
    </div>
    <div class="border-t border-gray-200 my-3"></div>
    <div class="space-y-2">
      <p class="text-sm"><span class="text-gray-500">Tanggal:</span> <span class="font-semibold">${date}</span></p>
      <p class="text-sm"><span class="text-gray-500">Sampah Masuk:</span> <span class="font-semibold text-green-700">${(data.sampah_masuk || 0).toFixed(2)} m³</span></p>
    </div>
  `;
  
  return div;
}

function viewDetail(id) {
  window.location.href = `/upkp/konfirmasi/detail/${id}`;
}

// Initialize
applyFilters();
</script>
@endpush
@endsection