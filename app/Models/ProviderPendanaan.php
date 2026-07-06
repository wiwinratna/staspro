<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderPendanaan extends Model
{
    use HasFactory;
    
    protected $table = 'provider_pendanaan';
    protected $fillable = ['nama', 'singkatan', 'deskripsi', 'is_active'];

    public function skemaPendanaan()
    {
        return $this->hasMany(SkemaPendanaan::class, 'provider_id');
    }
}
