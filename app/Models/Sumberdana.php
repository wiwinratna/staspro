<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sumberdana extends Model
{
    use HasFactory;

    protected $table    = 'sumber_dana';
    protected $fillable = [
        'tipe_project',
        'nama_sumber_dana',
        'jenis_pendanaan',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
