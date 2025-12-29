<?php

namespace App\Helpers;

use Carbon\Carbon;

class ExportHelper
{
    /**
     * Generate Excel export dengan styling menggunakan HTML table format
     * File ini bisa dibuka langsung di Excel dengan preserve styling
     * 
     * @param \Illuminate\Support\Collection $rows
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public static function exportUpkpToExcel($rows, $filename = null)
    {
        $filename = $filename ?? 'laporan-upkp-' . date('Y-m-d') . '.xls';

        // Calculate totals
        $totalsSampah = [
            'masuk' => $rows->sum('sampah_masuk'),
            'diolah' => $rows->sum('sampah_diolah'),
            'belum' => $rows->sum('sampah_belum_diolah'),
        ];

        $totalsPilahan = [
            $rows->sum('hasil_pilahan_bahan_rdf'),
            $rows->sum('hasil_pilahan_bursam'),
            $rows->sum('hasil_pilahan_residu'),
            $rows->sum('hasil_pilahan_rongsok'),
        ];

        $totalsPengangkutan = [
            $rows->sum('pengangkutan_bahan_rdf'),
            $rows->sum('pengangkutan_bursam'),
            $rows->sum('pengangkutan_residu'),
            $rows->sum('pengangkutan_rongsok'),
        ];

        $totalsPemusnahan = [
            $rows->sum('pemusnahan_sampah_murni'),
            $rows->sum('pemusnahan_bahan_rdf'),
            $rows->sum('pemusnahan_residu'),
        ];

        // For timbunan, get last record per KSM
        $lastPerKsm = [];
        foreach ($rows as $row) {
            $key = $row->id_ksm;
            if (!isset($lastPerKsm[$key]) || $row->tanggal > $lastPerKsm[$key]->tanggal) {
                $lastPerKsm[$key] = $row;
            }
        }
        $lastRows = collect(array_values($lastPerKsm));

        $totalsTimbunan = [
            $lastRows->sum('timbunan_sampah_murni'),
            $lastRows->sum('timbunan_bahan_rdf'),
            $lastRows->sum('timbunan_residu'),
            $lastRows->sum('timbunan_rdf'),
            $lastRows->sum('timbunan_rongsok'),
            $lastRows->sum('timbunan_bursam'),
        ];

        $html = self::buildExcelHTML($rows, $totalsSampah, $totalsPilahan, $totalsPengangkutan, $totalsPemusnahan, $totalsTimbunan);

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Build HTML table with Excel-compatible styling
     */
    private static function buildExcelHTML($rows, $totalsSampah, $totalsPilahan, $totalsPengangkutan, $totalsPemusnahan, $totalsTimbunan)
    {
        $html = '<html>' . "\n";
        $html .= '<head>' . "\n";
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
        $html .= '</head>' . "\n";
        $html .= '<body>' . "\n";
        $html .= '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10pt;">' . "\n";

        // Header Row 1: Main categories with colspan
        $html .= '<tr>' . "\n";
        $html .= '<th rowspan="3" style="background-color: #E8E8E8; font-weight: bold; text-align: center; width: 60px;">No.</th>' . "\n";
        $html .= '<th rowspan="3" style="background-color: #E8E8E8; font-weight: bold; text-align: center; width: 250px;">Nama UPKP</th>' . "\n";
        $html .= '<th rowspan="3" style="background-color: #E8E8E8; font-weight: bold; text-align: center; width: 260px;">Tanggal</th>' . "\n";
        $html .= '<th rowspan="3" style="background-color: #E8E8E8; font-weight: bold; text-align: center; width: 250px;">Nama KSM</th>' . "\n";
        $html .= '<th colspan="3" style="background-color: #E8F5E9; font-weight: bold; text-align: center; font-size: 11pt;">Sampah</th>' . "\n";
        $html .= '<th colspan="4" style="background-color: #FFF9C4; font-weight: bold; text-align: center; font-size: 11pt;">Hasil Pilahan</th>' . "\n";
        $html .= '<th colspan="4" style="background-color: #E1F5FE; font-weight: bold; text-align: center; font-size: 11pt;">Pengangkutan</th>' . "\n";
        $html .= '<th colspan="3" style="background-color: #FFE0B2; font-weight: bold; text-align: center; font-size: 11pt;">Pemusnahan</th>' . "\n";
        $html .= '<th colspan="6" style="background-color: #F3E5F5; font-weight: bold; text-align: center; font-size: 11pt;">Timbunan</th>' . "\n";
        $html .= '</tr>' . "\n";

        // Header Row 2: Empty (for merged effect)
        $html .= '<tr>' . "\n";
        $html .= '<th colspan="3" style="background-color: #E8F5E9;"></th>' . "\n";
        $html .= '<th colspan="4" style="background-color: #FFF9C4;"></th>' . "\n";
        $html .= '<th colspan="4" style="background-color: #E1F5FE;"></th>' . "\n";
        $html .= '<th colspan="3" style="background-color: #FFE0B2;"></th>' . "\n";
        $html .= '<th colspan="6" style="background-color: #F3E5F5;"></th>' . "\n";
        $html .= '</tr>' . "\n";

        // Header Row 3: Sub-headers
        $html .= '<tr>' . "\n";
        $html .= '<th style="background-color: #E8F5E9; font-weight: bold; text-align: center; width: 120px;">Masuk</th>' . "\n";
        $html .= '<th style="background-color: #E8F5E9; font-weight: bold; text-align: center; width: 120px;">Diolah</th>' . "\n";
        $html .= '<th style="background-color: #E8F5E9; font-weight: bold; text-align: center; width: 120px;">Belum<br/>Diolah</th>' . "\n";
        $html .= '<th style="background-color: #FFF9C4; font-weight: bold; text-align: center; width: 120px;">Bahan<br/>RDF</th>' . "\n";
        $html .= '<th style="background-color: #FFF9C4; font-weight: bold; text-align: center; width: 120px;">Bursam</th>' . "\n";
        $html .= '<th style="background-color: #FFF9C4; font-weight: bold; text-align: center; width: 120px;">Residu</th>' . "\n";
        $html .= '<th style="background-color: #FFF9C4; font-weight: bold; text-align: center; width: 130px;">Rongsok</th>' . "\n";
        $html .= '<th style="background-color: #E1F5FE; font-weight: bold; text-align: center; width: 120px;">Bahan<br/>RDF</th>' . "\n";
        $html .= '<th style="background-color: #E1F5FE; font-weight: bold; text-align: center; width: 120px;">Bursam</th>' . "\n";
        $html .= '<th style="background-color: #E1F5FE; font-weight: bold; text-align: center; width: 120px;">Residu</th>' . "\n";
        $html .= '<th style="background-color: #E1F5FE; font-weight: bold; text-align: center; width: 130px;">Rongsok</th>' . "\n";
        $html .= '<th style="background-color: #FFE0B2; font-weight: bold; text-align: center; width: 120px;">Sampah<br/>Murni</th>' . "\n";
        $html .= '<th style="background-color: #FFE0B2; font-weight: bold; text-align: center; width: 120px;">Bahan<br/>RDF</th>' . "\n";
        $html .= '<th style="background-color: #FFE0B2; font-weight: bold; text-align: center; width: 120px;">Residu</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Sampah<br/>Murni</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Bahan<br/>RDF</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Residu</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">RDF</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 130px;">Rongsok</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Bursam</th>' . "\n";
        $html .= '</tr>' . "\n";

        // Data rows
        foreach ($rows as $index => $row) {
            $html .= '<tr>' . "\n";
            $html .= '<td style="text-align: center;">' . ($index + 1) . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . htmlspecialchars($row->upkp->nama_upkp ?? $row->id_upkp ?? '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . Carbon::parse($row->tanggal)->locale('id')->translatedFormat('d F Y') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . htmlspecialchars($row->ksm->nama_ksm ?? $row->id_ksm ?? '') . '</td>' . "\n";
            
            // Sampah
            $html .= '<td style="text-align: center;">' . number_format($row->sampah_masuk ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->sampah_diolah ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->sampah_belum_diolah ?? 0, 2, '.', '') . '</td>' . "\n";
            
            // Hasil Pilahan
            $html .= '<td style="text-align: center;">' . number_format($row->hasil_pilahan_bahan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->hasil_pilahan_bursam ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->hasil_pilahan_residu ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->hasil_pilahan_rongsok ?? 0, 2, '.', '') . '</td>' . "\n";
            
            // Pengangkutan
            $html .= '<td style="text-align: center;">' . number_format($row->pengangkutan_bahan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pengangkutan_bursam ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pengangkutan_residu ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pengangkutan_rongsok ?? 0, 2, '.', '') . '</td>' . "\n";
            
            // Pemusnahan
            $html .= '<td style="text-align: center;">' . number_format($row->pemusnahan_sampah_murni ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pemusnahan_bahan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pemusnahan_residu ?? 0, 2, '.', '') . '</td>' . "\n";
            
            // Timbunan
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_sampah_murni ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_bahan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_residu ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_rongsok ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_bursam ?? 0, 2, '.', '') . '</td>' . "\n";
            
            $html .= '</tr>' . "\n";
        }

        // Totals row
        $html .= '<tr style="background-color: #DEDEDE; font-weight: bold;">' . "\n";
        $html .= '<td colspan="4" style="text-align: center; font-weight: bold;">TOTALS</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsSampah['masuk'], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsSampah['diolah'], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsSampah['belum'], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPilahan[0], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPilahan[1], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPilahan[2], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPilahan[3], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPengangkutan[0], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPengangkutan[1], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPengangkutan[2], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPengangkutan[3], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPemusnahan[0], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPemusnahan[1], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPemusnahan[2], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[0], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[1], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[2], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[3], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[4], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[5], 2, '.', '') . '</td>' . "\n";
        $html .= '</tr>' . "\n";

        $html .= '</table>' . "\n";
        $html .= '</body>' . "\n";
        $html .= '</html>';

        return $html;
    }

