<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSubkategori extends Model
{
    use HasFactory;

    protected $table    = 'detail_subkategori';
    protected $fillable = [
        'nominal',
        'realisasi_anggaran',
        'id_subkategori_sumberdana',
        'id_project',
        'user_id_created',
        'user_id_updated',
    ];
}
