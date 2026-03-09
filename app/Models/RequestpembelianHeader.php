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
        'is_talangan',
        'status_alokasi',
        'keterangan_reject',
        'biaya_admin_transfer',
        'nominal_final_total',
        'nominal_penambahan',
        'nominal_pengurangan',
        'keterangan_penambahan',
        'keterangan_pengurangan',
        'bukti_transfer',
        'invoice_pembelian',
        'id_project',
        'project_id_alokasi_final',
        'tanggal_alokasi_final',
        'catatan_alokasi',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];

    protected $casts = [
        'is_talangan' => 'boolean',
        'tanggal_alokasi_final' => 'date',
    ];

    public function pencatatanKeuangans()
    {
        return $this->hasMany(PencatatanKeuangan::class, 'request_pembelian_id');
    }
}
