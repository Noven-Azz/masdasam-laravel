@extends('layouts.app')

@section('title', 'Riwayat Laporan KSM')

@section('content')
<section class="p-0 min-h-screen bg-gray-50">
  {{-- Header KSM --}}
  @include('components.ksm_header')

  {{-- Filter Section --}}
  <div class="flex justify-between items-center mt-4 md:justify-around px-8">
    {{-- Download Button --}}
    <button onclick="toggleDatePicker()" 
            class="bg-green-700 hover:bg-green-800 text-white font-semibold py-2 px-4 md:py-4 rounded-lg transition-colors duration-200 flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
      </svg>
      Unduh
    </button>

    {{-- Month Year Picker Button --}}
    <button onclick="toggleMonthYearPicker()" 
            class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 md:py-4 rounded-lg transition-colors duration-200">
      <span id="monthYearLabel">Bulan</span>
    </button>
  </div>

  {{-- Month Year Picker Dialog (Hidden by default) --}}
  <div id="monthYearDialog" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-bold mb-4">Pilih Bulan & Tahun</h3>
      
      <div class="space-y-4">
        {{-- Month Selector --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
          <select id="monthSelect" class="w-full border border-gray-300 rounded-lg p-2">
            <option value="">Pilih Bulan</option>
            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $month)
              <option value="{{ $index + 1 }}">{{ $month }}</option>
            @endforeach
          </select>
        </div>

        {{-- Year Selector --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
          <select id="yearSelect" class="w-full border border-gray-300 rounded-lg p-2">
            <option value="">Pilih Tahun</option>
            @for($year = 2025; $year <= date('Y') + 1; $year++)
              <option value="{{ $year }}">{{ $year }}</option>
            @endfor
          </select>
        </div>
      </div>

      <div class="flex gap-2 mt-6">
        <button onclick="closeMonthYearPicker()" 
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 rounded-lg transition-colors">
          Batal
        </button>
        <button onclick="applyMonthYear()" 
                class="flex-1 bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-lg transition-colors">
          Pilih
        </button>
      </div>
    </div>
  </div>

  {{-- Date Range Picker Dialog (Hidden by default) --}}
  <div id="datePickerDialog" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-bold mb-4">Pilih Rentang Tanggal</h3>
      
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
          <input type="date" id="startDate" class="w-full border border-gray-300 rounded-lg p-2" value="{{ date('Y-m-01') }}">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
          <input type="date" id="endDate" class="w-full border border-gray-300 rounded-lg p-2" value="{{ date('Y-m-d') }}">
        </div>
      </div>

      <div class="flex gap-2 mt-6">
        <button onclick="closeDatePicker()" 
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 rounded-lg transition-colors">
          Batal
        </button>
        <button onclick="applyDateRange()" 
                class="flex-1 bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-lg transition-colors">
          Unduh
        </button>
      </div>
    </div>
  </div>

  {{-- Loading State --}}
  <div id="loadingState" class="hidden flex flex-col items-center justify-center" style="min-height: calc(100vh - 300px);">
    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-600 mb-4"></div>
    <p class="text-gray-600">Memuat data...</p>
  </div>

  {{-- Error State --}}
  <div id="errorState" class="hidden flex flex-col items-center justify-center px-8 mt-8">
    <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md w-full">
      <p class="text-red-800 font-medium text-center">⚠️ <span id="errorMessage">Terjadi kesalahan</span></p>
      <button onclick="location.reload()" 
              class="mt-4 w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
        Coba Lagi
      </button>
    </div>
  </div>

  {{-- Data Grid --}}
  <div id="dataGrid" class="grid grid-cols-1 gap-4 mt-6 px-4 md:grid-cols-2 md:gap-5 lg:grid-cols-3 xl:grid-cols-4 lg:px-8">
    {{-- Sample Card 1 --}}
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-4 cursor-pointer" onclick="handleCardClick(1)">
      <div class="flex items-start justify-between mb-3">
        <div>
          <h3 class="font-bold text-lg text-green-900">KSM AJIBARANG</h3>
          <p class="text-sm text-gray-600">UPKP Ajibarang</p>
        </div>
        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Lengkap</span>
      </div>
      <div class="border-t pt-3">
        <p class="text-sm text-gray-600 mb-2">
          <span class="font-semibold">Tanggal:</span> 15 Oktober 2025
        </p>
        <p class="text-sm text-gray-600">
          <span class="font-semibold">Total Sampah Masuk:</span> 2.5 m³
        </p>
      </div>
    </div>

    {{-- Sample Card 2 --}}
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-4 cursor-pointer" onclick="handleCardClick(2)">
      <div class="flex items-start justify-between mb-3">
        <div>
          <h3 class="font-bold text-lg text-green-900">KSM AJIBARANG</h3>
          <p class="text-sm text-gray-600">UPKP Ajibarang</p>
        </div>
        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">Sebagian</span>
      </div>
      <div class="border-t pt-3">
        <p class="text-sm text-gray-600 mb-2">
          <span class="font-semibold">Tanggal:</span> 14 Oktober 2025
        </p>
        <p class="text-sm text-gray-600">
          <span class="font-semibold">Total Sampah Masuk:</span> 1.8 m³
        </p>
      </div>
    </div>

    {{-- Sample Card 3 --}}
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-4 cursor-pointer" onclick="handleCardClick(3)">
      <div class="flex items-start justify-between mb-3">
        <div>
          <h3 class="font-bold text-lg text-green-900">KSM AJIBARANG</h3>
          <p class="text-sm text-gray-600">UPKP Ajibarang</p>
        </div>
        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Lengkap</span>
      </div>
      <div class="border-t pt-3">
        <p class="text-sm text-gray-600 mb-2">
          <span class="font-semibold">Tanggal:</span> 13 Oktober 2025
        </p>
        <p class="text-sm text-gray-600">
          <span class="font-semibold">Total Sampah Masuk:</span> 3.2 m³
        </p>
      </div>
    </div>
  </div>

  {{-- Empty State (Hidden by default) --}}
  <div id="emptyState" class="hidden flex flex-col items-center justify-center" style="min-height: calc(100vh - 300px);">
    <img src="{{ asset('images/empty-data.png') }}" class="w-1/2 bg-cover lg:w-1/4" alt="Tidak ada data">
    <p class="text-gray-600 mt-4">Tidak ada riwayat laporan</p>
    <p class="text-sm text-gray-500 mt-2">Periode: <span id="dateRangeText">-</span></p>
  </div>

  {{-- Info Footer --}}
  <div class="text-center mt-6 text-sm text-gray-600 pb-8">
    Menampilkan <span id="dataCount">3</span> tanggal dalam periode yang dipilih
  </div>
</section>

<script>
// Month Year Picker
function toggleMonthYearPicker() {
  document.getElementById('monthYearDialog').classList.toggle('hidden');
}

function closeMonthYearPicker() {
  document.getElementById('monthYearDialog').classList.add('hidden');
}

function applyMonthYear() {
  const month = document.getElementById('monthSelect').value;
  const year = document.getElementById('yearSelect').value;
  
  if (month && year) {
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    document.getElementById('monthYearLabel').textContent = `${months[month - 1]} ${year}`;
    closeMonthYearPicker();
    // Here you would normally filter data by month/year
  }
}

// Date Range Picker
function toggleDatePicker() {
  document.getElementById('datePickerDialog').classList.toggle('hidden');
}

function closeDatePicker() {
  document.getElementById('datePickerDialog').classList.add('hidden');
}

function applyDateRange() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;
  
  if (startDate && endDate) {
    closeDatePicker();
    alert(`Download data dari ${startDate} sampai ${endDate}`);
    // Here you would normally trigger download
  }
}

// Card Click Handler
function handleCardClick(id) {
  window.location.href = `/detail-report-ksm/${id}`;
}
</script>
@endsection