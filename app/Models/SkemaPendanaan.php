<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkemaPendanaan extends Model
{
    use HasFactory;
    
    protected $table = 'skema_pendanaan';
    protected $fillable = [
        'kode', 'nama', 'jenis_project_id', 'jenis_pendanaan_id', 'provider_id', 'deskripsi', 'is_active'
    ];

    public function jenisProject()
    {
        return $this->belongsTo(JenisProject::class, 'jenis_project_id');
    }

    public function jenisPendanaan()
    {
        return $this->belongsTo(JenisPendanaan::class, 'jenis_pendanaan_id');
    }

    public function provider()
    {
        return $this->belongsTo(ProviderPendanaan::class, 'provider_id');
    }

    public function komponen()
    {
        return $this->hasMany(SkemaKomponen::class, 'skema_pendanaan_id')->orderBy('urutan');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'skema_pendanaan_id');
    }
}
