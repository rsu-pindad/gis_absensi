<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DinasAbsen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dinas_absen';

    protected $fillable = [
        'user_id',
        'petugas_id',
        'absensi_id',
        'otp',
        'devices_ip',
        'informasi_device',
        'informasi_os',
        // 'informasi_os->name',
        // 'informasi_os->version',
        // 'position->lotd',
        // 'position->lotd',
        'position',
        'presensi_masuk',
        'presensi_keluar'
    ];

    // protected $guarded = ['id'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function parentPetugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id', 'id');
    }

    public function parentAbsensi(): BelongsTo
    {
        return $this->belongsTo(Absensi::class, 'absensi_id', 'id');
    }

    public function scopeSearch($query, $value)
    {
        $query->where('absensi_id', 'like', "%{$value}%");
    }
}
