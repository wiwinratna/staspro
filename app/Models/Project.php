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
        'id_sumber_dana',

        // status lama
        'status',
        'closed_at',
        'closed_by',

        // ✅ workflow baru
        'workflow_status',
        'ketua_id',
        'submitted_at',
        'approved_at',
        'funded_at',
        'finalized_at',

        // audit
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

    public function funding()
    {
        return $this->hasMany(\App\Models\ProjectFunding::class, 'project_id');
    }
    public function ketua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }

}
