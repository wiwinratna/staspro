<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #2AD000;
            color: white;
        }

        .navbar .profile {
            display: flex;
            align-items: center;
        }

        .navbar .profile img {
            border-radius: 50%;
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }

        .sidebar {
            background-color: #d9d9d9;
            height: 100vh;
            padding: 20px;
        }

        .sidebar a {
            text-decoration: none;
            display: block;
            color: #333;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #e2e2e2;
        }

        .sidebar a.active {
            background-color: #2AD000;
            color: white;
        }

        .card {
            background-color: #2AD000;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 20px;
        }

        .card h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .card p {
            margin: 0;
        }
    </style>
</head>

<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">LOGO</a>
            @include('navbar')
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="{{ route('dashboard') }}" class="active">Dashboard</a>
            <a href="{{ route('project.index') }}">Project</a>
            <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
            <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Dashboard</h1>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card">
                        <h3>10</h3>
                        <p>Request yang Belum Disetujui</p>
                        <a href="#" class="text-white">More info &rarr;</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <h3>Rp. xxx</h3>
                        <p>Total Transaksi Bulan Ini</p>
                        <a href="#" class="text-white">More info &rarr;</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <h3>{{ $project }}</h3>
                        <p>Jumlah Tim Project</p>
                        <a href="#" class="text-white">More info &rarr;</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <h3>{{ $request_pembelian }}</h3>
                        <p>Request Pembelian</p>
                        <a href="#" class="text-white">More info &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
