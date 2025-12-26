<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasTransaction extends Model
{
    use HasFactory;

    protected $table = 'kas_transactions';

    protected $fillable = [
        'tanggal',
        'tipe',
        'kategori',
        'project_id',
        'nominal',
        'deskripsi',
        'created_by',
    ];
}
