<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table    = 'project';
    protected $fillable = [
        'jumlah_dana',
        'tahun',
        'nama_project',
        'id_sumber_dana',
        'durasi',
        'deskripsi',
        'file_proposal',
        'file_rab',
        'user_id_created',
        'user_id_updated',
    ];
}
