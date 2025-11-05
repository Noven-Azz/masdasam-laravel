@extends('layouts.dashboard')

@section('title', 'Dashboard UPKP')

@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-green-900 text-2xl font-bold">Statistik</h1>

  <button
    onclick="resetFilters()"
    id="resetBtn"
    class="py-2 px-4 md:py-4 rounded-lg font-medium transition-all duration-200 bg-gray-300 text-gray-500 cursor-not-allowed"
    disabled
  >
    Reset Filter
  </button>
</div>

{{-- Filters --}}
<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
  <select id="filterKsm" class="border border-gray-300 rounded-lg p-2">
    <option value="">Pilih KSM</option>
    {{-- Nanti populate dari backend --}}
    <option value="1">KSM Ajibarang</option>
    <option value="2">KSM Purwokerto</option>
  </select>

  <select id="filterBahan" class="border border-gray-300 rounded-lg p-2">
    <option value="">Pilih Bahan</option>
    <option value="Pengangkutan">Pengangkutan</option>
    <option value="Pemilahan">Pemilahan</option>
    <option value="Pemusnahan">Pemusnahan</option>
    <option value="Timbunan">Timbunan</option>
  </select>

  <select id="filterMonth" class="border border-gray-300 rounded-lg p-2">
    <option value="">Pilih Bulan</option>
    @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $month)
      <option value="{{ $index + 1 }}">{{ $month }}</option>
    @endforeach
  </select>

  <select id="filterYear" class="border border-gray-300 rounded-lg p-2">
    <option value="">Pilih Tahun</option>
    @for($year = 2023; $year <= date('Y') + 1; $year++)
      <option value="{{ $year }}">{{ $year }}</option>
    @endfor
  </select>
</div>

{{-- Cards --}}
<div class="grid grid-cols-2 gap-4 mt-6 md:grid-cols-5">
  <div class="bg-white rounded-lg shadow p-4">
    <h3 class="text-sm font-semibold text-gray-600">Bahan RDF</h3>
    <p class="text-2xl font-bold text-green-700" id="cardRdf">0.00</p>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <h3 class="text-sm font-semibold text-gray-600">Sampah Murni</h3>
    <p class="text-2xl font-bold text-green-700" id="cardMurni">0.00</p>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <h3 class="text-sm font-semibold text-gray-600">Rongsok</h3>
    <p class="text-2xl font-bold text-green-700" id="cardRongsok">0.00</p>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <h3 class="text-sm font-semibold text-gray-600">Residu</h3>
    <p class="text-2xl font-bold text-green-700" id="cardResidu">0.00</p>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <h3 class="text-sm font-semibold text-gray-600">Bursam</h3>
    <p class="text-2xl font-bold text-green-700" id="cardBursam">0.00</p>
  </div>
</div>

{{-- Chart Year Filter --}}
<div class="mt-8 max-w-xs">
  <select id="filterChartYear" class="w-full border border-gray-300 rounded-lg p-2">
    <option value="">Pilih Tahun Chart</option>
    @for($year = 2023; $year <= date('Y') + 1; $year++)
      <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
    @endfor
  </select>
</div>

{{-- Chart --}}
<div class="flex flex-col mt-6">
  <h1 class="text-green-900 text-2xl font-bold text-center">
    Statistik total sampah tahun <span id="chartYearLabel">{{ date('Y') }}</span>
  </h1>

  <div class="mt-4 bg-white rounded-lg shadow p-4">
    <canvas id="chartTrash"></canvas>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize chart
const ctx = document.getElementById('chartTrash').getContext('2d');
let chartInstance = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    datasets: [{
      label: 'Total Sampah (m³)',
      data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
      borderColor: '#017F57',
      backgroundColor: 'rgba(1, 127, 87, 0.1)',
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: true }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});

// Filter logic (UI-only)
const filters = {
  ksm: document.getElementById('filterKsm'),
  bahan: document.getElementById('filterBahan'),
  month: document.getElementById('filterMonth'),
  year: document.getElementById('filterYear'),
  chartYear: document.getElementById('filterChartYear')
};

const resetBtn = document.getElementById('resetBtn');

function checkFilters() {
  const hasFilter = Object.values(filters).some(el => el.value !== '');
  if (hasFilter) {
    resetBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
    resetBtn.classList.add('bg-red-500', 'hover:bg-red-600', 'text-white', 'cursor-pointer');
    resetBtn.disabled = false;
  } else {
    resetBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
    resetBtn.classList.remove('bg-red-500', 'hover:bg-red-600', 'text-white', 'cursor-pointer');
    resetBtn.disabled = true;
  }
}

Object.values(filters).forEach(el => el.addEventListener('change', checkFilters));

function resetFilters() {
  Object.values(filters).forEach(el => el.value = '');
  checkFilters();
}

// Update chart year label
filters.chartYear.addEventListener('change', function() {
  document.getElementById('chartYearLabel').textContent = this.value || new Date().getFullYear();
});
</script>
@endpush
@endsection