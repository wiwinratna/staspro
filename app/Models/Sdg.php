<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sdg extends Model
{
    use HasFactory;

    protected $table = 'sdgs';

    protected $fillable = [
        'nomor',
        'nama',
        'warna'
    ];
}
