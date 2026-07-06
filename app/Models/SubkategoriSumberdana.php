<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubkategoriSumberdana extends Model
{
    use HasFactory;

    protected $table    = 'subkategori_sumberdana';
    protected $fillable = [
        'nama',
        'nama_form',
        'id_sumberdana',
        'komponen_biaya_id',
        'user_id_created',
        'user_id_updated',
    ];

    public function komponenBiaya()
    {
        return $this->belongsTo(KomponenBiaya::class, 'komponen_biaya_id');
    }
}
