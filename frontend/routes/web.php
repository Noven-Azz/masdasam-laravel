<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\SupabaseCallbackController;
use App\Models\Ksm;
use App\Models\Upkp;
use App\Models\InputanDataSampah;

// Public routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/lupa-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// Supabase authentication callback
Route::post('/auth/supabase/callback', [SupabaseCallbackController::class, 'handle'])
    ->name('auth.supabase.callback');

// Logout
Route::post('/logout', function () {
    session()->flush();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes - KSM
|--------------------------------------------------------------------------
*/
Route::middleware('web')->group(function () {
    Route::get('/', function (Request $request) {
        return view('ksm.laporan');
    })->name('ksm.laporan');

    Route::get('/laporan-ksm', function (Request $request) {
        return view('ksm.laporan');
    })->name('ksm.laporan-page');

    Route::get('/report-history-ksm', function (Request $request) {
        $laporanHistory = InputanDataSampah::orderBy('tanggal', 'desc')->get();
        return view('ksm.riwayat', ['laporan' => $laporanHistory]);
    })->name('ksm.riwayat');

    Route::get('/profil-ksm', function (Request $request) {
        $ksm = Ksm::with(['pengurusKsm', 'upkp'])->first();

        $pengurus = [];
        $tenagaPria = 0;
        $tenagaWanita = 0;

        if ($ksm && $ksm->pengurusKsm) {
            $pk = $ksm->pengurusKsm;
            $map = [
                'Ketua' => $pk->ketua_ksm,
                'Sekretaris' => $pk->sekretaris_ksm,
                'Bendahara' => $pk->bendahara_ksm,
                'Seksi Iuran Pengguna' => $pk->seksi_iuran_pengguna_ksm,
                'Seksi Pengoperasian & Pemliharaan' => $pk->seksi_pengoperasian_dan_pemliharaan_ksm,
                'Seksi Penyuluhan Kesehatan' => $pk->seksi_penyuluhan_kesehatan_ksm,
            ];
            foreach ($map as $jabatan => $nama) {
                if (!empty($nama)) {
                    $pengurus[] = ['jabatan' => $jabatan, 'nama' => $nama];
                }
            }
            $tenagaPria = $pk->laki_laki ?? 0;
            $tenagaWanita = $pk->perempuan ?? 0;
        }

        return view('ksm.profil', compact('ksm', 'pengurus', 'tenagaPria', 'tenagaWanita'));
    })->name('ksm.profil');s
});

/*
|--------------------------------------------------------------------------
| Protected Routes - UPKP
|--------------------------------------------------------------------------
*/
Route::middleware('web')->prefix('upkp')->name('upkp.')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $stats = [
            'total_ksm' => Ksm::count(),
            'laporan_masuk' => InputanDataSampah::whereMonth('tanggal', date('m'))->count(),
            'total_sampah_m3' => InputanDataSampah::whereMonth('tanggal', date('m'))->sum('sampah_masuk') ?? 0,
        ];
        return view('upkp.dashboard', ['stats' => $stats]);
    })->name('dashboard');

    Route::get('/konfirmasi', function (Request $request) {
        $laporanData = InputanDataSampah::with('ksm', 'upkp')->orderBy('tanggal', 'desc')->get();
        return view('upkp.konfirmasi', ['laporanData' => $laporanData]);
    })->name('konfirmasi');

    Route::get('/riwayat', function (Request $request) {
        $rowsEloquent = InputanDataSampah::with('ksm', 'upkp')
            ->orderBy('tanggal', 'desc')
            ->get();

        $rows = $rowsEloquent->map(function ($row) {
            return [
                'id'         => $row->id,
                'nama'       => $row->ksm->nama_ksm ?? '-',
                'nama_upkp'  => $row->upkp->nama_upkp ?? '-',
                'tanggal'    => $row->tanggal,
                'total'      => $row->sampah_masuk,
                'status'     => $row->status === 'verified' ? 'Terverifikasi' : 'Pending',
            ];
        });

        $listKsm = $rows->pluck('nama')->unique()->values();

        $stats = [
            'total_ksm' => Ksm::count(),
        ];
        $statsToday = [
            'laporan_masuk'   => InputanDataSampah::whereDate('tanggal', today())->count(),
            'total_sampah_m3' => InputanDataSampah::whereDate('tanggal', today())->sum('sampah_masuk') ?? 0,
        ];

        $selectedRange = [
            'start' => $request->query('start'),
            'end'   => $request->query('end'),
        ];
        $headerDateLabel = 'Semua';
        $upkpName = null;

        return view('upkp.riwayat', compact(
            'rows',
            'listKsm',
            'stats',
            'statsToday',
            'selectedRange',
            'headerDateLabel',
            'upkpName'
        ));
    })->name('riwayat');
});

/*
|--------------------------------------------------------------------------
| Protected Routes - DLH
|--------------------------------------------------------------------------
*/
Route::middleware('web')->get('/dashboard-dlh', function (Request $request) {
    $upkpStats = Upkp::withCount('ksmList')->get();
    $totalStats = [
        'total_upkp' => Upkp::count(),
        'total_ksm' => Ksm::count(),
        'total_laporan_bulan_ini' => InputanDataSampah::whereMonth('tanggal', date('m'))->count(),
        'total_sampah_bulan_ini' => InputanDataSampah::whereMonth('tanggal', date('m'))->sum('sampah_masuk') ?? 0,
    ];
    return view('dlh.dashboard', compact('upkpStats', 'totalStats'));
})->name('dlh.dashboard');