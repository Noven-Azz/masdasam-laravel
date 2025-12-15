<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Ksm extends Model
{
    use HasUuids;

    protected $table = 'ksm';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'id_upkp',
        'nama_ksm',
        'no_hp',
        'alamat',
        'kelurahan',
        'kecamatan',
    ];

    public $timestamps = true;

    public function upkp()
    {
        return $this->belongsTo(Upkp::class, 'id_upkp', 'id');
    }

    public function pengurusKsm()
    {
        return $this->hasOne(PengurusKsm::class, 'id_ksm', 'id');
    }

    public function inputanDataSampah()
    {
        return $this->hasMany(InputanDataSampah::class, 'id_ksm', 'id');
    }
}