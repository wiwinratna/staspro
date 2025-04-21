<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailproject extends Model
{
    use HasFactory;

    protected $table    = 'detail_project';
    protected $fillable = [
        'id_project',
        'id_user',
        'user_id_created',
        'user_id_updated',
        'rincian_anggaran',
        'realisasi_anggaran',
        'sisa_anggaran',
        'subkategori_sumberdana_id',
        'updated_at',
    ];    

    public function subkategori()
    {
        return $this->belongsTo(SubkategoriSumberdana::class, 'subkategori_sumberdana_id');
    }
}
