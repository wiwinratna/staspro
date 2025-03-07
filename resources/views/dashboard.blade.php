<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: url('https://www.toptal.com/designers/subtlepatterns/patterns/dot-grid.png');
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background-color: #006400;
        }

        .sidebar {
            background-color: #d9d9d9;
            height: 100vh;
            padding: 20px;
            width: 250px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 5px;
            transition: background 0.3s ease-in-out;
            text-decoration: none;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #006400;
            color: white;
        }

        .card {
            background: linear-gradient(135deg, #28a745, #006400);
            color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid d-flex justify-content-end">
            @include('navbar')
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="{{ route('dashboard') }}" class="active"><i class="fa-solid fa-home"></i> Dashboard</a>
            <a href="{{ route('project.index') }}"><i class="fa-solid fa-folder"></i> Project</a>
            <a href="{{ route('requestpembelian.index') }}"><i class="fa-solid fa-cart-plus"></i> Request Pembelian</a>
            <a href="{{ route('pencatatan_transaksi') }}"><i class="fa-solid fa-book"></i> Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}"><i class="fa-solid fa-file-invoice"></i> Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Dashboard</h1>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <h3>10</h3>
                        <p>Request yang Belum Disetujui</p>
                        <a href="#" class="text-white">More info &rarr;</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <h3>0</h3>
                        <p>Total Transaksi Bulan Ini</p>
                        <a href="#" class="text-white">More info &rarr;</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <h3>{{ $project }}</h3>
                        <p>Jumlah Tim Project</p>
                        <a href="#" class="text-white">More info &rarr;</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center">
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
