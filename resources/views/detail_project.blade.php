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

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #006400;
            color: white;
        }

        .card {
            background-color: #d9d9d9;
            height: 100%;
            color: black;
            border: none;
            border-radius: 5px;
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
            border: 2px solid #006400;
            padding: 5px 10px;
            border-radius: 20px;
            background-color: #006400;

        }

        .text-center {
            text-align: center;
        }

        .text a:hover {
            text-decoration: underline;
            background-color: white;
            color: #006400;
        }

        .table td, .table th {
            text-align: center;
        }

        .table .harga, .table .total {
            text-align: right;
        }

        .table .harga .currency, .table .total .currency {
            display: inline-block;
            text-align: left;
            margin-right: 5px; 
        }

        .table .harga .amount, .table .total .amount {
            display: inline-block;
            text-align: right;
        }

        .card .table {
            background-color: transparent !important;
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
                <a href="{{ route('users.index') }}">Management User</a>
            @endif
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

            <div class="d-flex mt-10 gap-3">
                @if (Auth::user()->role == 'admin')
                    <div class="text">
                        <a href="{{ route('project.create') }}" class="px-3"><span class="me-1">+</span>Input
                            Project</a>
                    </div>
                @endif

                <div class="text">
                    <a href="{{ route('project.downloadproposal', $project->id) }}" class="px-3"><span
                            class="me-1">Download Proposal</a>
                </div>

                <div class="text">
                    <a href="{{ route('project.downloadrab', $project->id) }}" class="px-3"><span
                            class="me-1">Download RAB</a>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h1>{{ $project->nama_project }}</h1>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <h5 class="fw-bold text-center">Deskripsi Project</h5>
                        <p>{{ $project->deskripsi }}</p>
                        <h5 class="text-center fw-bold mt-3">Anggota Tim Riset</h5>
                        <ol>
                            @foreach ($anggota as $a)
                                <li>{{ $a->name }}</li>
                            @endforeach
                            @if (Auth::user()->role == 'admin')
                                <li>
                                    <form action="{{ route('detailproject.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id_project" value="{{ $project->id }}">
                                        <select name="id_user" id="id_user" class="form-select">
                                            <option value="" selected disabled> -> Pilih Anggota Tim Riset <-
                                                    </option>
                                                    @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary mt-2">Tambah Anggota</button>
                            </form>
                            </li>
                            @endif
                        </ol>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <h5 class="mt-3 text-center fw-bold">Detail Pembelian</h5>
                        <table width="100%" class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Kuantitas</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_request = 0;
                                @endphp
                                @if (count($detail_request) == 0)
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada request pembelian</td>
                                    </tr>
                                @else
                                    @foreach ($detail_request as $dd)
                                        <tr>
                                            <td>{{ $dd->nama_barang }}</td>
                                            <td>{{ $dd->kuantitas }}</td>
                                            <td class="harga"><span class="currency">Rp.</span><span class="amount">{{ number_format($dd->harga, 0, ',', '.') }}</span></td>
                                            <td class="total"><span class="currency">Rp.</span><span class="amount">{{ number_format($dd->total, 0, ',', '.') }}</span></td>
                                        </tr>
                                        @php
                                            $total_request += $dd->total;
                                        @endphp
                                    @endforeach
                                    <tr class="fw-bold">
                                        <td colspan="3">Total Request Pembelian</td>
                                        <td class="total"><span class="currency">Rp.</span><span class="amount">{{ number_format($total_request, 0, ',', '.') }}</span></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <h5 class="mt-3 fw-bold text-center">Detail Dana</h5>
                    <table width="100%" class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Rincian Anggaran</th>
                                <th>Realisasi Anggaran</th>
                                <th>Sisa Anggaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_dana = 0;
                            @endphp
                            @foreach ($detail_dana as $dd)
                                <tr>
                                    <td>{{ $dd->nama }}</td>
                                    <td>Rp. {{ number_format($dd->nominal, 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($dd->realisasi_anggaran, 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($dd->nominal - $dd->realisasi_anggaran, 0, ',', '.') }}</td> <!-- Hitung sisa anggaran -->
                                </tr>
                                @php
                                    $total_dana += $dd->nominal;
                                @endphp
                            @endforeach
                            <tr class="fw-bold">
                                <td>Total Dana</td>
                                <td>Rp. {{ number_format($total_dana, 0, ',', '.') }}</td>
                                <td></td> <!-- Kosongkan kolom untuk total -->
                                <td></td> <!-- Kosongkan kolom untuk total -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
