@extends('layouts.app')

@section('title', 'Profil KSM')

@section('content')

@php
  $displayName = trim($ksm->nama_ksm ?? 'KSM');
  $parts = preg_split('/\s+/', $displayName, -1, PREG_SPLIT_NO_EMPTY);
  if (count($parts) >= 2) {
      $initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
  } elseif (count($parts) === 1) {
      $initials = mb_strtoupper(mb_substr($parts[0], 0, 2));
  } else {
      $initials = 'KS';
  }
@endphp

<div class="min-h-screen bg-gradient-to-br from-green-700 to-green-900">
  <div class="max-w-7xl mx-auto px-4 py-8 pb-12">
    {{-- Header Actions --}}
    <div class="mb-6 flex items-center justify-between">
      <a href="{{ route('ksm.laporan-page') }}"
         class="flex items-center space-x-2 text-white hover:text-gray-200 transition-colors duration-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <span class="text-lg font-semibold">Kembali</span>
      </a>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
                class="px-4 py-2 bg-white bg-opacity-90 text-red-600 font-semibold rounded-lg shadow hover:bg-opacity-100 transition">
          Logout
        </button>
      </form>
    </div>

    {{-- Title --}}
    <div class="text-center mb-8">
      <h1 class="text-white text-3xl md:text-4xl lg:text-5xl font-bold mb-4">Profil KSM</h1>
      <div class="w-24 h-1 bg-white mx-auto"></div>
    </div>

    {{-- Grid Layout --}}
    <div class="grid lg:grid-cols-[350px,1fr] gap-8">
      {{-- Sidebar Profile Card --}}
      <div class="bg-white rounded-3xl p-8 shadow-2xl h-fit">
        {{-- Avatar --}}
        <div class="flex justify-center mb-6">
          <div class="bg-green-950 w-32 h-32 md:w-40 md:h-40 rounded-full flex items-center justify-center text-white text-5xl md:text-6xl font-bold shadow-lg">
            {{ $initials }}
          </div>
        </div>

        {{-- Nama KSM --}}
        <div class="text-center mb-8">
          <h2 class="text-2xl md:text-3xl font-bold text-green-900 mb-3">
            {{ $displayName }}
          </h2>
          <div class="inline-flex items-center justify-center space-x-2 bg-green-700 bg-opacity-10 px-4 py-2 rounded-full">
            <svg class="w-4 h-4 text-green-700" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-900 text-sm font-semibold">
              {{ $ksm->upkp->nama_upkp ?? 'Belum terdaftar di UPKP' }}
            </p>
          </div>
        </div>

        {{-- Contact Info --}}
        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <div class="w-2 h-2 bg-green-700 rounded-full mt-1.5"></div>
            <div class="flex-1">
              <span class="text-sm text-gray-600 block">No HP:</span>
              <span class="font-semibold text-green-900">{{ $ksm->no_hp ?? '-' }}</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-2 h-2 bg-green-700 rounded-full mt-1.5"></div>
            <div class="flex-1">
              <span class="text-sm text-gray-600 block">Alamat:</span>
              <span class="font-semibold text-green-900">{{ $ksm->alamat ?? '-' }}</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-2 h-2 bg-green-700 rounded-full mt-1.5"></div>
            <div class="flex-1">
              <span class="text-sm text-gray-600 block">Kelurahan:</span>
              <span class="font-semibold text-green-900">{{ $ksm->kelurahan ?? '-' }}</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-2 h-2 bg-green-700 rounded-full mt-1.5"></div>
            <div class="flex-1">
              <span class="text-sm text-gray-600 block">Kecamatan:</span>
              <span class="font-semibold text-green-900">{{ $ksm->kecamatan ?? '-' }}</span>
            </div>
          </div>
        </div>

        {{-- Tenaga Kerja --}}
        <div class="mt-8 pt-6 border-t">
          <h3 class="text-xl font-bold text-green-900 mb-4 text-center">TENAGA KERJA</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="text-center bg-green-700 bg-opacity-10 rounded-xl p-4">
              <div class="text-3xl font-bold text-green-900">
                {{ $tenagaPria ?? 0 }}
              </div>
              <div class="text-sm text-green-900">Pria</div>
            </div>
            <div class="text-center bg-green-700 bg-opacity-10 rounded-xl p-4">
              <div class="text-3xl font-bold text-green-900">
                {{ $tenagaWanita ?? 0 }}
              </div>
              <div class="text-sm text-green-900">Wanita</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Main Content - Struktur Pengurus --}}
      <div class="bg-white rounded-3xl p-8 shadow-2xl min-h-fit">
        <h3 class="text-3xl font-bold text-green-900 mb-8 text-center">STRUKTUR PENGURUS</h3>

        @if(!empty($pengurus) && count($pengurus) > 0)
          <div class="grid md:grid-cols-2 gap-6 pb-4">
            @foreach($pengurus as $index => $item)
              <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-green-700">
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0 w-12 h-12 bg-green-700 bg-opacity-10 rounded-full flex items-center justify-center">
                    <span class="text-green-700 font-bold text-lg">{{ $index + 1 }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-lg font-semibold text-green-900 mb-1 leading-tight">
                      {{ $item['jabatan'] }}
                    </h4>
                    <p class="text-lg font-medium text-slate-700 leading-tight">
                      {{ $item['nama'] }}
                    </p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <p class="text-gray-500 text-lg font-medium">Belum ada data pengurus</p>
            <p class="text-gray-400 text-sm mt-2">Data struktur pengurus belum tersedia</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection