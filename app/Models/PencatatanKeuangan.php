<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubkategoriSumberdana;

class PencatatanKeuangan extends Model
{
    use HasFactory;

    protected $table = 'pencatatan_keuangan';
    
    protected $fillable = [
        'tanggal',
        'project_id',
        'sub_kategori_pendanaan',
        'jenis_transaksi',
        'deskripsi_transaksi',
        'jumlah_transaksi',
        'metode_pembayaran',
        'bukti_transaksi',
        'request_pembelian_id',
        'is_talangan',
        'talangan_ref_type',
        'talangan_ref_id',
        'is_reclass',
        'reclass_group_id',
    ];

    protected $dates = ['tanggal'];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_transaksi' => 'integer',
        'is_talangan' => 'boolean',
        'is_reclass' => 'boolean',
    ];

    public function sumberDana() {
        return $this->belongsTo(Sumberdana::class, 'id_sumber_dana');
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
    
    public function getSubkategoriSumberDanaAttribute()
    {
        return $this->sub_kategori_pendanaan;
    }

    public function subKategoriPendanaan()
    {
        return $this->belongsTo(SubkategoriSumberdana::class, 'sub_kategori_pendanaan');
    }

    public function getTimPenelitianAttribute()
    {
        return $this->project ? $this->project->nama : null;
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriTransaksi::class, 'kategori_transaksi_id');
    }

    public function requestPembelian()
    {
        return $this->belongsTo(RequestpembelianHeader::class, 'request_pembelian_id');
    }
}
