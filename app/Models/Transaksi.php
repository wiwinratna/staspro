<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    
    protected $fillable = [
        'tanggal',
        'jenis_transaksi',
        'deskripsi_transaksi',
        'jumlah_transaksi',
        'metode_pembayaran',
        'kategori_transaksi',
        'sub_kategori',
        'sub_sub_kategori',
        'bukti_transaksi',
    ];

    protected $dates = ['tanggal'];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_transaksi' => 'integer',
    ];
}