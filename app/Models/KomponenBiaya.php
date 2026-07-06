<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenBiaya extends Model
{
    use HasFactory;
    
    protected $table = 'komponen_biaya';
    protected $fillable = ['nama', 'kode', 'deskripsi', 'is_active', 'metadata'];
    
    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    public function skemaKomponen()
    {
        return $this->hasMany(SkemaKomponen::class, 'komponen_biaya_id');
    }
}
