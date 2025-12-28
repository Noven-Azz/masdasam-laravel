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
    @foreach($ksmList ?? [] as $ksm)
      <option value="{{ $ksm }}">{{ $ksm }}</option>
    @endforeach
  </select>

  <select id="filterBahan" class="border border-gray-300 rounded-lg p-2">
    <option value="">Semua Bahan</option>
    <option value="Pemilahan">Pemilahan</option>
    <option value="Pengangkutan">Pengangkutan</option>
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
      tension: 0.4,
      fill: true
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

// Filter logic
const filters = {
  ksm: document.getElementById('filterKsm'),
  bahan: document.getElementById('filterBahan'),
  month: document.getElementById('filterMonth'),
  year: document.getElementById('filterYear'),
  chartYear: document.getElementById('filterChartYear')
};

const resetBtn = document.getElementById('resetBtn');

// Card elements
const cards = {
  rdf: document.getElementById('cardRdf'),
  murni: document.getElementById('cardMurni'),
  rongsok: document.getElementById('cardRongsok'),
  residu: document.getElementById('cardResidu'),
  bursam: document.getElementById('cardBursam')
};

// Fetch stats data
async function fetchStats() {
  const params = new URLSearchParams();
  
  if (filters.ksm.value) params.append('ksm', filters.ksm.value);
  if (filters.bahan.value) params.append('bahan', filters.bahan.value);
  if (filters.month.value) params.append('month', filters.month.value);
  if (filters.year.value) params.append('year', filters.year.value);

  try {
    const response = await fetch(`/upkp/api/stats?${params}`);
    const data = await response.json();
    
    cards.rdf.textContent = Number(data.rdf).toFixed(2);
    cards.murni.textContent = Number(data.murni).toFixed(2);
    cards.rongsok.textContent = Number(data.rongsok).toFixed(2);
    cards.residu.textContent = Number(data.residu).toFixed(2);
    cards.bursam.textContent = Number(data.bursam).toFixed(2);
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
}

// Fetch chart data
async function fetchChartData(year) {
  try {
    const response = await fetch(`/upkp/api/chart?year=${year}`);
    const result = await response.json();
    
    chartInstance.data.datasets[0].data = result.data;
    chartInstance.update();
  } catch (error) {
    console.error('Error fetching chart data:', error);
  }
}

// Check if filters are active
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

// Reset filters
function resetFilters() {
  Object.values(filters).forEach(el => el.value = '');
  filters.chartYear.value = new Date().getFullYear();
  checkFilters();
  fetchStats();
  fetchChartData(new Date().getFullYear());
  document.getElementById('chartYearLabel').textContent = new Date().getFullYear();
}

// Event listeners
[filters.ksm, filters.bahan, filters.month, filters.year].forEach(el => {
  el.addEventListener('change', () => {
    checkFilters();
    fetchStats();
  });
});

filters.chartYear.addEventListener('change', function() {
  const year = this.value || new Date().getFullYear();
  document.getElementById('chartYearLabel').textContent = year;
  fetchChartData(year);
});

// Initial load
fetchStats();
fetchChartData(filters.chartYear.value || new Date().getFullYear());
</script>
@endpush
@endsection