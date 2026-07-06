<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisProject extends Model
{
    use HasFactory;
    
    protected $table = 'jenis_project';
    protected $fillable = ['nama', 'kode', 'deskripsi', 'is_active'];

    public function skemaPendanaan()
    {
        return $this->hasMany(SkemaPendanaan::class, 'jenis_project_id');
    }
}