    /**
     * Generate CSV export untuk laporan UPKP dengan format lengkap
     * 
     * @param \Illuminate\Support\Collection $rows
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public static function exportUpkpToCSV($rows, $filename = null)
    {
        $filename = $filename ?? 'laporan-upkp-' . date('Y-m-d') . '.csv';

        // Calculate totals
        $totalsSampah = [
            'masuk' => $rows->sum('sampah_masuk'),
            'diolah' => $rows->sum('sampah_diolah'),
            'belum' => $rows->sum('sampah_belum_diolah'),
        ];

        $totalsPilahan = [
            $rows->sum('hasil_pilahan_bahan_rdf'),
            $rows->sum('hasil_pilahan_bursam'),
            $rows->sum('hasil_pilahan_residu'),
            $rows->sum('hasil_pilahan_rongsok'),
        ];

        $totalsPengangkutan = [
            $rows->sum('pengangkutan_bahan_rdf'),
            $rows->sum('pengangkutan_bursam'),
            $rows->sum('pengangkutan_residu'),
            $rows->sum('pengangkutan_rongsok'),
        ];

        $totalsPemusnahan = [
            $rows->sum('pemusnahan_sampah_murni'),
            $rows->sum('pemusnahan_bahan_rdf'),
            $rows->sum('pemusnahan_residu'),
        ];

        // For timbunan, get last record per KSM
        $lastPerKsm = [];
        foreach ($rows as $row) {
            $key = $row->id_ksm;
            if (!isset($lastPerKsm[$key]) || $row->tanggal > $lastPerKsm[$key]->tanggal) {
                $lastPerKsm[$key] = $row;
            }
        }
        $lastRows = collect(array_values($lastPerKsm));

        $totalsTimbunan = [
            $lastRows->sum('timbunan_sampah_murni'),
            $lastRows->sum('timbunan_bahan_rdf'),
            $lastRows->sum('timbunan_residu'),
            $lastRows->sum('timbunan_rdf'),
            $lastRows->sum('timbunan_rongsok'),
            $lastRows->sum('timbunan_bursam'),
        ];

        // Build CSV with proper structure
        $csv = self::buildUpkpCSV($rows, $totalsSampah, $totalsPilahan, $totalsPengangkutan, $totalsPemusnahan, $totalsTimbunan);

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Build CSV content dengan struktur header yang proper
     */
    private static function buildUpkpCSV($rows, $totalsSampah, $totalsPilahan, $totalsPengangkutan, $totalsPemusnahan, $totalsTimbunan)
    {
        $csvData = [];

        // Header Row 1: Main categories
        $headerRow1 = [
            'No.', 'Nama UPKP', 'Tanggal', 'Nama KSM',
            'Sampah', '', '',
            'Hasil Pilahan', '', '', '',
            'Pengangkutan', '', '', '',
            'Pemusnahan', '', '',
            'Timbunan', '', '', '', '', ''
        ];
        $csvData[] = self::escapeRow($headerRow1);

        // Header Row 2: Empty for merged cells effect
        $headerRow2 = array_fill(0, count($headerRow1), '');
        $csvData[] = self::escapeRow($headerRow2);

        // Header Row 3: Sub-headers
        $headerRow3 = [
            '', '', '', '',
            'Masuk', 'Diolah', 'Belum Diolah',
            'Bahan RDF', 'Bursam', 'Residu', 'Rongsok',
            'Bahan RDF', 'Bursam', 'Residu', 'Rongsok',
            'Sampah Murni', 'Bahan RDF', 'Residu',
            'Sampah Murni', 'Bahan RDF', 'Residu', 'RDF', 'Rongsok', 'Bursam'
        ];
        $csvData[] = self::escapeRow($headerRow3);

        // Data rows
        foreach ($rows as $index => $row) {
            $dataRow = [
                $index + 1,
                $row->upkp->nama_upkp ?? $row->id_upkp ?? '',
                Carbon::parse($row->tanggal)->locale('id')->translatedFormat('d F Y'),
                $row->ksm->nama_ksm ?? $row->id_ksm ?? '',
                number_format($row->sampah_masuk ?? 0, 2, '.', ''),
                number_format($row->sampah_diolah ?? 0, 2, '.', ''),
                number_format($row->sampah_belum_diolah ?? 0, 2, '.', ''),
                number_format($row->hasil_pilahan_bahan_rdf ?? 0, 2, '.', ''),
                number_format($row->hasil_pilahan_bursam ?? 0, 2, '.', ''),
                number_format($row->hasil_pilahan_residu ?? 0, 2, '.', ''),
                number_format($row->hasil_pilahan_rongsok ?? 0, 2, '.', ''),
                number_format($row->pengangkutan_bahan_rdf ?? 0, 2, '.', ''),
                number_format($row->pengangkutan_bursam ?? 0, 2, '.', ''),
                number_format($row->pengangkutan_residu ?? 0, 2, '.', ''),
                number_format($row->pengangkutan_rongsok ?? 0, 2, '.', ''),
                number_format($row->pemusnahan_sampah_murni ?? 0, 2, '.', ''),
                number_format($row->pemusnahan_bahan_rdf ?? 0, 2, '.', ''),
                number_format($row->pemusnahan_residu ?? 0, 2, '.', ''),
                number_format($row->timbunan_sampah_murni ?? 0, 2, '.', ''),
                number_format($row->timbunan_bahan_rdf ?? 0, 2, '.', ''),
                number_format($row->timbunan_residu ?? 0, 2, '.', ''),
                number_format($row->timbunan_rdf ?? 0, 2, '.', ''),
                number_format($row->timbunan_rongsok ?? 0, 2, '.', ''),
                number_format($row->timbunan_bursam ?? 0, 2, '.', ''),
            ];
            $csvData[] = self::escapeRow($dataRow);
        }

        // Blank row
        $csvData[] = '';

        // Totals row
        $totalsRow = [
            '', 'TOTAL', '', '',
            number_format($totalsSampah['masuk'], 2, '.', ''),
            number_format($totalsSampah['diolah'], 2, '.', ''),
            number_format($totalsSampah['belum'], 2, '.', ''),
            number_format($totalsPilahan[0], 2, '.', ''),
            number_format($totalsPilahan[1], 2, '.', ''),
            number_format($totalsPilahan[2], 2, '.', ''),
            number_format($totalsPilahan[3], 2, '.', ''),
            number_format($totalsPengangkutan[0], 2, '.', ''),
            number_format($totalsPengangkutan[1], 2, '.', ''),
            number_format($totalsPengangkutan[2], 2, '.', ''),
            number_format($totalsPengangkutan[3], 2, '.', ''),
            number_format($totalsPemusnahan[0], 2, '.', ''),
            number_format($totalsPemusnahan[1], 2, '.', ''),
            number_format($totalsPemusnahan[2], 2, '.', ''),
            number_format($totalsTimbunan[0], 2, '.', ''),
            number_format($totalsTimbunan[1], 2, '.', ''),
            number_format($totalsTimbunan[2], 2, '.', ''),
            number_format($totalsTimbunan[3], 2, '.', ''),
            number_format($totalsTimbunan[4], 2, '.', ''),
            number_format($totalsTimbunan[5], 2, '.', ''),
        ];
        $csvData[] = self::escapeRow($totalsRow);

        // Add BOM for UTF-8 Excel compatibility
        return "\xEF\xBB\xBF" . implode("\n", $csvData);
    }

