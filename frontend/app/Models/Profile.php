<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Profile extends Model
{
    use HasUuids;

    protected $table = 'profiles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'role',
        'id_upkp',
        'id_ksm',
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