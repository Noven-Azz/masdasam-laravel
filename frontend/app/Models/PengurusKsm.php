<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PengurusKsm extends Model
{
    use HasUuids;

    protected $table = 'pengurus_ksm';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_ksm',
        'ketua_ksm',
        'sekretaris_ksm',
        'bendahara_ksm',
        'seksi_iuran_pengguna_ksm',
        'seksi_pengoperasian_dan_pemliharaan_ksm',
        'seksi_penyuluhan_kesehatan_ksm',
        'laki_laki',
        'perempuan',
    ];

    public $timestamps = true;

    public function ksm()
    {
        return $this->belongsTo(Ksm::class, 'id_ksm', 'id');
    }
}