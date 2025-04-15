<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <a href="{{ route('project.index') }}" class="active">Project</a>
            <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
            @if (Auth::user()->role == 'admin')
                <a href="{{ route('sumberdana.index') }}">Sumber Dana</a>
                <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
                <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
            @endif
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Project</h1>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">{{ $message }}</div>
            @endif
            @if ($message = Session::get('error'))
                <div class="alert alert-danger">{{ $message }}</div>
            @endif
            <div class="text mt-10">
                <a href="{{ route('project.index') }}" class="px-3"><span class="me-1">&lt;</span> Project</a>
            </div>
            <div class="form-container">
            <form action="{{ isset($project) ? route('project.update', $project->id) : route('project.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($project))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="nama_project">Nama Project</label>
                    <input type="text" id="nama_project" name="nama_project" placeholder="Cth: E-Sniffer"
                        value="{{ old('nama_project', isset($project) ? $project->nama_project : '') }}" required>
                </div>

                <div class="form-group">
                    <label for="tahun">Tahun</label>
                    <input type="number" id="tahun" name="tahun" required class="form-control" placeholder="Contoh: 2025" min="2015" max="2030"
                        value="{{ old('tahun', isset($project) ? $project->tahun : '') }}">
                </div>

                <div class="form-group">
                    <label for="durasi">Durasi Project</label>
                    <input type="text" id="durasi" name="durasi" placeholder="Cth: 1 Bulan/ Tahun"
                        value="{{ old('durasi', isset($project) ? $project->durasi : '') }}" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" placeholder="Cth: Project" required>{{ old('deskripsi', isset($project) ? $project->deskripsi : '') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="file_proposal">File Proposal (PDF)</label>
                    <input type="file" id="file_proposal" name="file_proposal" class="form-control" accept=".pdf"
                        @if (!isset($project)) required @endif>
                    @if(isset($project) && $project->file_proposal)
                        <small>File saat ini: <a href="{{ asset('storage/' . $project->file_proposal) }}" target="_blank">Lihat Proposal</a></small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="file_rab">File RAB (XLSX)</label>
                    <input type="file" id="file_rab" name="file_rab" class="form-control" accept=".xlsx"
                        @if (!isset($project)) required @endif>
                    @if(isset($project) && $project->file_rab)
                        <small>File saat ini: <a href="{{ asset('storage/' . $project->file_rab) }}" target="_blank">Lihat RAB</a></small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="sumber_dana">Sumber Dana</label>
                    <select id="sumber_dana" name="sumber_dana">
                        <option value="internal" {{ old('sumber_dana', isset($project) ? $project->sumber_dana : '') == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="eksternal" {{ old('sumber_dana', isset($project) ? $project->sumber_dana : '') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                    </select>
                </div>

                <div id="internal" style="{{ old('sumber_dana', isset($project) ? $project->sumber_dana : '') == 'eksternal' ? 'display:none' : '' }}">
                    <label>Kategori Pendanaan Internal</label>
                    <select name="kategori_pendanaan_internal" id="kategori_pendanaan_internal" class="form-select">
                        @foreach ($sumber_internal as $si)
                            <option value="{{ $si->id }}" {{ old('kategori_pendanaan_internal', isset($project) ? $project->kategori_pendanaan_internal : '') == $si->id ? 'selected' : '' }}>
                                {{ $si->nama_sumber_dana }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="eksternal" style="{{ old('sumber_dana', isset($project) ? $project->sumber_dana : '') == 'internal' ? 'display:none' : '' }}">
                    <label>Kategori Pendanaan Eksternal</label>
                    <select name="kategori_pendanaan_eksternal" id="kategori_pendanaan_eksternal" class="form-select">
                        @foreach ($sumber_eksternal as $se)
                            <option value="{{ $se->id }}" {{ old('kategori_pendanaan_eksternal', isset($project) ? $project->kategori_pendanaan_eksternal : '') == $se->id ? 'selected' : '' }}>
                                {{ $se->nama_sumber_dana }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="subkategori_pendanaan_container" class="mt-2">
                    <h5>Detail Pendanaan</h5>
                    <div id="subkategori_pendanaan"></div>
                </div>

                <button class="submit-btn mt-2">{{ isset($project) ? 'UPDATE' : 'SUBMIT' }}</button>
            </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sumberDana = document.getElementById('sumber_dana');
        const internalDiv = document.getElementById('internal');
        const eksternalDiv = document.getElementById('eksternal');
        const kategoriInternal = document.getElementById('kategori_pendanaan_internal');
        const kategoriEksternal = document.getElementById('kategori_pendanaan_eksternal');
        const subkategoriContainer = document.getElementById('subkategori_pendanaan_container');
        const subkategoriPendanaanDiv = document.getElementById('subkategori_pendanaan');
        const tahunSelect = document.getElementById('tahun'); // pastikan id="tahun" untuk element select di dropdown

        // --- Tambahkan kode untuk generate dropdown tahun ---
        const currentYear = new Date().getFullYear();
        for (let i = currentYear + 5; i >= 2015; i--) {
            let opt = document.createElement('option');
            opt.value = i;
            opt.textContent = i;
            tahunSelect.appendChild(opt);
        }

        // Fungsi untuk menerapkan formatting rupiah pada input dengan class "rupiah"
        function applyRupiahFormatter() {
            document.querySelectorAll('.rupiah').forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = this.value.replace(/\D/g, '');
                    this.value = 'Rp. ' + new Intl.NumberFormat('id-ID').format(value);
                });
            });
        }

        function updateKategoriPendanaan() {
            let sumberValue = sumberDana.value;
            if (sumberValue === "eksternal") {
                internalDiv.style.display = "none";
                eksternalDiv.style.display = "block";
                loadSubkategoriPendanaan(kategoriEksternal.value);
            } else {
                eksternalDiv.style.display = "none";
                internalDiv.style.display = "block";
                loadSubkategoriPendanaan(kategoriInternal.value);
            }
        }

        function loadSubkategoriPendanaan(id) {
            fetch(`/project/sumberdana/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        subkategoriContainer.style.display = "block";
                        subkategoriPendanaanDiv.innerHTML = data.map(item => `
                            <div class="form-group">
                                <label for="${item.nama_form}">${item.nama}</label>
                                <input type="text" id="${item.nama_form}" name="${item.nama_form}" placeholder="Cth: Rp. 1.000.000" required class="form-control rupiah">
                            </div>
                        `).join('');
                        // Re-apply formatter after innerHTML update
                        applyRupiahFormatter();
                    } else {
                        subkategoriContainer.style.display = "none";
                        subkategoriPendanaanDiv.innerHTML = "";
                    }
                })
                .catch(error => console.error("Error fetching data:", error));
        }

        sumberDana.addEventListener("change", updateKategoriPendanaan);
        kategoriInternal.addEventListener("change", () => loadSubkategoriPendanaan(kategoriInternal.value));
        kategoriEksternal.addEventListener("change", () => loadSubkategoriPendanaan(kategoriEksternal.value));

        updateKategoriPendanaan();

        // Terapkan formatter pada element yang sudah ada saat halaman pertama kali load
        applyRupiahFormatter();
    });
</script>

</body>

</html>
