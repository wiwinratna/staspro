<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailproject extends Model
{
    use HasFactory;

    protected $table    = 'detail_project';
    protected $fillable = [
        'id_project',
        'id_user',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
