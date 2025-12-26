<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSubkategori extends Model
{
    use HasFactory;

    protected $table = 'detail_subkategori';
    protected $fillable = [
        'nominal',
        'realisasi_anggaran',
        'id_subkategori_sumberdana',
        'id_project',
        'user_id_created',
        'user_id_updated',
    ];

    public function subkategori()
    {
        return $this->belongsTo(SubkategoriSumberdana::class, 'id_subkategori_sumberdana');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }
}
