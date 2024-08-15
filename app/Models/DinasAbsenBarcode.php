<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DinasAbsenBarcode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dinas_absen_barcode';

    protected $fillable = [
        'user_id',
        'petugas_id',
        'absensi_id',
        'user_barcode_url',
        'user_barcode_img',
        'otp_qr',
        'otp_input',
        'fingerprint',
        'devices_ip',
        'informasi_device',
        'informasi_os',
        'lotd_user_barcode_masuk',
        'latd_user_barcode_masuk',
        'lotd_user_barcode_keluar',
        'latd_user_barcode_keluar',
        'user_masuk',
        'user_keluar'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // protected $casts = [
    //     // 'informasi_device' => 'array',
    //     // 'informasi_os' => AsArrayObject::class,
    //     'presensi_masuk'  => 'datetime:H:i',
    //     'presensi_keluar' => 'datetime:H:i',
    // ];

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

    public static function scopeSearch($query, $value)
    {
        $query->whereHas('parentUser', function ($query) use ($value) {
            $query->where('npp', 'like', "%{$value}%");
        })->whereHas('parentPetugas', function ($query) use ($value) {
            $query->where('npp', 'like', "%{$value}%");
        })->whereHas('parentAbsensi', function ($query) use ($value) {
            $query->whereHas('parentLokasi', function ($query) use ($value) {
                $query->where('instansi', 'like', "%{$value}%");
            });
        });
    }
}
