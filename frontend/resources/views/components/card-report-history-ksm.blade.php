@props([
    'upkpName' => 'UPKP Unknown',
    'ksmName' => 'KSM Unknown',
    'date' => null,
    'status' => 'pending',
    'totalSampahMasuk' => 0,
    'reportId' => null
])

@php
    $isVerified = in_array(strtolower($status), ['verified', 'terverifikasi']);
    $statusBg = $isVerified ? 'bg-green-100' : 'bg-yellow-100';
    $statusText = $isVerified ? 'text-green-800' : 'text-yellow-800';
    $statusLabel = $isVerified ? 'Terverifikasi' : 'Belum Diverifikasi';
    
    // Format date
    $formattedDate = '-';
    if ($date) {
        try {
            $formattedDate = \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y');
        } catch (\Exception $e) {
            $formattedDate = $date;
        }
    }
@endphp

<div 
    onclick="window.location.href='{{ url('/detail-report-ksm/' . $reportId) }}'"
    class="bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 p-5 cursor-pointer border border-gray-100 hover:border-green-500"
>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-lg text-green-900 truncate mb-1">
                {{ $ksmName }}
            </h3>
            <p class="text-sm text-gray-600 flex items-center gap-1">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                <span class="truncate">{{ $upkpName }}</span>
            </p>
        </div>
        <span class="{{ $statusBg }} {{ $statusText }} text-xs font-semibold px-3 py-1.5 rounded-full whitespace-nowrap ml-2 flex-shrink-0">
            {{ $statusLabel }}
        </span>
    </div>

    {{-- Divider --}}
    <div class="border-t border-gray-200 my-3"></div>

    {{-- Content --}}
    <div class="space-y-2">
        <div class="flex items-start gap-2">
            <svg class="w-4 h-4 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 mb-0.5">Tanggal</p>
                <p class="text-sm font-semibold text-gray-800 truncate">{{ $formattedDate }}</p>
            </div>
        </div>

        <div class="flex items-start gap-2">
            <svg class="w-4 h-4 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 mb-0.5">Total Sampah Masuk</p>
                <p class="text-sm font-semibold text-gray-800">
                    <span class="text-green-700">{{ number_format($totalSampahMasuk, 2, ',', '.') }}</span>
                    <span class="text-xs text-gray-500 ml-1">m³</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Footer Arrow --}}
    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end">
        <span class="text-xs text-green-700 font-medium flex items-center gap-1">
            Lihat Detail
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
    </div>
</div>