    /**
     * Escape and format a row for CSV
     */
    private static function escapeRow($row)
    {
        return implode(',', array_map(function($value) {
            if ($value === null || $value === '') return '';
            $str = (string) $value;
            // Escape quotes and wrap in quotes if contains comma, quote, or newline
            if (preg_match('/[",\n]/', $str)) {
                return '"' . str_replace('"', '""', $str) . '"';
            }
            return $str;
        }, $row));
    }

    /**
     * Generate HTML table untuk preview sebelum export
     */
    public static function generatePreviewHTML($rows)
    {
        // Calculate totals
        $totalsSampah = [
            'masuk' => $rows->sum('sampah_masuk'),
            'diolah' => $rows->sum('sampah_diolah'),
            'belum' => $rows->sum('sampah_belum_diolah'),
        ];

        $html = '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: Arial;">';
        
        // Headers
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f3f4f6;">';
        $html .= '<th rowspan="3">No.</th>';
        $html .= '<th rowspan="3">Nama UPKP</th>';
        $html .= '<th rowspan="3">Tanggal</th>';
        $html .= '<th rowspan="3">Nama KSM</th>';
        $html .= '<th colspan="3">Sampah</th>';
        $html .= '<th colspan="4">Hasil Pilahan</th>';
        $html .= '<th colspan="4">Pengangkutan</th>';
        $html .= '<th colspan="3">Pemusnahan</th>';
        $html .= '<th colspan="6">Timbunan</th>';
        $html .= '</tr>';
        
        $html .= '<tr style="background-color: #e5e7eb;">';
        $html .= '<th>Masuk</th><th>Diolah</th><th>Belum Diolah</th>';
        $html .= '<th>Bahan RDF</th><th>Bursam</th><th>Residu</th><th>Rongsok</th>';
        $html .= '<th>Bahan RDF</th><th>Bursam</th><th>Residu</th><th>Rongsok</th>';
        $html .= '<th>Sampah Murni</th><th>Bahan RDF</th><th>Residu</th>';
        $html .= '<th>Sampah Murni</th><th>Bahan RDF</th><th>Residu</th><th>RDF</th><th>Rongsok</th><th>Bursam</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        
        // Data rows
        $html .= '<tbody>';
        foreach ($rows as $index => $row) {
            $html .= '<tr>';
            $html .= '<td>' . ($index + 1) . '</td>';
            $html .= '<td>' . ($row->upkp->nama_upkp ?? '') . '</td>';
            $html .= '<td>' . Carbon::parse($row->tanggal)->locale('id')->translatedFormat('d F Y') . '</td>';
            $html .= '<td>' . ($row->ksm->nama_ksm ?? '') . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($row->sampah_masuk ?? 0, 2) . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($row->sampah_diolah ?? 0, 2) . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($row->sampah_belum_diolah ?? 0, 2) . '</td>';
            // Add more columns...
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        
        // Totals
        $html .= '<tfoot>';
        $html .= '<tr style="background-color: #f9fafb; font-weight: bold;">';
        $html .= '<td colspan="4" style="text-align: right;">TOTAL</td>';
        $html .= '<td style="text-align: right;">' . number_format($totalsSampah['masuk'], 2) . '</td>';
        $html .= '<td style="text-align: right;">' . number_format($totalsSampah['diolah'], 2) . '</td>';
        $html .= '<td style="text-align: right;">' . number_format($totalsSampah['belum'], 2) . '</td>';
        $html .= '<td colspan="17"></td>';
        $html .= '</tr>';
        $html .= '</tfoot>';
        
        $html .= '</table>';
        
        return $html;
    }

    /**
     * Generate Excel export untuk KSM dengan styling
     * 
     * @param \Illuminate\Support\Collection $rows
     * @param string $filename
     * @param string $ksmName
     * @return \Illuminate\Http\Response
     */
    public static function exportKsmToExcel($rows, $filename = null, $ksmName = 'KSM')
    {
        $filename = $filename ?? 'laporan-ksm-' . date('Y-m-d') . '.xls';

        // Calculate totals
        $totalsSampah = [
            'masuk' => $rows->sum('sampah_masuk'),
            'diolah' => $rows->sum('sampah_diolah'),
            'belum' => $rows->sum('sampah_belum_diolah'),
        ];

        $totalsPilahan = [
            $rows->sum('hasil_pilahan_bahan_rdf'),
            $rows->sum('hasil_pilahan_bursam'),
            $rows->sum('hasil_pilahan_residu'),
            $rows->sum('hasil_pilahan_rongsok'),
        ];

        $totalsPengangkutan = [
            $rows->sum('pengangkutan_bahan_rdf'),
            $rows->sum('pengangkutan_bursam'),
            $rows->sum('pengangkutan_residu'),
            $rows->sum('pengangkutan_rongsok'),
        ];

        $totalsPemusnahan = [
            $rows->sum('pemusnahan_sampah_murni'),
            $rows->sum('pemusnahan_bahan_rdf'),
            $rows->sum('pemusnahan_residu'),
        ];

        // For timbunan, use last record
        $lastRow = $rows->sortByDesc('tanggal')->first();
        $totalsTimbunan = [
            $lastRow->timbunan_sampah_murni ?? 0,
            $lastRow->timbunan_bahan_rdf ?? 0,
            $lastRow->timbunan_residu ?? 0,
            $lastRow->timbunan_rdf ?? 0,
            $lastRow->timbunan_rongsok ?? 0,
            $lastRow->timbunan_bursam ?? 0,
        ];

        $html = self::buildKsmExcelHTML($rows, $totalsSampah, $totalsPilahan, $totalsPengangkutan, $totalsPemusnahan, $totalsTimbunan, $ksmName);

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Build HTML table untuk KSM export
     */
    private static function buildKsmExcelHTML($rows, $totalsSampah, $totalsPilahan, $totalsPengangkutan, $totalsPemusnahan, $totalsTimbunan, $ksmName)
    {
        $html = '<html>' . "\n";
        $html .= '<head>' . "\n";
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
        $html .= '</head>' . "\n";
        $html .= '<body>' . "\n";
        $html .= '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10pt;">' . "\n";

        // Title row
        $html .= '<tr>' . "\n";
        $html .= '<th colspan="23" style="background-color: #2D5F3F; color: white; font-size: 14pt; padding: 12px; text-align: center;">LAPORAN RIWAYAT ' . strtoupper(htmlspecialchars($ksmName)) . '</th>' . "\n";
        $html .= '</tr>' . "\n";

        // Header Row 1: Main categories with colspan
        $html .= '<tr>' . "\n";
        $html .= '<th rowspan="3" style="background-color: #E8E8E8; font-weight: bold; text-align: center; width: 60px;">No.</th>' . "\n";
        $html .= '<th rowspan="3" style="background-color: #E8E8E8; font-weight: bold; text-align: center; width: 250px;">Nama UPKP</th>' . "\n";
        $html .= '<th rowspan="3" style="background-color: #E8E8E8; font-weight: bold; text-align: center; width: 230px;">Tanggal</th>' . "\n";
        $html .= '<th colspan="3" style="background-color: #E8F5E9; font-weight: bold; text-align: center; font-size: 11pt;">Sampah</th>' . "\n";
        $html .= '<th colspan="4" style="background-color: #FFF9C4; font-weight: bold; text-align: center; font-size: 11pt;">Hasil Pilahan</th>' . "\n";
        $html .= '<th colspan="4" style="background-color: #E1F5FE; font-weight: bold; text-align: center; font-size: 11pt;">Pengangkutan</th>' . "\n";
        $html .= '<th colspan="3" style="background-color: #FFE0B2; font-weight: bold; text-align: center; font-size: 11pt;">Pemusnahan</th>' . "\n";
        $html .= '<th colspan="6" style="background-color: #F3E5F5; font-weight: bold; text-align: center; font-size: 11pt;">Timbunan</th>' . "\n";
        $html .= '</tr>' . "\n";

        // Header Row 2: Empty (for merged effect)
        $html .= '<tr>' . "\n";
        $html .= '<th colspan="3" style="background-color: #E8F5E9;"></th>' . "\n";
        $html .= '<th colspan="4" style="background-color: #FFF9C4;"></th>' . "\n";
        $html .= '<th colspan="4" style="background-color: #E1F5FE;"></th>' . "\n";
        $html .= '<th colspan="3" style="background-color: #FFE0B2;"></th>' . "\n";
        $html .= '<th colspan="6" style="background-color: #F3E5F5;"></th>' . "\n";
        $html .= '</tr>' . "\n";

        // Header Row 3: Sub-headers
        $html .= '<tr>' . "\n";
        $html .= '<th style="background-color: #E8F5E9; font-weight: bold; text-align: center; width: 120px;">Masuk</th>' . "\n";
        $html .= '<th style="background-color: #E8F5E9; font-weight: bold; text-align: center; width: 120px;">Diolah</th>' . "\n";
        $html .= '<th style="background-color: #E8F5E9; font-weight: bold; text-align: center; width: 120px;">Belum<br/>Diolah</th>' . "\n";
        $html .= '<th style="background-color: #FFF9C4; font-weight: bold; text-align: center; width: 120px;">Bahan<br/>RDF</th>' . "\n";
        $html .= '<th style="background-color: #FFF9C4; font-weight: bold; text-align: center; width: 120px;">Bursam</th>' . "\n";
        $html .= '<th style="background-color: #FFF9C4; font-weight: bold; text-align: center; width: 120px;">Residu</th>' . "\n";
        $html .= '<th style="background-color: #FFF9C4; font-weight: bold; text-align: center; width: 120px;">Rongsok</th>' . "\n";
        $html .= '<th style="background-color: #E1F5FE; font-weight: bold; text-align: center; width: 120px;">Bahan<br/>RDF</th>' . "\n";
        $html .= '<th style="background-color: #E1F5FE; font-weight: bold; text-align: center; width: 120px;">Bursam</th>' . "\n";
        $html .= '<th style="background-color: #E1F5FE; font-weight: bold; text-align: center; width: 120px;">Residu</th>' . "\n";
        $html .= '<th style="background-color: #E1F5FE; font-weight: bold; text-align: center; width: 120px;">Rongsok</th>' . "\n";
        $html .= '<th style="background-color: #FFE0B2; font-weight: bold; text-align: center; width: 120px;">Sampah<br/>Murni</th>' . "\n";
        $html .= '<th style="background-color: #FFE0B2; font-weight: bold; text-align: center; width: 120px;">Bahan<br/>RDF</th>' . "\n";
        $html .= '<th style="background-color: #FFE0B2; font-weight: bold; text-align: center; width: 120px;">Residu</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Sampah<br/>Murni</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Bahan<br/>RDF</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Residu</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">RDF</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Rongsok</th>' . "\n";
        $html .= '<th style="background-color: #F3E5F5; font-weight: bold; text-align: center; width: 120px;">Bursam</th>' . "\n";
        $html .= '</tr>' . "\n";

        // Data rows
        foreach ($rows as $index => $row) {
            $html .= '<tr>' . "\n";
            $html .= '<td style="text-align: center;">' . ($index + 1) . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . htmlspecialchars($row->upkp->nama_upkp ?? $row->id_upkp ?? '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . Carbon::parse($row->tanggal)->locale('id')->translatedFormat('d F Y') . '</td>' . "\n";
            
            // Sampah
            $html .= '<td style="text-align: center;">' . number_format($row->sampah_masuk ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->sampah_diolah ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->sampah_belum_diolah ?? 0, 2, '.', '') . '</td>' . "\n";
            
            // Hasil Pilahan
            $html .= '<td style="text-align: center;">' . number_format($row->hasil_pilahan_bahan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->hasil_pilahan_bursam ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->hasil_pilahan_residu ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->hasil_pilahan_rongsok ?? 0, 2, '.', '') . '</td>' . "\n";
            
            // Pengangkutan
            $html .= '<td style="text-align: center;">' . number_format($row->pengangkutan_bahan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pengangkutan_bursam ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pengangkutan_residu ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pengangkutan_rongsok ?? 0, 2, '.', '') . '</td>' . "\n";
            
            // Pemusnahan
            $html .= '<td style="text-align: center;">' . number_format($row->pemusnahan_sampah_murni ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pemusnahan_bahan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->pemusnahan_residu ?? 0, 2, '.', '') . '</td>' . "\n";
            
            // Timbunan
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_sampah_murni ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_bahan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_residu ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_rdf ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_rongsok ?? 0, 2, '.', '') . '</td>' . "\n";
            $html .= '<td style="text-align: center;">' . number_format($row->timbunan_bursam ?? 0, 2, '.', '') . '</td>' . "\n";
            
            $html .= '</tr>' . "\n";
        }

        // Totals row
        $html .= '<tr style="background-color: #DEDEDE; font-weight: bold;">' . "\n";
        $html .= '<td colspan="3" style="text-align: center; font-weight: bold;">TOTALS</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsSampah['masuk'], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsSampah['diolah'], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsSampah['belum'], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPilahan[0], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPilahan[1], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPilahan[2], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPilahan[3], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPengangkutan[0], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPengangkutan[1], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPengangkutan[2], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPengangkutan[3], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPemusnahan[0], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPemusnahan[1], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsPemusnahan[2], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[0], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[1], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[2], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[3], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[4], 2, '.', '') . '</td>' . "\n";
        $html .= '<td style="text-align: center;">' . number_format($totalsTimbunan[5], 2, '.', '') . '</td>' . "\n";
        $html .= '</tr>' . "\n";

        $html .= '</table>' . "\n";
        $html .= '</body>' . "\n";
        $html .= '</html>';

        return $html;
    }
}
