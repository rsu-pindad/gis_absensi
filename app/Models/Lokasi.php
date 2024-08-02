<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lokasi';

    protected $fillable = [
        'lotd',
        'latd',
        'instansi',
        'alamat'
    ];

    protected $guarded = 'id';

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function scopeSearch($query, $value)
    {
        $query->where('instansi', 'like', "%{$value}%");
    }
}
