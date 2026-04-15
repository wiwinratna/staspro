<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanTransaksiHeader extends Model
{
    protected $table = 'pengajuan_transaksi_header';

    protected $fillable = [
        'no_request',
        'id_project',
        'id_subkategori_sumberdana',
        'tipe',
        'jenis_transaksi',
        'deskripsi',
        'kuantitas',
        'harga_satuan',
        'estimasi_nominal',
        'tgl_request',
        'nama_bank',
        'no_rekening',
        'tgl_cair',
        'nominal_disetujui',
        'nominal_final',
        'biaya_admin',
        'metode_pembayaran',
        'bukti_transfer',
        'tgl_bukti',
        'nominal_realisasi',
        'bukti_file',
        'status',
        'keterangan_reject',
        'pencatatan_keuangan_id',
        'is_talangan',
        'status_alokasi',
        'project_id_alokasi_final',
        'tanggal_alokasi_final',
        'catatan_alokasi',
        'user_id_created',
        'user_id_updated',
    ];

    // (Optional) relasi - biar blade nggak error
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'id_project');
    }

    public function subKategoriSumberDana()
    {
        // sesuaikan class model subkategori kamu:
        return $this->belongsTo(\App\Models\SubkategoriSumberDana::class, 'id_subkategori_sumberdana');
    }
}
