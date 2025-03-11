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
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid d-flex justify-content-end">
            @include('navbar')
        </div>
    </nav>

    <div class="d-flex">
        <div class="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="active"><i class="fa-solid fa-home"></i> Dashboard</a>
            <a href="{{ route('project.index') }}"><i class="fa-solid fa-folder"></i> Project</a>
            <a href="{{ route('requestpembelian.index') }}"><i class="fa-solid fa-cart-plus"></i> Request Pembelian</a>
            <a href="{{ route('pencatatan_transaksi') }}"><i class="fa-solid fa-book"></i> Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}"><i class="fa-solid fa-file-invoice"></i> Laporan Keuangan</a>
        </div>

        <div class="container mt-4">
            <h2 class="mb-4">Dashboard Admin</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card h-100 d-flex flex-column justify-content-center">
                        <h5 class="fw-semibold">Request yang Belum Disetujui</h5>
                        <p class="fs-4">{{ $pendingRequests }}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 d-flex flex-column justify-content-center">
                        <h5 class="fw-semibold">Total Transaksi Bulan Ini</h5>
                        <p class="fs-4">Rp {{ number_format($totalTransactions, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 d-flex flex-column justify-content-center">
                        <h5 class="fw-semibold">Jumlah Tim Project</h5>
                        <p class="fs-4">{{ $totalTeams }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card h-100 d-flex flex-column justify-content-center">
                        <h5 class="fw-semibold">Total Request Pembelian</h5>
                        <p class="fs-4">{{ $totalRequests }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('chartTransaksi').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Transaksi'],
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: [{{ $totalTransactions }}],
                    backgroundColor: 'rgba(255, 255, 255, 0.5)',
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255, 255, 255, 0.2)' }
                    },
                    y: {
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255, 255, 255, 0.2)' }
                    }
                }
            }
        });
    </script>
</body>
</html>
