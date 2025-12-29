@extends('layouts.app')

@section('title', 'Riwayat Laporan KSM')

@section('content')
<section class="p-0 min-h-screen bg-gray-50">
  {{-- Header KSM --}}
  @include('components.ksm_header')

  {{-- Success Alert --}}
  @if(session('success'))
    <div class="mx-4 mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-green-800">
      {{ session('success') }}
    </div>
  @endif

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
      <span id="monthYearLabel">
        @if(request('month') && request('year'))
          {{ ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][request('month')] }} {{ request('year') }}
        @else
          Bulan
        @endif
      </span>
    </button>
  </div>

  {{-- Month Year Picker Dialog --}}
  <div id="monthYearDialog" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-bold mb-4">Pilih Bulan & Tahun</h3>
      
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
          <select id="monthSelect" class="w-full border border-gray-300 rounded-lg p-2">
            <option value="">Semua Bulan</option>
            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $month)
              <option value="{{ $index + 1 }}" {{ request('month') == ($index + 1) ? 'selected' : '' }}>{{ $month }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
          <select id="yearSelect" class="w-full border border-gray-300 rounded-lg p-2">
            <option value="">Semua Tahun</option>
            @for($year = 2020; $year <= date('Y') + 1; $year++)
              <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endfor
          </select>
        </div>
      </div>

      <div class="flex gap-2 mt-6">
        <button onclick="resetFilter()" 
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 rounded-lg transition-colors">
          Reset
        </button>
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

  {{-- Date Range Picker Dialog --}}
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

  {{-- Data Grid --}}
  @if($laporan->count() > 0)
    <div id="dataGrid" class="grid grid-cols-1 gap-4 mt-6 px-4 md:grid-cols-2 md:gap-5 lg:grid-cols-3 xl:grid-cols-4 lg:px-8">
      @foreach($laporan as $item)
        <x-card-report-history-ksm
            :upkp-name="$item->upkp->nama_upkp ?? 'UPKP Unknown'"
            :ksm-name="$item->ksm->nama_ksm ?? 'KSM Unknown'"
            :date="$item->tanggal"
            :status="$item->sudah_verifikasi ? 'verified' : 'pending'"
            :total-sampah-masuk="$item->sampah_masuk ?? 0"
            :report-id="$item->id"
        />
      @endforeach
    </div>

    {{-- Info Footer --}}
    <div class="text-center mt-6 text-sm text-gray-600 pb-8">
      Menampilkan <span id="dataCount" class="font-semibold text-green-700">{{ $laporan->count() }}</span> laporan
    </div>
  @else
    {{-- Empty State --}}
    <div id="emptyState" class="flex flex-col items-center justify-center" style="min-height: calc(100vh - 300px);">
      <svg class="w-32 h-32 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-gray-600 font-medium text-lg">Tidak ada riwayat laporan</p>
      <p class="text-sm text-gray-500 mt-2">Belum ada data laporan untuk KSM ini</p>
    </div>
  @endif
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
  
  if (month || year) {
    let url = `{{ route('ksm.riwayat') }}`;
    const params = [];
    if (month) params.push(`month=${month}`);
    if (year) params.push(`year=${year}`);
    if (params.length > 0) {
      url += '?' + params.join('&');
    }
    window.location.href = url;
  } else {
    window.location.href = `{{ route('ksm.riwayat') }}`;
  }
}

function resetFilter() {
  window.location.href = `{{ route('ksm.riwayat') }}`;
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
    window.location.href = `{{ route('ksm.riwayat') }}?download=1&start=${startDate}&end=${endDate}`;
  } else {
    alert('Silakan pilih tanggal mulai dan tanggal akhir');
  }
}
</script>
@endsection