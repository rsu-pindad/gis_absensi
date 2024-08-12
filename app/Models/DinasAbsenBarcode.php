<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'otp_qr',
        'otp_input',
        'user_barcode_url',
        'user_barcode_img',
        'fingerprint',
        'devices_ip',
        'informasi_device',
        'informasi_os',
        'position',
        'presensi_masuk',
        'presensi_keluar'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
