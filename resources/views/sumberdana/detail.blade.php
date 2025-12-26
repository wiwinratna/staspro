<!DOCTYPE html>
<html lang="en">

<head>
    @extends('layouts.app')
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

        .text {
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .text a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            position: relative;
            left: 10px;
            border: 2px solid #006400;
            padding: 5px 10px;
            border-radius: 20px;
            background-color: #006400;
        }

        .text a:hover {
            text-decoration: underline;
            background-color: white;
            color: #006400;
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
    <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
    @if (Auth::user()->role == 'admin')
        <a href="{{ route('sumberdana.index') }}" class="active">Sumber Dana</a>
        <a href="{{ route('pencatatan_keuangan') }}">Pencatatan Keuangan</a>
        <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
        <a href="{{ route('users.index') }}">Management User</a>
    @endif
</div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Tambah Sumber Dana</h1>
            <div class="text mt-10">
                <a href="{{ route('sumberdana.index') }}" class="px-3"><span class="me-1"><- </span>Lihat Sumber
                            Dana</a>
            </div>
            <div class="form-container">
                <div class="form-group">
                    <label for="nama_sumber_dana">Nama Sumber Dana</label>
                    <input type="text" id="nama_sumber_dana" name="nama_sumber_dana"
                        value="{{ $sumberdana_header->nama_sumber_dana }}" disabled>
                </div>
                <div class="form-group">
                    <label for="jenis_pendanaan">Jenis Pendanaan</label>
                    <select class="form-select" id="jenis_pendanaan" name="jenis_pendanaan" disabled>
                        <option value="internal"
                            {{ $sumberdana_header->jenis_pendanaan == 'internal' ? 'selected' : '' }}>
                            Internal</option>
                        <option value="eksternal"
                            {{ $sumberdana_header->jenis_pendanaan == 'eksternal' ? 'selected' : '' }}>
                            Eksternal</option>
                    </select>
                </div>
            </div>
            <div>
                <h3 class="mt-4 mb-3">Subkategori Sumber Dana</h3>
                
                @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Tambah Subkategori Baru</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sumberdana.storedetail') }}" method="POST" class="row g-3 align-items-end">
                            @csrf
                            <input type="hidden" name="id_sumberdana" value="{{ $sumberdana_header->id }}">
                            <div class="col-md-8">
                                <label for="nama" class="form-label">Nama Subkategori</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                    placeholder="Contoh: Bahan, Peralatan Pendukung, Biaya Langsung" value="{{ old('nama') }}">
                                <small class="text-muted">Nama subkategori akan digunakan untuk klasifikasi dana</small>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="submit-btn w-100">Tambah Subkategori</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Daftar Subkategori</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="80%">Nama Subkategori</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($detail) > 0)
                                    @foreach ($detail as $d)
                                        <tr>
                                            <td>{{ $d->nama }}</td>
                                            <td>
                                                <a href="{{ route('sumberdana.destroydetail', $d->id) }}"
                                                    class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus subkategori ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center">Belum ada subkategori untuk sumber dana ini</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>

</html>
