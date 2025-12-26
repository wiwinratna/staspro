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
        'bukti_bayar',
        'id_request_pembelian_header',
        'id_subkategori_sumberdana',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
    
    public function subkategori()
    {
        return $this->belongsTo(SubkategoriSumberdana::class, 'id_subkategori_sumberdana');
    }
    
    public function header()
    {
        return $this->belongsTo(RequestpembelianHeader::class, 'id_request_pembelian_header');
    }
}
