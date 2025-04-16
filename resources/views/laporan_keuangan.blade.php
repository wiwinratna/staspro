<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #006400;
            color: white;
        }

        .sidebar {
            background-color: #d9d9d9;
            padding: 20px;
            min-height: 100vh;
            width: 250px;
        }

        .sidebar a {
            display: block;
            color: #333;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 10px;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #006400;
            color: white;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .table th {
            background-color: #006400;
            color: white;
        }

        .filter-container select {
            width: 230px; 
            appearance: none;
            background-image: url('https://cdn-icons-png.flaticon.com/512/60/60995.png');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 15px;
            padding-right: 30px; 
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-end">
            @include('navbar')
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('project.index') }}">Project</a>
            <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
            @if (Auth::user()->role == 'admin')
                <a href="{{ route('sumberdana.index') }}">Sumber Dana</a>
                <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
                <a href="{{ route('laporan_keuangan') }}" class="active">Laporan Keuangan</a>
                <a href="{{ route('users.index') }}">Management User</a>
            @endif
        </div>

        <div class="container-fluid p-4">
            <h1 class="mb-4">Laporan Keuangan</h1>

            <!-- Filter Section -->
            <form method="GET" action="{{ route('laporan_keuangan') }}" id="filter-form">
                <label><strong>Filter Laporan Keuangan:</strong></label>
                <div class="filter-container">
                    <select name="tim_peneliti" class="form-control" id="tim_peneliti">
                        <option value="">Semua Tim Peneliti</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request()->tim_peneliti == $project->id ? 'selected' : '' }}>
                                {{ $project->nama_project }}
                            </option>
                        @endforeach
                    </select>

                    <select name="metode_pembayaran" class="form-control" id="metode_pembayaran">
                        <option value="">Semua Metode Pembayaran</option>
                        <option value="cash" {{ request()->metode_pembayaran == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="transfer bank" {{ request()->metode_pembayaran == 'transfer bank' ? 'selected' : '' }}>Transfer Bank</option>
                    </select>

                    <select name="sumber_dana" class="form-control" id="sumber_dana">
                        <option value="">Semua Sumber Dana</option>
                        <option value="internal" {{ request()->sumber_dana == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="eksternal" {{ request()->sumber_dana == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                    </select>
                </div>
            </form>

            <!-- Tombol Unduh -->
            <div class="download-buttons">
                <a href="{{ route('laporan.export', 'excel') }}?tim_peneliti={{ request('tim_peneliti') }}&metode_pembayaran={{ request('metode_pembayaran') }}&sumber_dana={{ request('sumber_dana') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Unduh Excel
                </a>
                <a href="{{ route('laporan.export', 'pdf') }}?tim_peneliti={{ request('tim_peneliti') }}&metode_pembayaran={{ request('metode_pembayaran') }}&sumber_dana={{ request('sumber_dana') }}" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                </a>
            </div>

            <h3 class="mt-4 fw-semibold">Laporan Keuangan Keseluruhan</h3>

            <!-- Tabel Laporan -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Tim Peneliti</th>
                            <th>Deskripsi Transaksi</th>
                            <th>Metode Pembayaran</th>
                            <th>Sumber Dana</th>
                            <th>Debit (Rp)</th>
                            <th>Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksis as $index => $transaksi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                            <td>{{ $transaksi->project->nama_project ?? '-' }}</td>
                            <td>{{ $transaksi->deskripsi_transaksi }}</td>
                            <td>{{ ucfirst($transaksi->metode_pembayaran) }}</td>
                            <td>{{ ucwords($transaksi->project->sumberDana->jenis_pendanaan ?? '-') }}</td>
                            <td>
                                @if($transaksi->jenis_transaksi == 'pemasukan')
                                    Rp. {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>
                                @if($transaksi->jenis_transaksi == 'pengeluaran')
                                    Rp. {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6">Total</th>
                            <th>Rp. {{ number_format($totalDebit, 0, ',', '.') }}</th>
                            <th>Rp. {{ number_format($totalKredit, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
            $(".filter-container select").on("change", function() {
                $("#filter-form").submit();
            });
        });
    </script>

    <!-- script chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- script bootstrap dropdown agar dropdown bisa berfungsi -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>