<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Project extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'project';

    protected $fillable = [
        'nama_project',
        'tahun',
        'durasi',
        'deskripsi',
        'file_proposal',
        'file_rab',
        'kategori_pendanaan',
        'id_sumber_dana',
        'realisasi_anggaran',
        'user_id_created',
        'user_id_updated',
    ];

    public function sumberDana(): BelongsTo
    {
        return $this->belongsTo(Sumberdana::class, 'id_sumber_dana');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_created');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_updated');
    }

    public function requestPembelianHeader()
    {
        return $this->hasMany(RequestpembelianHeader::class)->onDelete('cascade');
    }
}
