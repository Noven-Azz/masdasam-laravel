<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InputanDataSampah extends Model
{
    use HasUuids;

    protected $table = 'inputan_data_sampah';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_upkp',
        'id_ksm',
        'tanggal',
        'sampah_masuk',
        'hasil_pilahan_bahan_rdf',
        'hasil_pilahan_bursam',
        'hasil_pilahan_residu',
        'hasil_pilihan_rongsok',
        'pengangkutan_bahan_rdf',
        'pengangkutan_bursam',
        'pengangkutan_residu',
        'pengangkutan_rongsok',
        'pemusnahan_sampah_murni',
        'pemusnahan_bahan_rdf',
        'pemusnahan_residu',
        'timbunan_sampah_murni',
        'timbunan_bahan_rdf',
        'timbunan_residu',
        'timbunan_bursam',
        'timbunan_rdf',
        'timbunan_rongsok',
        'sudah_verifikasi',
        'penyusutan_jumlah',
        'sampah_diolah',
        'sampah_belum_diolah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'sudah_verifikasi' => 'boolean',
    ];

    public $timestamps = true;

    public function ksm()
    {
        return $this->belongsTo(Ksm::class, 'id_ksm', 'id');
    }

    public function upkp()
    {
        return $this->belongsTo(Upkp::class, 'id_upkp', 'id');
    }
}