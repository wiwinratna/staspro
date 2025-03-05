<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestpembelianDetail extends Model
{
    use HasFactory;

    protected $table    = 'request_pembelian_detail';
    protected $fillable = [
        'nama_barang',
        'kuantitas',
        'harga',
        'link_pembelian',
        'id_request_pembelian_header',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
