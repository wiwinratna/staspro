<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rab extends Model
{
    use HasFactory;

    protected $table    = 'rab';
    protected $fillable = [
        'judul_rab',
        'tgl_pengajuan',
        'file_rab',
        'nama_pengaju',
        'anggaran_diajukan',
        'status',
        'id_project',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
