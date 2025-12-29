# Export Helper - Dokumentasi Penggunaan

## Overview

`ExportHelper` adalah utility class untuk export data laporan UPKP ke format CSV yang optimal untuk Excel.

## Features

-   Export data ke CSV dengan format multi-header yang rapi
-   Perhitungan otomatis untuk totals (sampah, pilahan, pengangkutan, pemusnahan, timbunan)
-   UTF-8 BOM encoding untuk compatibility dengan Excel
-   Format angka dengan 2 desimal
-   Tanggal dalam format Indonesia (dd MMMM yyyy)

## Cara Penggunaan

### 1. Import Helper

```php
use App\Helpers\ExportHelper;
```

### 2. Export Data ke CSV

```php
// Ambil data dari database
$rows = InputanDataSampah::with('ksm', 'upkp')
    ->where('sudah_verifikasi', true)
    ->orderBy('tanggal', 'desc')
    ->get();

// Export ke CSV
return ExportHelper::exportUpkpToCSV($rows, 'nama-file-optional.csv');
```

### 3. Contoh Penggunaan di Route

```php
Route::get('/export-laporan', function (Request $request) {
    $rows = InputanDataSampah::with('ksm', 'upkp')
        ->where('sudah_verifikasi', true)
        ->get();

    $filename = 'laporan-' . date('Y-m-d') . '.csv';

    return ExportHelper::exportUpkpToCSV($rows, $filename);
});
```

### 4. Export dengan Filter

```php
Route::get('/export-by-date', function (Request $request) {
    $query = InputanDataSampah::with('ksm', 'upkp')
        ->where('sudah_verifikasi', true);

    // Filter tanggal
    if ($request->filled('start') && $request->filled('end')) {
        $query->whereBetween('tanggal', [$request->start, $request->end]);
    }

    // Filter KSM
    if ($request->filled('ksm_id')) {
        $query->where('id_ksm', $request->ksm_id);
    }

    $rows = $query->orderBy('tanggal', 'desc')->get();

    return ExportHelper::exportUpkpToCSV($rows);
});
```

## Format Output CSV

### Struktur Header

```
Row 1: No. | Nama UPKP | Tanggal | Nama KSM | Sampah | | | Hasil Pilahan | ...
Row 2: (kosong untuk merged cell effect)
Row 3: | | | | Masuk | Diolah | Belum Diolah | Bahan RDF | Bursam | ...
```

### Kolom Data

1. **Metadata**: No, Nama UPKP, Tanggal, Nama KSM
2. **Sampah**: Masuk, Diolah, Belum Diolah
3. **Hasil Pilahan**: Bahan RDF, Bursam, Residu, Rongsok
4. **Pengangkutan**: Bahan RDF, Bursam, Residu, Rongsok
5. **Pemusnahan**: Sampah Murni, Bahan RDF, Residu
6. **Timbunan**: Sampah Murni, Bahan RDF, Residu, RDF, Rongsok, Bursam

### Baris Total

-   Otomatis dihitung di bawah data
-   Timbunan menggunakan record terakhir per KSM (sesuai logic bisnis)

## Tips untuk Excel

### Membuka File CSV di Excel dengan Formatting

1. Buka Excel
2. Pilih **Data > From Text/CSV**
3. Pilih file CSV yang didownload
4. Excel akan otomatis detect UTF-8 encoding
5. Klik **Load**

### Adjust Column Width Otomatis

Setelah membuka file:

1. Select semua kolom (Ctrl+A)
2. Double-click border antar column header
3. Atau: Home > Format > AutoFit Column Width

### Format Number Cells

1. Select kolom numeric (kolom E sampai X)
2. Right-click > Format Cells
3. Pilih **Number** dengan 2 decimal places

## Kustomisasi

### Menambahkan Kolom Baru

Edit method `buildUpkpCSV` di `ExportHelper.php`:

```php
// Tambahkan di $headerRow3
$headerRow3 = [
    // ...existing headers
    'Kolom Baru'
];

// Tambahkan di data row
$dataRow = [
    // ...existing data
    $row->kolom_baru ?? 0,
];
```

### Mengubah Format Angka

Edit method `buildUpkpCSV`:

```php
// Dari 2 desimal ke 0 desimal
number_format($value, 0, '.', '')

// Dengan thousand separator
number_format($value, 2, '.', ',')
```

## Troubleshooting

### File tidak bisa dibuka di Excel

-   Pastikan file memiliki BOM UTF-8 (`\xEF\xBB\xBF`)
-   Cek Content-Type header: `text/csv; charset=UTF-8`

### Angka tidak ter-format dengan benar

-   Gunakan `.` sebagai decimal separator (bukan `,`)
-   Jangan gunakan thousand separator di CSV

### Karakter Indonesia tidak tampil benar

-   Pastikan BOM UTF-8 ada di awal file
-   Buka file dengan Excel (bukan Notepad)

## Maintenance

Lokasi file: `app/Helpers/ExportHelper.php`

Update logic totals atau format sesuai kebutuhan bisnis baru.
