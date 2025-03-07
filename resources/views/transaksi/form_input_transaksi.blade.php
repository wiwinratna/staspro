<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ isset($transaksi) ? 'Edit Pencatatan Transaksi' : 'Tambah Pencatatan Transaksi' }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
  <!-- Top Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="#">
        <img src="{{ asset('images/logo-stas-rg.png') }}" alt="Logo" style="height: 40px;">
      </a>
      <div class="d-flex align-items-center">
        <button class="btn text-white me-3">
          <i class="fas fa-bell"></i>
        </button>
        <div class="dropdown">
          <button class="btn dropdown-toggle p-0 d-flex align-items-center" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://via.placeholder.com/30" alt="Profile Picture" class="rounded-circle">
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
            <li><a class="dropdown-item" href="#">Edit Profil</a></li>
            <li><a class="dropdown-item" href="#">Pengaturan</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
      <a href="{{ route('dashboard') }}">Dashboard</a>
      <a href="{{ route('project.index') }}">Project</a>
      <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
      <a href="{{ route('pencatatan_transaksi') }}" class="active">Pencatatan Transaksi</a>
      <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
    </div>

    <!-- Tombol Kembali & Main Content -->
    <div class="container-fluid p-4">
      <a href="javascript:window.history.back();" class="btn btn-secondary mb-4">
        <i class="bi bi-arrow-left-circle"></i> Kembali
      </a>

      <h1 class="mb-4">{{ isset($transaksi) ? 'Edit Transaksi' : 'Tambah Transaksi' }}</h1>

      <!-- Tampilkan pesan error jika ada -->
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Form Input Transaksi -->
      <form method="POST" action="{{ isset($transaksi) ? route('transaksi.update', $transaksi->id) : route('transaksi.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($transaksi))
          @method('PUT')
        @endif

        <!-- Tanggal Transaksi -->
        <div class="mb-3">
          <label for="tanggal" class="form-label">Tanggal Transaksi</label>
          <input type="date" id="tanggal" name="tanggal" class="form-control" required value="{{ old('tanggal', isset($transaksi) ? $transaksi->tanggal : '') }}">
        </div>

        <!-- Jenis Transaksi -->
        <div class="mb-3">
          <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
          <select id="jenis_transaksi" name="jenis_transaksi" class="form-select" required>
            <option value="">Pilih jenis transaksi</option>
            <option value="Pemasukan" {{ (old('jenis_transaksi', isset($transaksi) ? $transaksi->jenis_transaksi : '') == 'Pemasukan') ? 'selected' : '' }}>Pemasukan</option>
            <option value="Pengeluaran" {{ (old('jenis_transaksi', isset($transaksi) ? $transaksi->jenis_transaksi : '') == 'Pengeluaran') ? 'selected' : '' }}>Pengeluaran</option>
          </select>
        </div>

        <!-- Deskripsi Transaksi -->
        <div class="mb-3">
          <label for="deskripsi_transaksi" class="form-label">Deskripsi Transaksi</label>
          <input type="text" id="deskripsi_transaksi" name="deskripsi_transaksi" class="form-control" required value="{{ old('deskripsi_transaksi', isset($transaksi) ? $transaksi->deskripsi_transaksi : '') }}">
        </div>

        <!-- Jumlah Transaksi -->
        <div class="mb-3">
          <label for="jumlah_transaksi" class="form-label">Jumlah Transaksi</label>
          <input type="number" id="jumlah_transaksi" name="jumlah_transaksi" class="form-control" required value="{{ old('jumlah_transaksi', isset($transaksi) ? $transaksi->jumlah_transaksi : '') }}">
        </div>

        <!-- Metode Pembayaran -->
        <div class="mb-3">
          <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
          <select id="metode_pembayaran" name="metode_pembayaran" class="form-select" required>
            <option value="">Pilih metode pembayaran</option>
            <option value="BNI" {{ (old('metode_pembayaran', isset($transaksi) ? $transaksi->metode_pembayaran : '') == 'BNI') ? 'selected' : '' }}>BNI</option>
            <option value="Dana" {{ (old('metode_pembayaran', isset($transaksi) ? $transaksi->metode_pembayaran : '') == 'Dana') ? 'selected' : '' }}>Dana</option>
            <option value="Gopay" {{ (old('metode_pembayaran', isset($transaksi) ? $transaksi->metode_pembayaran : '') == 'Gopay') ? 'selected' : '' }}>Gopay</option>
            <option value="Jago" {{ (old('metode_pembayaran', isset($transaksi) ? $transaksi->metode_pembayaran : '') == 'Jago') ? 'selected' : '' }}>Jago</option>
          </select>
        </div>

        <!-- Kategori Transaksi -->
        <div class="mb-3">
          <label for="kategori_transaksi" class="form-label">Kategori</label>
          <select id="kategori_transaksi" name="kategori_transaksi" class="form-select" required>
            <option value="">Pilih kategori</option>
            <option value="Internal" {{ (old('kategori_transaksi', isset($transaksi) ? $transaksi->kategori_transaksi : '') == 'Internal') ? 'selected' : '' }}>Internal</option>
            <option value="Eksternal" {{ (old('kategori_transaksi', isset($transaksi) ? $transaksi->kategori_transaksi : '') == 'Eksternal') ? 'selected' : '' }}>Eksternal</option>
            <option value="lainnya" {{ (old('kategori_transaksi', isset($transaksi) ? $transaksi->kategori_transaksi : '') == 'lainnya') ? 'selected' : '' }}>Lainnya</option>
          </select>
        </div>

        <!-- Container Input Kategori Lainnya -->
        <div class="mb-3" id="kategori-lainnya-container" style="display: none;">
          <label for="kategori_lainnya" class="form-label">Masukkan Kategori Lainnya</label>
          <input type="text" id="kategori_lainnya" name="kategori_lainnya" class="form-control" placeholder="Masukkan kategori lainnya" value="{{ old('kategori_lainnya', isset($transaksi) ? $transaksi->kategori_lainnya : '') }}">
        </div>

        <!-- Subkategori Internal -->
        <div class="mb-3" id="subkategori-internal-container" style="display: none;">
          <label for="subkategori_internal" class="form-label">Subkategori Internal</label>
          <select id="subkategori_internal" name="subkategori_internal" class="form-select">
            <option value="">Pilih subkategori</option>
            <option value="Bahan Habis Pakai dan Peralatan" {{ (old('subkategori_internal', isset($transaksi) ? $transaksi->subkategori_internal : '') == 'Bahan Habis Pakai dan Peralatan') ? 'selected' : '' }}>Bahan Habis Pakai dan Peralatan</option>
            <option value="Biaya Transportasi dan Perjalanan" {{ (old('subkategori_internal', isset($transaksi) ? $transaksi->subkategori_internal : '') == 'Biaya Transportasi dan Perjalanan') ? 'selected' : '' }}>Biaya Transportasi dan Perjalanan</option>
            <option value="Biaya Lainnya" {{ (old('subkategori_internal', isset($transaksi) ? $transaksi->subkategori_internal : '') == 'Biaya Lainnya') ? 'selected' : '' }}>Biaya Lainnya</option>
            <option value="lainnya" {{ (old('subkategori_internal', isset($transaksi) ? $transaksi->subkategori_internal : '') == 'lainnya') ? 'selected' : '' }}>Lainnya</option>
          </select>
          <input type="text" id="input_subkategori_internal" name="sub_kategori_internal" class="form-control mt-2" placeholder="Masukkan subkategori lain" style="display: none;" value="{{ old('sub_kategori_internal', isset($transaksi) ? $transaksi->sub_kategori_internal : '') }}">
        </div>

        <!-- Subkategori Eksternal -->
        <div class="mb-3" id="subkategori-eksternal-container" style="display: none;">
          <label for="subkategori_eksternal" class="form-label">Subkategori Eksternal</label>
          <select id="subkategori_eksternal" name="subkategori_eksternal" class="form-select">
            <option value="">Pilih subkategori</option>
            <option value="DRTPM" {{ (old('subkategori_eksternal', isset($transaksi) ? $transaksi->subkategori_eksternal : '') == 'DRTPM') ? 'selected' : '' }}>DRTPM</option>
            <option value="Kedaireka" {{ (old('subkategori_eksternal', isset($transaksi) ? $transaksi->subkategori_eksternal : '') == 'Kedaireka') ? 'selected' : '' }}>Kedaireka</option>
            <option value="LPDP" {{ (old('subkategori_eksternal', isset($transaksi) ? $transaksi->subkategori_eksternal : '') == 'LPDP') ? 'selected' : '' }}>LPDP</option>
            <option value="lainnya" {{ (old('subkategori_eksternal', isset($transaksi) ? $transaksi->subkategori_eksternal : '') == 'lainnya') ? 'selected' : '' }}>Lainnya</option>
          </select>
          <input type="text" id="input_subkategori_eksternal" name="sub_kategori_eksternal" class="form-control mt-2" placeholder="Masukkan subkategori lain" style="display: none;" value="{{ old('sub_kategori_eksternal', isset($transaksi) ? $transaksi->sub_kategori_eksternal : '') }}">
        </div>

        <!-- Sub-Subkategori -->
        <div class="mb-3" id="sub-subkategori-container" style="display: none;">
          <label for="sub_subkategori" class="form-label">Sub-Subkategori</label>
          <select id="sub_subkategori" name="sub_sub_kategori" class="form-select">
            <option value="">Pilih sub-sub kategori</option>
          </select>
          <input type="text" id="input_subsubkategori" name="sub_sub_kategori" class="form-control mt-2" placeholder="Masukkan sub-subkategori lain" style="display: none;" value="{{ old('sub_sub_kategori', isset($transaksi) ? $transaksi->sub_sub_kategori : '') }}">
        </div>

        <!-- Upload Bukti Transaksi -->
        <div class="mb-3">
          <label for="bukti_transaksi" class="form-label">Upload Bukti Transaksi</label>
          <input type="file" id="bukti_transaksi" name="bukti_transaksi" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">
          {{ isset($transaksi) ? 'Update Transaksi' : 'Simpan Transaksi' }}
        </button>
      </form>
    </div>
  </div>

  <script>
    // Listener untuk kategori utama
    document.getElementById('kategori_transaksi').addEventListener('change', function() {
      const subkategoriInternalContainer = document.getElementById('subkategori-internal-container');
      const subkategoriEksternalContainer = document.getElementById('subkategori-eksternal-container');
      const subSubkategoriContainer = document.getElementById('sub-subkategori-container');
      const kategoriLainnyaContainer = document.getElementById('kategori-lainnya-container');

      subkategoriInternalContainer.style.display = 'none';
      subkategoriEksternalContainer.style.display = 'none';
      subSubkategoriContainer.style.display = 'none';
      kategoriLainnyaContainer.style.display = 'none';

      if (this.value === 'Internal') {
        subkategoriInternalContainer.style.display = 'block';
      } else if (this.value === 'Eksternal') {
        subkategoriEksternalContainer.style.display = 'block';
      } else if (this.value === 'lainnya') {
        kategoriLainnyaContainer.style.display = 'block';
      }
    });

    // Listener untuk subkategori internal (jika pilih "lainnya")
    document.getElementById('subkategori_internal').addEventListener('change', function() {
      const inputSubkategori = document.getElementById('input_subkategori_internal');
      if (this.value === 'lainnya') {
        inputSubkategori.style.display = 'block';
      } else {
        inputSubkategori.style.display = 'none';
        inputSubkategori.value = '';
      }
    });

    // Listener untuk subkategori eksternal (gabungan antara pilihan sub-subkategori dan opsi "lainnya")
    document.getElementById('subkategori_eksternal').addEventListener('change', function() {
      const inputSubkategoriEksternal = document.getElementById('input_subkategori_eksternal');
      const subSubkategoriContainer = document.getElementById('sub-subkategori-container');
      const subSubkategoriSelect = document.getElementById('sub_subkategori');

      // Reset input dan sub-subkategori
      inputSubkategoriEksternal.style.display = 'none';
      inputSubkategoriEksternal.value = '';
      subSubkategoriSelect.innerHTML = '<option value="">Pilih sub-subkategori</option>';

      if (this.value === 'lainnya') {
        // Jika opsi "lainnya" dipilih, tampilkan input custom dan sembunyikan sub-subkategori
        inputSubkategoriEksternal.style.display = 'block';
        subSubkategoriContainer.style.display = 'none';
      } else if (this.value === 'DRTPM') {
        subSubkategoriSelect.innerHTML += '<option value="Bahan">Bahan</option><option value="Pengumpulan Data">Pengumpulan Data</option><option value="Analisis Data (Termasuk Sewa Peralatan)">Analisis Data (Termasuk Sewa Peralatan)</option><option value="Pelaporan, Luaran Wajib, dan Luaran Tambahan">Pelaporan, Luaran Wajib, dan Luaran Tambahan</option><option value="Lain-lain">Lain-lain</option>';
        subSubkategoriContainer.style.display = 'block';
      } else if (this.value === 'Kedaireka') {
        subSubkategoriSelect.innerHTML += '<option value="Honorarium Tenaga Peneliti">Honorarium Tenaga Peneliti</option><option value="Peralatan Pendukung Terkait Langsung dengan Kegiatan">Peralatan Pendukung Terkait Langsung dengan Kegiatan</option><option value="Bahan Prototype/Produksi Skala Terbatas/Bahan Habis Penelitian">Bahan Prototype/Produksi Skala Terbatas/Bahan Habis Penelitian</option><option value="Pendampingan/Alih Teknologi">Pendampingan/Alih Teknologi</option><option value="Diskusi Terpumpun/Focus Group Discussion (FGD)">Diskusi Terpumpun/Focus Group Discussion (FGD)</option><option value="Survei">Survei</option><option value="Biaya Pengujian Produk">Biaya Pengujian Produk</option><option value="Pendaftaran HKI">Pendaftaran HKI</option><option value="Biaya Perjalanan Dinas">Biaya Perjalanan Dinas</option><option value="Bantuan Insentif Mahasiswa">Bantuan Insentif Mahasiswa</option><option value="Biaya Produksi Skala Terbatas">Biaya Produksi Skala Terbatas</option><option value="Pengelolaan Program Dana Padanan">Pengelolaan Program Dana Padanan</option>';
        subSubkategoriContainer.style.display = 'block';
      } else if (this.value === 'LPDP') {
        subSubkategoriSelect.innerHTML += '<option value="Biaya Langsung">Biaya Langsung</option><option value="Biaya Tidak Langsung">Biaya Tidak Langsung</option>';
        subSubkategoriContainer.style.display = 'block';
      } else {
        subSubkategoriContainer.style.display = 'none';
      }
    });

    // Listener untuk sub-subkategori (jika pilih "lainnya")
    document.getElementById('sub_subkategori').addEventListener('change', function() {
      const inputSubSubkategori = document.getElementById('input_subsubkategori');
      if (this.value === 'lainnya') {
        inputSubSubkategori.style.display = 'block';
      } else {
        inputSubSubkategori.style.display = 'none';
        inputSubSubkategori.value = '';
      }
    });
  </script>
</body>
</html>
