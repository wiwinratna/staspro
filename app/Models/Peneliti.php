<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peneliti extends Model
{
    protected $table = 'peneliti';
    protected $fillable = [
        'nama_peneliti',
        'posisi',
    ];
}
