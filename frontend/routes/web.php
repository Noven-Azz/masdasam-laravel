<?php

use Illuminate\Support\Facades\Route;

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