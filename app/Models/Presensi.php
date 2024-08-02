<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presensi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'presensi';

    protected $guarded = 'id';

    protected $fillable = [
        'presensi_masuk',
        'presensi_keluar'
    ];

    protected $hidden = [
        'created_ad',
        'updated_ad',
        'deleted_ad',
    ];

    public function parentAbsensi() : BelongsTo
    {
        return $this->belongsTo(Absensi::class, 'absensi_id', 'id');
    }

    public function parentUser() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
