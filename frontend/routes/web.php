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
        $ksm = Ksm::with('pengurusKsm', 'upkp')->first();
        return view('ksm.profil', compact('ksm'));
    })->name('ksm.profil');
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
        $rows = InputanDataSampah::with('ksm')->orderBy('tanggal', 'desc')->get();
        return view('upkp.riwayat', ['rows' => $rows]);
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