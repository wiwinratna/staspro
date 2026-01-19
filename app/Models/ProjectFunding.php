<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFunding extends Model
{
    protected $table = 'project_funding'; // 🔴 penting (singular)

    protected $fillable = [
        'project_id',
        'tanggal',
        'nominal',
        'sumber_dana',
        'keterangan',
        'bukti',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
