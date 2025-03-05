<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Dashboard</title>
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
            color: white;
            border: none;
            border-radius: 10px;
            padding: 20px;
        }

        .card h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .card p {
            margin: 0;
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
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('project.index') }}">Project</a>
            <a href="{{ route('requestpembelian.index') }}">Request Pembelian</a>
            <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}"class="active">Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Laporan Keuangan</h1>
        
            <!-- Filter Section -->
            <form method="GET" action="{{ route('laporan_keuangan') }}">
                <div class="filter-container">
                    <div class="filters">
                        <!-- Filter Tim Penelitian -->
                        <select class="form-select" id="timPenelitian" name="timPenelitian" aria-label="Filter Tim Penelitian">
                            <option value="">Tim Penelitian</option>
                            <option value="smart_manequin" {{ request('timPenelitian') == 'smart_manequin' ? 'selected' : '' }}>Smart Manequin</option>
                            <option value="automation_weapon_rack" {{ request('timPenelitian') == 'automation_weapon_rack' ? 'selected' : '' }}>Automation Weapon Rack</option>
                            <option value="vr_manequin_senjata" {{ request('timPenelitian') == 'vr_manequin_senjata' ? 'selected' : '' }}>VR Manequin dan Senjata</option>
                            <option value="smart_vest" {{ request('timPenelitian') == 'smart_vest' ? 'selected' : '' }}>Smart Vest</option>
                            <option value="autodimension" {{ request('timPenelitian') == 'autodimension' ? 'selected' : '' }}>Autodimension</option>
                            <option value="crowded_detection" {{ request('timPenelitian') == 'crowded_detection' ? 'selected' : '' }}>Crowded Detection</option>
                            <option value="biofarma" {{ request('timPenelitian') == 'biofarma' ? 'selected' : '' }}>Biofarma</option>
                            <option value="cage_monitoring" {{ request('timPenelitian') == 'cage_monitoring' ? 'selected' : '' }}>Cage Monitoring</option>
                            <option value="modern_farming" {{ request('timPenelitian') == 'modern_farming' ? 'selected' : '' }}>Modern Farming</option>
                            <option value="intercropping_anggur_paprika" {{ request('timPenelitian') == 'intercropping_anggur_paprika' ? 'selected' : '' }}>Intercropping Anggur Paprika</option>
                            <option value="vehicle" {{ request('timPenelitian') == 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                            <option value="tosisitas_danau" {{ request('timPenelitian') == 'tosisitas_danau' ? 'selected' : '' }}>Tosisitas Danau</option>
                            <option value="e_sniffer" {{ request('timPenelitian') == 'e_sniffer' ? 'selected' : '' }}>E-Sniffer</option>
                            <option value="incinerator" {{ request('timPenelitian') == 'incinerator' ? 'selected' : '' }}>Incinerator</option>
                        </select>

                        <!-- Filter Kategori Pendanaan -->
                        <select class="form-select" id="kategoriPendanaan" name="kategoriPendanaan" aria-label="Filter Kategori Pendanaan">
                            <option value="">Kategori Pendanaan</option>
                            <option value="Internal" {{ request('kategoriPendanaan') == 'Internal' ? 'selected' : '' }}>Internal</option>
                            <option value="Eksternal" {{ request('kategoriPendanaan') == 'Eksternal' ? 'selected' : '' }}>Eksternal</option>
                        </select>
                    </div>
                    <!-- Tombol Filter & Unduh Excel -->
                    <div class="download-btn">
                        <button type="submit" class="btn" style="background-color: #006400; color: white;">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('laporan.export.excel', request()->all()) }}" class="btn" style="background-color: #006400; color: white; margin-left:10px;">
                            <i class="fas fa-download"></i> Unduh Excel
                        </a>
                    </div>
                </div>
            </form>

            <!-- Judul Laporan -->
            <h3 id="laporanJudul" class="mt-4">
                @if(request('kategoriPendanaan') || request('timPenelitian'))
                    Laporan Keuangan
                    @if(request('timPenelitian'))
                        - {{ ucwords(str_replace('_', ' ', request('timPenelitian'))) }}
                    @endif
                    @if(request('kategoriPendanaan'))
                        - {{ request('kategoriPendanaan') }}
                    @endif
                @else
                    Laporan Keuangan Keseluruhan
                @endif
            </h3>

            <!-- Tabel Laporan -->
            <div class="table-container">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Tim Peneliti</th>
                            <th>Jenis Transaksi</th>
                            <th>Deskripsi Transaksi</th>
                            <th>Jumlah Transaksi</th>
                            <th>Metode Pembayaran</th>
                            <th>Kategori Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksis as $index => $transaksi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                                <td>{{ $transaksi->tim_penelitian ?? '-' }}</td>
                                <td>{{ $transaksi->jenis_transaksi }}</td>
                                <td>{{ $transaksi->deskripsi_transaksi }}</td>
                                <td>{{ number_format($transaksi->jumlah_transaksi, 2) }}</td>
                                <td>{{ $transaksi->metode_pembayaran }}</td>
                                <td>{{ $transaksi->kategori_transaksi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total Nominal -->
            <div class="mt-4">
                <h4>Total Nominal: {{ number_format($totalNominal ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
