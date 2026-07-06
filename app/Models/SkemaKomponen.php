<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkemaKomponen extends Model
{
    use HasFactory;
    
    protected $table = 'skema_komponen';
    protected $fillable = ['skema_pendanaan_id', 'komponen_biaya_id', 'urutan', 'is_wajib'];
    
    protected $casts = [
        'is_wajib' => 'boolean',
    ];

    public function skemaPendanaan()
    {
        return $this->belongsTo(SkemaPendanaan::class, 'skema_pendanaan_id');
    }

    public function komponenBiaya()
    {
        return $this->belongsTo(KomponenBiaya::class, 'komponen_biaya_id');
    }
}
