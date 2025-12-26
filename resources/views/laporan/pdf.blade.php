<!DOCTYPE html>
<html lang="id">
<head>
    @extends('layouts.app')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #006400; color: white; }
        .total-row { font-weight: bold; }
        .right-align { text-align: right; }
    </style>
</head>
<body>
<h2 style="text-align: center;">Laporan Keuangan</h2>

@if (!empty($filterInfo))
    <p style="text-align: center;">
    @foreach ($filterInfo as $key => $value)
        <strong>{{ $key }}:</strong> {{ ucfirst($value) }}<br>
    @endforeach
    </p>
@else
    <p style="text-align: center;">
        <strong>Periode:</strong> {{ $tanggal_awal }} - {{ $tanggal_akhir }}
    </p>
@endif

<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Tim Peneliti</th>
            <th>Deskripsi Transaksi</th>
            <th>Metode Pembayaran</th>
            <th>Sumber Dana</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_debit = 0;
            $total_kredit = 0;
            $saldo = 0;
        @endphp

        @if(isset($pencatatanKeuangans) && $pencatatanKeuangans->count() > 0)
            @foreach($pencatatanKeuangans as $index => $pencatatanKeuangan)
                @php
                    $debit = $pencatatanKeuangan->jenis_transaksi === 'pemasukan' ? $pencatatanKeuangan->jumlah_transaksi : 0;
                    $kredit = $pencatatanKeuangan->jenis_transaksi === 'pengeluaran' ? $pencatatanKeuangan->jumlah_transaksi : 0;

                    $total_debit += $debit;
                    $total_kredit += $kredit;
                    $saldo += ($debit - $kredit);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pencatatanKeuangan->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $pencatatanKeuangan->project->nama_project ?? '-' }}</td>
                    <td>{{ $pencatatanKeuangan->deskripsi_transaksi }}</td>
                    <td>{{ ucfirst($pencatatanKeuangan->metode_pembayaran) }}</td>
                    <td>{{ ucwords($pencatatanKeuangan->project->sumberDana->jenis_pendanaan ?? '-') }}</td>
                    <td class="right-align">
                        {!! $debit > 0 ? 'Rp&nbsp;' . number_format($debit, 0, ',', '.') : '-' !!}
                    </td>
                    <td class="right-align">
                        {!! $kredit > 0 ? 'Rp&nbsp;' . number_format($kredit, 0, ',', '.') : '-' !!}
                    </td>
                    <td class="right-align">
                        {!! 'Rp&nbsp;' . number_format($saldo, 0, ',', '.') !!}
                    </td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="6">Total</td>
                <td class="right-align">{!! 'Rp&nbsp;' . number_format($total_debit, 0, ',', '.') !!}</td>
                <td class="right-align">{!! 'Rp&nbsp;' . number_format($total_kredit, 0, ',', '.') !!}</td>
                <td class="right-align">{!! 'Rp&nbsp;' . number_format($saldo, 0, ',', '.') !!}</td>
            </tr>
        @else
            <tr>
                <td colspan="9" class="text-center">Tidak ada data transaksi.</td>
            </tr>
        @endif
    </tbody>
</table>
</body>
</html>
