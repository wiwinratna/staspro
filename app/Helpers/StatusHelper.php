<?php

namespace App\Helpers;

/**
 * StatusHelper — Pusat mapping label status bahasa Indonesia.
 *
 * Value di DB tetap bahasa Inggris, class ini hanya mengkonversi
 * label tampilan agar user melihat bahasa Indonesia yang konsisten.
 */
class StatusHelper
{
    /**
     * Label workflow status project (pengajuan project).
     */
    public const WORKFLOW = [
        'submitted' => 'Diajukan',
        'approved'  => 'Disetujui',
        'rejected'  => 'Ditolak',
        'funded'    => 'Dana Cair',
        'finalized' => 'Final',
        'unknown'   => 'Tidak Diketahui',
    ];

    /**
     * Label status pengajuan transaksi (pengajuan dana / reimbursement).
     */
    public const TRANSAKSI = [
        'submit'  => 'Diajukan',
        'approve' => 'Disetujui',
        'bukti'   => 'Bukti Diupload',
        'done'    => 'Selesai',
        'reject'  => 'Ditolak',
    ];

    /**
     * Label status pengajuan komponen (request pembelian).
     */
    public const KOMPONEN = [
        'draft'           => 'Draft / Belum Diajukan',
        'submit_request'  => 'Dalam Proses Pemesanan',
        'approve_request' => 'Menunggu Verifikasi Final',
        'reject_request'  => 'Ditolak',
        'submit_payment'  => 'Menunggu Finalisasi',
        'approve_payment' => 'Terverifikasi Final',
        'reject_payment'  => 'Perlu Revisi Pembelian',
        'done'            => 'Selesai',
    ];

    /**
     * Ambil label workflow status.
     */
    public static function workflow(string $status): string
    {
        return self::WORKFLOW[strtolower($status)] ?? ucfirst($status);
    }

    /**
     * Ambil label status transaksi.
     */
    public static function transaksi(string $status): string
    {
        return self::TRANSAKSI[strtolower($status)] ?? strtoupper($status);
    }

    /**
     * Ambil label status komponen.
     */
    public static function komponen(string $status): string
    {
        return self::KOMPONEN[strtolower($status)] ?? ucwords(str_replace('_', ' ', $status));
    }
}
