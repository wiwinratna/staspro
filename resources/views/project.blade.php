<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
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

        .card {
            background-color: #006400;
            height: 200px;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h3 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .text a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            background-color: #006400;
            border: 2px solid #006400;
            transition: 0.3s ease-in-out;
        }

        .text a:hover {
            background-color: white;
            color: #006400;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-end">
            @include('navbar')
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('project.index') }}" class="active">Project</a>
            <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
            <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Project</h1>

            @if ($message = Session::get('success'))
                <p class="text-success">{{ $message }}</p>
            @endif
            @if ($message = Session::get('error'))
                <p class="text-danger">{{ $message }}</p>
            @endif

            <div class="text mb-3">
                <a href="{{ route('project.create') }}"><span class="me-1">+</span> Input Project</a>
            </div>

            <div class="row g-2">
                @foreach ($project as $p)
                    <div class="col-md-3">
                        <div class="card">
                            <h3>{{ $p->nama_project }}</h3>
                            <a href="{{ route('project.show', $p->id) }}" class="text-white">More info &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
