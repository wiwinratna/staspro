<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        /* Navbar */
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

        /* Sidebar */
        .sidebar {
            background-color: #d9d9d9;
            height: 100vh;
            padding: 20px;
            width: 220px;
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #fff;
        }

        .main-content h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input::placeholder {
            color: #aaa;
            font-style: italic;
        }

        .submit-btn {
            background-color: #2AD000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .submit-btn:hover {
            background-color: #249C00;
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
            <a href="/project">Project</a>
            <a href="{{ route('requestpembelian.index') }}" class="active">Request Pembelian</a>
            <a href="/pencatatan_transaksi">Pencatatan Transaksi</a>
            <a href="/laporan_keuangan">Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Tambah Request Pembelian</h1>
            <div class="form-container">
                <form action="{{ route('requestpembelian.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="id_project">Tim Penelitian</label>
                        <select class="form-select" id="id_project" name="id_project">
                            <option value="" selected disabled> -> Pilih Tim Penelitian <- </option>
                                    @foreach ($project as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_project }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tgl_request">Tanggal Request</label>
                        <input type="date" id="tgl_request" name="tgl_request">
                    </div>
                    <button class="submit-btn mt-2">SUBMIT</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
