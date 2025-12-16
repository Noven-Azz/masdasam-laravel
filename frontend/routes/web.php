<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    // Simpan laporan KSM (schema baru inputan_data_sampah)
    Route::post('/laporan-ksm', function (Request $request) {
        $profile = session('profile') ?? [];
        $supabaseUser = session('supabase_user') ?? [];

        // reload profile jika session kosong
        if (empty($profile) && !empty($supabaseUser['id'])) {
            $p = \App\Models\Profile::where('user_id', $supabaseUser['id'])->first();
            if ($p) $profile = $p->toArray();
        }

        $idKsm = $profile['id_ksm'] ?? null;
        $idUpkp = $profile['id_upkp'] ?? null;

        // Fallback cari KSM dari user_id jika belum ada di session
        if (!$idKsm && !empty($supabaseUser['id'])) {
            $ksm = Ksm::where('user_id', $supabaseUser['id'])->first();
            if ($ksm) {
                $idKsm = $ksm->id;
                $idUpkp = $idUpkp ?? $ksm->id_upkp;
            }
        }

        if (!$idKsm) {
            return back()->withErrors(['auth' => 'Akun KSM tidak ditemukan.'])->withInput();
        }

        $rules = [
            'tanggal' => 'required|date',
            'sampah_masuk' => 'nullable|numeric',
            'sampah_diolah' => 'nullable|numeric',
            'sampah_belum_diolah' => 'nullable|numeric',

            'hasil_pilahan_bahan_rdf' => 'nullable|numeric',
            'hasil_pilahan_bursam' => 'nullable|numeric',
            'hasil_pilahan_residu' => 'nullable|numeric',
            'hasil_pilahan_rongsok' => 'nullable|numeric',

            'pengangkutan_bahan_rdf' => 'nullable|numeric',
            'pengangkutan_bursam' => 'nullable|numeric',
            'pengangkutan_residu' => 'nullable|numeric',
            'pengangkutan_rongsok' => 'nullable|numeric',

            'pemusnahan_sampah_murni' => 'nullable|numeric',
            'pemusnahan_bahan_rdf' => 'nullable|numeric',
            'pemusnahan_residu' => 'nullable|numeric',

            'timbunan_sampah_murni' => 'nullable|numeric',
            'timbunan_bahan_rdf' => 'nullable|numeric',
            'timbunan_residu' => 'nullable|numeric',
            'timbunan_rdf' => 'nullable|numeric',
            'timbunan_rongsok' => 'nullable|numeric',
            'timbunan_bursam' => 'nullable|numeric',
        ];

        $validated = $request->validate($rules);

        // Normalisasi nilai numeric null -> 0 (kecuali tanggal)
        foreach ($validated as $key => $val) {
            if ($key !== 'tanggal' && is_null($val)) {
                $validated[$key] = 0;
            }
        }

        $payload = array_merge($validated, [
            'id' => (string) Str::uuid(),
            'id_ksm' => $idKsm,
            'id_upkp' => $idUpkp,
            'sudah_verifikasi' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('inputan_data_sampah')->insert($payload);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Laporan tersimpan'], 201);
        }

        return redirect()->route('ksm.laporan-page')->with('success', 'Laporan berhasil dikirim.');
    })->name('ksm.laporan.store');

    Route::get('/report-history-ksm', function (Request $request) {
        $profile = session('profile') ?? [];
        $supabaseUser = session('supabase_user') ?? [];

        // reload profile jika session kosong
        if (empty($profile) && !empty($supabaseUser['id'])) {
            $p = \App\Models\Profile::where('user_id', $supabaseUser['id'])->first();
            if ($p) $profile = $p->toArray();
        }

        $idKsm = $profile['id_ksm'] ?? null;

        // Fallback cari KSM dari user_id
        if (!$idKsm && !empty($supabaseUser['id'])) {
            $ksm = Ksm::where('user_id', $supabaseUser['id'])->first();
            if ($ksm) $idKsm = $ksm->id;
        }

        // Filter by month/year if provided
        $month = $request->query('month');
        $year = $request->query('year');

        $query = InputanDataSampah::with(['ksm', 'upkp'])
            ->where('id_ksm', $idKsm)
            ->orderBy('tanggal', 'desc');

        if ($month && $year) {
            $query->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
        }

        $laporan = $query->get();

        return view('ksm.riwayat', ['laporan' => $laporan]);
    })->name('ksm.riwayat');

    Route::get('/detail-report-ksm/{id}', function ($id) {
        $laporan = InputanDataSampah::with(['ksm', 'upkp'])->findOrFail($id);

        $data = [
            'id' => $laporan->id,
            'nama_ksm' => $laporan->ksm->nama_ksm ?? '-',
            'nama_upkp' => $laporan->upkp->nama_upkp ?? '-',
            'tanggal_laporan' => $laporan->tanggal,
            'sampah_masuk' => $laporan->sampah_masuk,
            'sampah_diolah' => $laporan->sampah_diolah,
            'sampah_belum_diolah' => $laporan->sampah_belum_diolah,
            'sudah_verifikasi' => $laporan->sudah_verifikasi,
            'hasil_pilahan' => [
                'bahan_rdf' => $laporan->hasil_pilahan_bahan_rdf,
                'bursam' => $laporan->hasil_pilahan_bursam,
                'residu' => $laporan->hasil_pilahan_residu,
                'rongsok' => $laporan->hasil_pilahan_rongsok,
            ],
            'pengangkutan' => [
                'bahan_rdf' => $laporan->pengangkutan_bahan_rdf,
                'bursam' => $laporan->pengangkutan_bursam,
                'residu' => $laporan->pengangkutan_residu,
                'rongsok' => $laporan->pengangkutan_rongsok,
            ],
            'pemusnahan' => [
                'sampah_murni' => $laporan->pemusnahan_sampah_murni,
                'bahan_rdf' => $laporan->pemusnahan_bahan_rdf,
                'residu' => $laporan->pemusnahan_residu,
            ],
            'timbunan' => [
                'sampah_murni' => $laporan->timbunan_sampah_murni,
                'bahan_rdf' => $laporan->timbunan_bahan_rdf,
                'residu' => $laporan->timbunan_residu,
                'rdf' => $laporan->timbunan_rdf,
                'rongsok' => $laporan->timbunan_rongsok,
                'bursam' => $laporan->timbunan_bursam,
            ],
        ];

        return view('ksm.detail-report', ['data' => $data]);
    })->name('ksm.detail-report');

    // Update laporan KSM
    Route::put('/update-laporan/{id}', function (Request $request, $id) {
        $laporan = InputanDataSampah::findOrFail($id);
        
        // KSM tidak boleh edit jika sudah verifikasi
        if ($laporan->sudah_verifikasi) {
            return back()->withErrors(['error' => 'Laporan sudah diverifikasi, tidak bisa diedit.']);
        }

        $rules = [
            'tanggal' => 'required|date',
            'sampah_masuk' => 'nullable|numeric',
            'sampah_diolah' => 'nullable|numeric',
            'sampah_belum_diolah' => 'nullable|numeric',
            'hasil_pilahan_bahan_rdf' => 'nullable|numeric',
            'hasil_pilahan_bursam' => 'nullable|numeric',
            'hasil_pilahan_residu' => 'nullable|numeric',
            'hasil_pilahan_rongsok' => 'nullable|numeric',
            'pengangkutan_bahan_rdf' => 'nullable|numeric',
            'pengangkutan_bursam' => 'nullable|numeric',
            'pengangkutan_residu' => 'nullable|numeric',
            'pengangkutan_rongsok' => 'nullable|numeric',
            'pemusnahan_sampah_murni' => 'nullable|numeric',
            'pemusnahan_bahan_rdf' => 'nullable|numeric',
            'pemusnahan_residu' => 'nullable|numeric',
            'timbunan_sampah_murni' => 'nullable|numeric',
            'timbunan_bahan_rdf' => 'nullable|numeric',
            'timbunan_residu' => 'nullable|numeric',
            'timbunan_rdf' => 'nullable|numeric',
            'timbunan_rongsok' => 'nullable|numeric',
            'timbunan_bursam' => 'nullable|numeric',
        ];

        $validated = $request->validate($rules);

        foreach ($validated as $key => $val) {
            if ($key !== 'tanggal' && is_null($val)) {
                $validated[$key] = 0;
            }
        }

        $laporan->update($validated);

        return redirect()->route('ksm.detail-report', $id)->with('success', 'Laporan berhasil diupdate.');
    })->name('ksm.update-laporan');

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
                'status'     => $row->sudah_verifikasi ? 'Terverifikasi' : 'Pending',
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

    // Detail report UPKP
    Route::get('/detail-report/{id}', function ($id) {
        $laporan = InputanDataSampah::with(['ksm', 'upkp'])->findOrFail($id);

        $data = [
            'id' => $laporan->id,
            'nama_ksm' => $laporan->ksm->nama_ksm ?? '-',
            'nama_upkp' => $laporan->upkp->nama_upkp ?? '-',
            'tanggal_laporan' => $laporan->tanggal,
            'sampah_masuk' => $laporan->sampah_masuk,
            'sampah_diolah' => $laporan->sampah_diolah,
            'sampah_belum_diolah' => $laporan->sampah_belum_diolah,
            'sudah_verifikasi' => $laporan->sudah_verifikasi,
            'hasil_pilahan' => [
                'bahan_rdf' => $laporan->hasil_pilahan_bahan_rdf,
                'bursam' => $laporan->hasil_pilahan_bursam,
                'residu' => $laporan->hasil_pilahan_residu,
                'rongsok' => $laporan->hasil_pilahan_rongsok,
            ],
            'pengangkutan' => [
                'bahan_rdf' => $laporan->pengangkutan_bahan_rdf,
                'bursam' => $laporan->pengangkutan_bursam,
                'residu' => $laporan->pengangkutan_residu,
                'rongsok' => $laporan->pengangkutan_rongsok,
            ],
            'pemusnahan' => [
                'sampah_murni' => $laporan->pemusnahan_sampah_murni,
                'bahan_rdf' => $laporan->pemusnahan_bahan_rdf,
                'residu' => $laporan->pemusnahan_residu,
            ],
            'timbunan' => [
                'sampah_murni' => $laporan->timbunan_sampah_murni,
                'bahan_rdf' => $laporan->timbunan_bahan_rdf,
                'residu' => $laporan->timbunan_residu,
                'rdf' => $laporan->timbunan_rdf,
                'rongsok' => $laporan->timbunan_rongsok,
                'bursam' => $laporan->timbunan_bursam,
            ],
        ];

        return view('upkp.detail-report', ['data' => $data]);
    })->name('detail-report');

    // Update laporan UPKP
    Route::put('/update-laporan/{id}', function (Request $request, $id) {
        $laporan = InputanDataSampah::findOrFail($id);

        $rules = [
            'tanggal' => 'required|date',
            'sampah_masuk' => 'nullable|numeric',
            'sampah_diolah' => 'nullable|numeric',
            'sampah_belum_diolah' => 'nullable|numeric',
            'hasil_pilahan_bahan_rdf' => 'nullable|numeric',
            'hasil_pilahan_bursam' => 'nullable|numeric',
            'hasil_pilahan_residu' => 'nullable|numeric',
            'hasil_pilahan_rongsok' => 'nullable|numeric',
            'pengangkutan_bahan_rdf' => 'nullable|numeric',
            'pengangkutan_bursam' => 'nullable|numeric',
            'pengangkutan_residu' => 'nullable|numeric',
            'pengangkutan_rongsok' => 'nullable|numeric',
            'pemusnahan_sampah_murni' => 'nullable|numeric',
            'pemusnahan_bahan_rdf' => 'nullable|numeric',
            'pemusnahan_residu' => 'nullable|numeric',
            'timbunan_sampah_murni' => 'nullable|numeric',
            'timbunan_bahan_rdf' => 'nullable|numeric',
            'timbunan_residu' => 'nullable|numeric',
            'timbunan_rdf' => 'nullable|numeric',
            'timbunan_rongsok' => 'nullable|numeric',
            'timbunan_bursam' => 'nullable|numeric',
        ];

        $validated = $request->validate($rules);

        foreach ($validated as $key => $val) {
            if ($key !== 'tanggal' && is_null($val)) {
                $validated[$key] = 0;
            }
        }

        $laporan->update($validated);

        return redirect()->route('upkp.detail-report', $id)->with('success', 'Laporan berhasil diupdate.');
    })->name('update-laporan');

    // Verifikasi laporan
    Route::post('/verify-laporan/{id}', function ($id) {
        DB::table('inputan_data_sampah')
            ->where('id', $id)
            ->update(['sudah_verifikasi' => true, 'updated_at' => now()]);
        
        return response()->json(['success' => true]);
    })->name('verify-laporan');
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