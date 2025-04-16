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
            padding: 20px;
            min-height: 100vh;
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar a:hover,
        .sidebar a.active {
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

        .card.no-hover:hover {
            transform: none !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-end">
            @include('navbar')
        </div>
    </nav>

    <div class="d-flex">
    <div class="sidebar">
        <a href="{{ route('dashboard') }}" class="active">Dashboard</a>
        <a href="{{ route('project.index') }}">Project</a>
        <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
        @if (Auth::user()->role == 'admin')
            <a href="{{ route('sumberdana.index') }}">Sumber Dana</a>
            <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
            <a href="{{ route('users.index') }}">Management User</a>
        @endif
    </div>

        <div class="container-fluid p-4">
            <h1 class="mb-4">Dashboard</h1>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

                @if (Auth::user()->role == 'admin')
                    <div class="col">
                        <a href="{{ route('pencatatan_transaksi') }}" class="text-decoration-none">
                            <div class="card h-100 text-white bg-success p-3 rounded-4 shadow">
                                <h5 class="fw-semibold">Total Transaksi Bulan Ini</h5>
                                <p class="fs-4">Rp {{ number_format($totalTransactions, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    </div>
                @endif

                <div class="col">
                    <a href="{{ route('project.index') }}" class="text-decoration-none">
                        <div class="card h-100 text-white bg-success p-3 rounded-4 shadow">
                            <h5 class="fw-semibold">Jumlah Project</h5>
                            <p class="fs-4">{{ $totalProjects }}</p>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="{{ route('requestpembelian.index') }}" class="text-decoration-none">
                        <div class="card h-100 text-white bg-success p-3 rounded-4 shadow">
                            <h5 class="fw-semibold">Total Request Pembelian</h5>
                            <p class="fs-4">{{ $totalRequests }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card no-hover" style="max-width: 1000px; margin: auto;">
                        <h5 class="fw-semibold text-white">Grafik Transaksi per Project</h5>
                        <canvas id="grafikPengeluaranProject"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxProject = document.getElementById('grafikPengeluaranProject').getContext('2d');
        const chartPengeluaran = new Chart(ctxProject, {
            type: 'bar',
            data: {
                labels: {!! json_encode($namaProjects) !!},
                datasets: [{
                    label: 'Total Transaksi (Rp)',
                    data: {!! json_encode($pengeluaranPerProject) !!},
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
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    },
                    y: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)',
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>