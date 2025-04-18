<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumber Dana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        /* Navbar */
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

        /* Sidebar */
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

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #006400;
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
            background-color: #006400;
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
        <div class="container-fluid d-flex justify-content-end">
            @include('navbar')
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('project.index') }}">Project</a>
    <a href="{{ route('requestpembelian.index') }}" class="active">Request Pembelian</a>
    @if (Auth::user()->role == 'admin')
        <a href="{{ route('sumberdana.index') }}">Sumber Dana</a>
        <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
        <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
        <a href="{{ route('users.index') }}">Management User</a>
    @endif
</div>

        <!-- Main Content -->
        <div class="main-content">
            <h1 class="mb-4" style="font-weight: bold; font-size: 2rem;">Tambah Sumber Dana</h1>
            <div class="form-container">
                <form action="{{ route('sumberdana.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_sumber_dana">Nama Sumber Dana</label>
                        <input type="text" id="nama_sumber_dana" name="nama_sumber_dana">
                    </div>
                    <div class="form-group">
                        <label for="jenis_pendanaan">Jenis Pendanaan</label>
                        <select class="form-select" id="jenis_pendanaan" name="jenis_pendanaan">
                            <option value="internal">
                                Internal</option>
                            <option value="eksternal">Eksternal</option>
                        </select>
                    </div>
                    <button class="submit-btn mt-2">SUBMIT</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
