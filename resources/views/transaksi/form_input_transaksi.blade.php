<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pencatatan Transaksi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #006400;
            color: white;
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

        .content {
            flex-grow: 1;
            padding: 20px;
        }

        .card {
            background-color: #006400;
            color: white;
            border-radius: 10px;
            padding: 20px;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg w-100">
        <div class="container-fluid d-flex justify-content-end">
            @include('navbar')
        </div>
    </nav>

    <div class="d-flex">
        <div class="sidebar">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('project.index') }}">Project</a>
            <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
            <a href="{{ route('pencatatan_transaksi') }}" class="active">Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
        </div>

        <div class="content container-fluid p-4">
            <a href="javascript:window.history.back();" class="btn btn-secondary mb-4">
                <i class="bi bi-arrow-left-circle"></i> Kembali
            </a>
            <h1 class="mb-4">{{ isset($transaksi) ? 'Edit Transaksi' : 'Tambah Transaksi' }}</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form>
                <div class="row">
                    <div class="col-md-6">
                        <label for="tanggal" class="form-label">Tanggal Transaksi</label>
                        <input type="text" id="tanggal" name="tanggal" class="form-control flatpickr">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="tim_project" class="form-label">Tim Project</label>
                        <select id="tim_project" name="tim_project" class="form-control">
                            <option value="">Pilih Tim Project</option>
                            @foreach ($tim_projects as $project)
                                <option value="{{ $project->id }}">{{ $project->nama_project }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="sub_sub_kategori" class="form-label">Sub-Sub Kategori Pendanaan</label>
                        <select id="sub_sub_kategori" name="sub_sub_kategori" class="form-control">
                            <option value="">Pilih Sub-Sub Kategori</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                        <select id="jenis_transaksi" name="jenis_transaksi" class="form-control">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="deskripsi" class="form-label">Deskripsi Transaksi</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control"></textarea>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="text" id="jumlah" name="jumlah" class="form-control" placeholder="Rp. 0">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select id="metode_pembayaran" name="metode_pembayaran" class="form-control">
                            <option value="bni">BNI</option>
                            <option value="mandiri">MANDIRI</option>
                            <option value="bca">BCA</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="bukti_transaksi" class="form-label">Bukti Transaksi</label>
                        <input type="file" id="bukti_transaksi" name="bukti_transaksi" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            flatpickr("#tanggal", { 
                enableTime: false, 
                dateFormat: "Y-m-d",
                defaultDate: new Date()
            });
        });

        document.getElementById("tim_project").addEventListener("change", function () {
            let projectId = this.value;
            let subSubKategoriDropdown = document.getElementById("sub_sub_kategori");
            subSubKategoriDropdown.innerHTML = '<option value="">Memuat...</option>';
            
            fetch(`/get-sub-sub-kategori/${projectId}`)
                .then(response => response.json())
                .then(data => {
                    subSubKategoriDropdown.innerHTML = '<option value="">Pilih Sub-Sub Kategori</option>';
                    data.forEach(item => {
                        subSubKategoriDropdown.innerHTML += `<option value="${item.id}">${item.nama_sub_kategori}</option>`;
                    });
                });
        });
    </script>
</body>
</html>
