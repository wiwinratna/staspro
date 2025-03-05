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
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
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
            <a href="/project" class="active">Project</a>
            <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
            <a href="/pencatatan_transaksi">Pencatatan Transaksi</a>
            <a href="/laporan_keuangan">Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Project</h1>
            @if ($message = Session::get('success'))
                <p class="text-success">{{ $message }}</p>
            @endif
            @if ($message = Session::get('error'))
                <p class="text-danger">{{ $message }}</p>
            @endif
            <div class="text mt-10">
                <a href="{{ route('project.index') }}" class="px-3"><span class="me-1">
                        < </span>Project</a>
            </div>
            <div class="form-container">
                <form action="{{ route('project.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="nama_project">Nama Project</label>
                        <input type="text" id="nama_project" name="nama_project" placeholder="Cth: E-Sniffer">
                    </div>
                    <div class="form-group">
                        <label for="tahun">Tahun</label>
                        <input type="text" id="tahun" name="tahun" placeholder="Cth: 2024">
                    </div>
                    <div class="form-group">
                        <label for="durasi">Durasi Project</label>
                        <input type="text" id="durasi" name="durasi" placeholder="Cth: 1 Bulan/ Tahun ">
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" placeholder="Cth: Project"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="file_proposal">File Proposal (PDF)</label>
                        <input type="file" id="file_proposal" name="file_proposal" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="file_rab">File RAB (XLSX)</label>
                        <input type="file" id="file_rab" name="file_rab" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="sumber_dana">Sumber Dana</label>
                        <select id="sumber_dana" name="sumber_dana">
                            <option value="internal" selected>Internal</option>
                            <option value="external">External</option>
                            <option value="tambah_sumber">Tambah Sumber Dana</option>
                        </select>
                    </div>
                    <div id="internal">
                        <label>Kategori Pendanaan Internal</label>
                        @foreach ($sumber_internal as $s)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kategori_pendanaan"
                                    id="internal{{ $s->id }}" value="{{ $s->id }}">
                                <label class="form-check-label" for="internal{{ $s->id }}">
                                    {{ $s->nama_sumber_dana }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div id="external" style="display: none">
                        <label>Kategori Pendanaan Eksternal</label>
                        @foreach ($sumber_eksternal as $s)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kategori_pendanaan"
                                    id="eksternal{{ $s->id }}" value="{{ $s->id }}">
                                <label class="form-check-label" for="eksternal{{ $s->id }}">
                                    {{ $s->nama_sumber_dana }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <label for="jumlah_dana">Jumlah Dana</label>
                        <input type="text" id="jumlah_dana" name="jumlah_dana" placeholder="Cth: Rp. 1.000.000"
                            readonly>
                    </div>
                    <button class="submit-btn mt-2">SUBMIT</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('sumber_dana').addEventListener('change', function(e) {
            let value = e.target.value;
            const internal = document.getElementById('internal');
            const external = document.getElementById('external');
            if (value == 'external') {
                internal.style.display = 'none';
                external.style.display = 'block';
            } else if (value == 'internal') {
                external.style.display = 'none';
                internal.style.display = 'block';
            } else {
                window.location.href = "{{ route('sumberdana.create') }}";
            }
        })

        document.querySelectorAll('input[name="kategori_pendanaan"]').forEach(function(e) {
            e.addEventListener('change', function(e) {
                fetch(`{{ route('sumberdana.show', ':id') }}`.replace(':id', e.target.value))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('jumlah_dana').value = data.anggaran_maksimal;
                    })
            })
        })
    </script>
</body>

</html>
