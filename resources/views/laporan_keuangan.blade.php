<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .card {
            background-color: #006400;
            color: white;
            border-radius: 10px;
            padding: 20px;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
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
            <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}" class="active">Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h1 class="mb-4">Laporan Keuangan</h1>

            <!-- Filter Section -->
            <form method="GET" action="{{ route('laporan_keuangan') }}">
                <div class="filter-container">
                    <select class="form-select" name="timPenelitian">
                        <option value="">Tim Penelitian</option>
                        <option value="smart_manequin">Smart Manequin</option>
                        <option value="automation_weapon_rack">Automation Weapon Rack</option>
                    </select>

                    <select class="form-select" name="kategoriPendanaan">
                        <option value="">Kategori Pendanaan</option>
                        <option value="Internal">Internal</option>
                        <option value="Eksternal">Eksternal</option>
                    </select>
                    
                    <button type="submit" class="btn btn-success">Filter</button>
                    <a href="{{ route('laporan.export.excel', request()->all()) }}" class="btn btn-success">Unduh Excel</a>
                </div>
            </form>

            <h3 class="mt-4">Laporan Keuangan Keseluruhan</h3>

            <!-- Tabel Laporan -->
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Tim Peneliti</th>
                            <th>Jenis Transaksi</th>
                            <th>Deskripsi Transaksi</th>
                            <th>Jumlah Transaksi</th>
                            <th>Metode Pembayaran</th>
                            <th>Kategori Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksis as $index => $transaksi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                            <td>{{ $transaksi->tim_penelitian ?? '-' }}</td>
                            <td>{{ $transaksi->jenis_transaksi }}</td>
                            <td>{{ $transaksi->deskripsi_transaksi }}</td>
                            <td>{{ number_format($transaksi->jumlah_transaksi, 2) }}</td>
                            <td>{{ $transaksi->metode_pembayaran }}</td>
                            <td>{{ $transaksi->kategori_transaksi }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h4>Total Nominal: {{ number_format($totalNominal ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>
</body>
</html>
