<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubkategoriSumberdana;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    
    protected $fillable = [
        'tanggal',
        'project_id',
        'sub_kategori_pendanaan',
        'jenis_transaksi',
        'deskripsi_transaksi',
        'jumlah_transaksi',
        'metode_pembayaran',
        'bukti_transaksi'
    ];

    protected $dates = ['tanggal'];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_transaksi' => 'integer',
    ];

    public function sumberDana() {
        return $this->belongsTo(Sumberdana::class, 'id_sumber_dana');
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
    
    public function getSubkategoriSumberDanaAttribute()
    {
        return $this->sub_kategori_pendanaan; // Mengambil nilai dari kolom sub_kategori_pendanaan
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
