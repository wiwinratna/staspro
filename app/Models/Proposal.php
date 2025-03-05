<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $table    = 'proposal';
    protected $fillable = [
        'judul_proposal',
        'tgl_pengajuan',
        'file_proposal',
        'nama_pengaju',
        'anggaran_diajukan',
        'status',
        'id_project',
        'id_sumber_dana',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
