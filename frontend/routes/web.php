<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::get('/lupa-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/', function () {
    return view('ksm.laporan');
});

Route::get('/laporan-ksm', function () {
    return view('ksm.laporan');
});

Route::get('/report-history-ksm', function () {
    return view('ksm.riwayat');
});

Route::get('/detail-report-ksm/{id}', function ($id) {
    // Nanti bisa buat view detail
    return "Detail Report ID: $id";
});

Route::get('/dashboard-upkp', function () {
    return view('upkp.dashboard', ['roleName' => 'UPKP']);
});

Route::get('/dashboard-dlh', function () {
    return view('dlh.dashboard', ['roleName' => 'DLH']);
});

Route::post('/logout', function () {
    // Handle logout logic
    return redirect('/login');
})->name('logout');

Route::get('/profil-ksm', function () {
    // Mock data untuk testing UI (nanti ganti dengan data dari database/API)
    $profile = [
        'nama_ksm' => 'KSM AJIBARANG',
        'no_hp' => '081234567890',
        'alamat' => 'Jl. Contoh No. 123',
        'kelurahan' => 'Ajibarang',
        'kecamatan' => 'Ajibarang',
        'upkp' => [
            'nama_upkp' => 'UPKP Ajibarang'
        ],
        'pengurus_ksm' => [
            'laki_laki' => 5,
            'perempuan' => 3,
        ]
    ];

    $pengurus = [
        ['jabatan' => 'Ketua KSM', 'nama' => 'Budi Santoso'],
        ['jabatan' => 'Sekretaris KSM', 'nama' => 'Ani Wijaya'],
        ['jabatan' => 'Bendahara KSM', 'nama' => 'Citra Dewi'],
        ['jabatan' => 'Seksi Iuran', 'nama' => 'Dedi Kurniawan'],
        ['jabatan' => 'Seksi Penyuluhan Kesehatan', 'nama' => 'Eka Putri'],
        ['jabatan' => 'Seksi Operasional & Pemeliharaan', 'nama' => 'Fajar Ramadan'],
    ];

    return view('ksm.profil', compact('profile', 'pengurus'));
});

Route::get('/konfirmasi-upkp', function () {
    // Mock data (nanti ganti dengan fetch dari database)
    $laporanData = [
        [
            'id' => 1,
            'nama_ksm' => 'KSM AJIBARANG',
            'nama_upkp' => 'UPKP Ajibarang',
            'tanggal_laporan' => '2025-11-01',
            'sampah_masuk' => 2.5,
            'sudah_verifikasi' => true
        ],
        [
            'id' => 2,
            'nama_ksm' => 'KSM PURWOKERTO',
            'nama_upkp' => 'UPKP Ajibarang',
            'tanggal_laporan' => '2025-11-02',
            'sampah_masuk' => 1.8,
            'sudah_verifikasi' => false
        ],
        [
            'id' => 3,
            'nama_ksm' => 'KSM SOKARAJA',
            'nama_upkp' => 'UPKP Ajibarang',
            'tanggal_laporan' => '2025-11-03',
            'sampah_masuk' => 3.2,
            'sudah_verifikasi' => false
        ],
    ];

    $profile = [
        'name' => 'Admin UPKP Ajibarang',
        'role' => 'UPKP'
    ];

    return view('upkp.konfirmasi', compact('laporanData', 'profile'));
});

Route::get('/konfirmasi-upkp/detail/{id}', function ($id) {
    // Nanti buat view detail
    return "Detail Konfirmasi ID: $id";
});

Route::get('/riwayat-upkp', function (Illuminate\Http\Request $request) {
    // Mock data (nanti ganti dengan fetch dari database)
    $start = $request->get('start', date('Y-m-01'));
    $end = $request->get('end', date('Y-m-d'));

    $stats = [
        'total_ksm' => 5,
        'laporan_masuk' => 12,
        'total_sampah_m3' => 15.75,
    ];

    $statsToday = [
        'laporan_masuk' => 3,
        'total_sampah_m3' => 4.25,
    ];

    $rows = [
        ['id' => 1, 'nama' => 'KSM AJIBARANG', 'nama_upkp' => 'UPKP Ajibarang', 'tanggal' => '15 Oktober 2025', 'total' => '2.5 m³', 'status' => 'Terverifikasi'],
        ['id' => 2, 'nama' => 'KSM PURWOKERTO', 'nama_upkp' => 'UPKP Ajibarang', 'tanggal' => '14 Oktober 2025', 'total' => '1.8 m³', 'status' => 'Belum Verifikasi'],
        ['id' => 3, 'nama' => 'KSM SOKARAJA', 'nama_upkp' => 'UPKP Ajibarang', 'tanggal' => '13 Oktober 2025', 'total' => '3.2 m³', 'status' => 'Terverifikasi'],
    ];

    $listKsm = array_unique(array_column($rows, 'nama'));
    $upkpName = 'UPKP Ajibarang';
    $headerDateLabel = "$start - $end";
    $selectedRange = compact('start', 'end');

    return view('upkp.riwayat', compact('stats', 'statsToday', 'rows', 'listKsm', 'upkpName', 'headerDateLabel', 'selectedRange'));
});

Route::get('/riwayat-upkp/detail/{id}', function ($id) {
    return "Detail Riwayat ID: $id";
});