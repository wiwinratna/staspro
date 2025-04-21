<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestpembelianHeader extends Model
{
    use HasFactory;

    protected $table    = 'request_pembelian_header';
    protected $fillable = [
        'no_request',
        'tgl_request',
        'status_request',
        'keterangan_reject',
        'id_project',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'request_pembelian_id');
    }

    public function requestPembelianDetail()
    {
        return $this->hasMany(RequestpembelianDetail::class, 'id_request_pembelian_header');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }
}
