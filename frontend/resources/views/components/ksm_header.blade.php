@php
  $profile = session('profile') ?? [];
  $supabaseUser = session('supabase_user') ?? [];

  $ksmName = 'KSM';
  if (!empty($profile['id_ksm'])) {
      $ksm = \App\Models\Ksm::find($profile['id_ksm']);
      if ($ksm) $ksmName = $ksm->nama_ksm ?? $ksmName;
  } elseif (!empty($supabaseUser['id'])) {
      $ksm = \App\Models\Ksm::where('user_id', $supabaseUser['id'])->first();
      if ($ksm) $ksmName = $ksm->nama_ksm ?? $ksmName;
  }

  $name = trim((string) $ksmName);
  $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);
  $initials = '';
  if (count($parts) >= 2) {
      $initials = mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1);
  } elseif (count($parts) === 1) {
      $initials = mb_substr($parts[0], 0, 2);
  } else {
      $initials = 'KS';
  }
  $initials = mb_strtoupper($initials);
@endphp

<div class="w-full bg-white px-0 overflow-hidden">
  <!-- Kotak Hijau -->
  <div class="bg-[#017F57] text-white px-4 pt-4 pb-3 relative flex flex-row items-center space-x-4 w-full">
    <!-- Foto Profil di kiri -->
    <a href="{{ url('/profil-ksm') }}" aria-label="Profil KSM"
       class="bg-green-950 w-20 h-20 rounded-full flex items-center justify-center text-2xl font-bold hover:bg-green-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
      {{ $initials }}
    </a>

    <!-- Konten di kanan -->
    <div class="flex flex-col justify-center">
      <h3 class="font-bold text-base md:text-lg lg:text-xl">{{ $name }}</h3>
      <p class="text-xs mt-1 md:text-sm">Apa yang ingin anda lakukan?</p>

      <!-- Tombol -->
      <div class="flex space-x-2 mt-2">
        <a href="{{ url('/laporan-ksm') }}"
           class="bg-green-950 px-4 py-1 text-xs md:text-sm rounded font-semibold hover:bg-green-800 transition-colors duration-200">
          Laporan
        </a>
        <a href="{{ url('/report-history-ksm') }}"
           class="bg-green-950 px-4 py-1 text-xs md:text-sm rounded font-semibold hover:bg-green-800 transition-colors duration-200">
          Riwayat
        </a>
      </div>
    </div>

    <!-- Dekorasi -->
    <div class="absolute top-20 right-6 w-5 h-5 border-4 border-white rounded-full" aria-hidden="true"></div>
    <div class="absolute -top-4 -right-4 w-16 h-16 border-[5px] border-white rounded-full" aria-hidden="true"></div>
    <div class="absolute -top-8 -right-8 w-24 h-24 border-[8px] border-white rounded-full" aria-hidden="true"></div>
  </div>

  <!-- Bar Hijau Bawah -->
  <div class="h-[8px] w-4/5 bg-green-900 rounded-r-lg mt-2"></div>
</div>