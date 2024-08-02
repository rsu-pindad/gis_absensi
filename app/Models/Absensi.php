<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'absensi';

    protected $guarded = 'id';

    protected $fillable = [
        'lokasi_id',
        'tanggal',
        'mulai',
        'selesai'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function parentLokasi() : BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }

    public function scopeSearch($query, $value)
    {
        $query->where('lokasi_id', 'like', "%{$value}%");
    }


}
