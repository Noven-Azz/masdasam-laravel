<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Upkp extends Model
{
    use HasUuids;

    protected $table = 'upkp';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'nama_upkp',
        'nama_operator',
        'no_hp_operator',
    ];

    public $timestamps = true;

    public function ksmList()
    {
        return $this->hasMany(Ksm::class, 'id_upkp', 'id');
    }

    public function inputanDataSampah()
    {
        return $this->hasMany(InputanDataSampah::class, 'id_upkp', 'id');
    }
}