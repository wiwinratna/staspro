<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pencatatan Transaksi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
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
            <a href="{{ route('pencatatan_transaksi') }}" class="active">Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Pencatatan Transaksi</h1>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <h3>{{ number_format($totalNominal ?? 0, 2) }}</h3>
                    <p>Total Nominal Transaksi Bulan Ini</p>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <form class="d-flex align-items-center" method="GET" action="{{ route('filter_transaksi') }}">
                    <label for="startDate" class="me-2">Mulai:</label>
                    <input type="date" id="startDate" class="form-control me-3" name="start_date" required>
                    <label for="endDate" class="me-2">Sampai:</label>
                    <input type="date" id="endDate" class="form-control me-3" name="end_date" required>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>

                <button class="btn btn-success" onclick="window.location.href='/form_input_transaksi'">
                    Tambah Pencatatan Transaksi
                </button>
            </div>

            <!-- Tabel Transaksi dengan text-center -->
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Jenis Transaksi</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Metode Pembayaran</th>
                        <th>Kategori</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksis as $index => $transaksi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                            <td>{{ $transaksi->jenis_transaksi }}</td>
                            <td>{{ $transaksi->deskripsi_transaksi }}</td>
                            <td>{{ number_format($transaksi->jumlah_transaksi, 2) }}</td>
                            <td>{{ $transaksi->metode_pembayaran }}</td>
                            <td>{{ $transaksi->kategori_transaksi }}</td>
                            <td>
                                @if($transaksi->bukti_transaksi)
                                    <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#modalBukti{{ $transaksi->id }}">
                                    Lihat Bukti
                                    </button>

                                    <!-- Modal untuk menampilkan bukti transaksi -->
                                    <div class="modal fade" id="modalBukti{{ $transaksi->id }}" tabindex="-1" aria-labelledby="modalBuktiLabel{{ $transaksi->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalBuktiLabel{{ $transaksi->id }}">Bukti Transaksi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" alt="Bukti Transaksi" class="img-fluid">
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                @else
                                    -
                                @endif
                                </td>
                            <td>
                                <a href="{{ route('transaksi.edit', $transaksi->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
