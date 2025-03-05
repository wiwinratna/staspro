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
            height: 200px;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 5px;

        }

        .card h3 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .card p {
            margin: 0;
        }

        .text a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            position: relative;
            left: 10px;
            border: 2px solid #2AD000;
            padding: 5px 10px;
            border-radius: 20px;
            background-color: #2AD000;
        }

        .text a:hover {
            text-decoration: underline;
            background-color: white;
            color: #2AD000;
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
            <a href="/dashboard">Dashboard</a>
            <a href="/project"class="active">Project</a>
            <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
            <a href="/pencatatan_transaksi">Pencatatan Transaksi</a>
            <a href="/laporan_keuangan">Laporan Keuangan</a>
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
            <div class="text mt-10">
                <a href="{{ route('project.create') }}" class="px-3"><span class="me-1">+</span>Input Project</a>
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
