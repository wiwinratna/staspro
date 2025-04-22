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
            <h1>Tambah Request Pembelian</h1>
            <div class="text mt-10">
                <a href="{{ route('requestpembelian.index') }}" class="px-3"><span class="me-1"><- </span>Lihat
                            Daftar
                            Request</a>
            </div>
            <div class="form-container">
                <div class="form-group">
                    <label for="id_project">Tim Penelitian</label>
                    <select class="form-select" id="id_project" name="id_project" disabled>
                        <option value="" selected disabled> -> Pilih Tim Penelitian <- </option>
                                @foreach ($project as $p)
                        <option value="{{ $p->id }}"
                            {{ $p->id == $request_pembelian->id_project ? 'selected' : '' }}>{{ $p->nama_project }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="tgl_request">Tanggal Request</label>
                    <input type="date" id="tgl_request" name="tgl_request"
                        value="{{ $request_pembelian->tgl_request }}" disabled>
                </div>
                @if (Auth::user()->role == 'admin')
                    <div class="form-group">
                        <form action="{{ route('requestpembelian.changestatus') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_request_pembelian_header"
                                value="{{ $request_pembelian->id }}">
                            <label for="status_request">Tim Penelitian</label>
                            <select class="form-select" id="status_request" name="status_request"
                                @if (count($detail) == 0) disabled @endif>
                                <option value="approve_request"
                                    {{ $request_pembelian->status_request == 'approve_request' ? 'selected' : '' }}>
                                    Menyetujui Request</option>
                                <option value="reject_request"
                                    {{ $request_pembelian->status_request == 'reject_request' ? 'selected' : '' }}>
                                    Menolak Request</option>
                                <option value="done"
                                    {{ $request_pembelian->status_request == 'approve_payment' || $request_pembelian->status_request == 'done' ? 'selected' : '' }}>
                                    Menyetujui Bukti Pembayaran</option>
                                <option value="reject_payment"
                                    {{ $request_pembelian->status_request == 'reject_payment' ? 'selected' : '' }}>
                                    Menolak Bukti Pembayaran</option>
                            </select>
                            <div style="display: none" id="keterangan_reject" class="mt-2">
                                <label for="keterangan_reject">Keterangan Ditolak</label>
                                <input type="text" id="keterangan_reject" name="keterangan_reject"
                                    value="{{ $request_pembelian->keterangan_reject }}"
                                    placeholder="Keterangan Reject">
                            </div>
                            <button class="submit-btn" style="margin-top: 12px">Submit</button>
                        </form>
                    </div>
                @endif
                @if (Auth::user()->role != 'admin')
                    <div class="form-group">
                        <label for="status_request">Tim Penelitian</label>
                        <select class="form-select" id="status_request" name="status_request" disabled>
                            <option value="approve_request"
                                {{ $request_pembelian->status_request == 'approve_request' ? 'selected' : '' }}>
                                Menyetujui Request</option>
                            <option value="reject_request"
                                {{ $request_pembelian->status_request == 'reject_request' ? 'selected' : '' }}>
                                Menolak Request</option>
                            <option value="done"
                                {{ $request_pembelian->status_request == 'approve_payment' || $request_pembelian->status_request == 'done' ? 'selected' : '' }}>
                                Menyetujui Bukti Pembayaran</option>
                            <option value="reject_payment"
                                {{ $request_pembelian->status_request == 'reject_payment' ? 'selected' : '' }}>
                                Menolak Bukti Pembayaran</option>
                        </select>
                    </div>
                    @if ($request_pembelian->status_request == 'reject_request' || $request_pembelian->status_request == 'reject_payment')
                        <div class="form-group">
                            <label for="keterangan_reject" class="mt-2">Keterangan Ditolak</label>
                            <input type="text" id="keterangan_reject" name="keterangan_reject"
                                value="{{ $request_pembelian->keterangan_reject }}" disabled>
                        </div>
                        <a href="{{ route('requestpembelian.pengajuanulang', $request_pembelian->id) }}"
                            class="btn btn-success">Pengajuan
                            Ulang</a>
                    @endif
                @endif
            </div>
            <div>
            <form action="{{ route('requestpembelian.storedetail') }}" method="POST">
                @csrf
                <input type="hidden" name="id_request_pembelian_header" value="{{ $request_pembelian->id }}">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Link Pembelian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detail as $d)
                            <tr>
                                <td>{{ $d->nama_barang }}</td>
                                <td>{{ $d->kuantitas }}</td>
                                <td>{{ number_format($d->harga, 0, ',', '.') }}</td>
                                <td><a href="{{ $d->link_pembelian }}" target="_blank">Lihat Link</a></td>
                                <td>
                                    @if ($d->bukti_bayar)
                                        <a href="{{ asset('bukti_bayar/' . $d->bukti_bayar) }}" class="btn btn-success btn-sm">Lihat Bukti</a>
                                    @else
                                        <a href="{{ route('requestpembelian.addbukti', $d->id) }}" class="btn btn-primary btn-sm">Tambah Bukti</a>
                                    @endif
                                    <a href="{{ route('requestpembelian.editdetail', $d->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="{{ route('requestpembelian.destroydetail', $d->id) }}" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus item ini?')">Delete</a>
                                </td>
                            </tr>
                        @endforeach

                        @if (Auth::user()->role != 'admin')
                            <tr>
                                <td>
                                    <input type="text" name="nama_barang" placeholder="Nama Barang" class="form-control" required>
                                </td>
                                <td>
                                    <input type="number" name="kuantitas" placeholder="Qty" class="form-control" min="1" required>
                                </td>
                                <td>
                                    <input type="text" id="harga" name="harga" placeholder="Harga" class="form-control" required>
                                </td>
                                <td>
                                    <input type="url" name="link_pembelian" placeholder="Link Pembelian" class="form-control" required>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-success btn-sm">Tambah</button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusRequest = document.getElementById('status_request');
            const keteranganReject = document.getElementById('keterangan_reject');

            function toggleKeteranganReject() {
                if (statusRequest.value === 'reject_request' || statusRequest.value === 'reject_payment') {
                    keteranganReject.style.display = 'block';
                } else {
                    keteranganReject.style.display = 'none';
                }
            }

            statusRequest.addEventListener('change', toggleKeteranganReject);

            // Trigger it on page load
            toggleKeteranganReject();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function formatRupiah(angka) {
            const numberString = angka.replace(/[^,\d]/g, "").toString();
            const split = numberString.split(",");
            const sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                const separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] !== undefined ? rupiah + "," + split[1] : rupiah;
            return "Rp. " + rupiah;
        }

        document.addEventListener("DOMContentLoaded", function () {
            const hargaInput = document.getElementById("harga");

            hargaInput.addEventListener("input", function () {
                let value = this.value.replace(/[^,\d]/g, "");
                this.value = formatRupiah(value);
            });

            // Bersihkan format sebelum submit
            const form = hargaInput.closest("form");
            form.addEventListener("submit", function () {
                let rawValue = hargaInput.value.replace(/[^0-9]/g, ""); // hanya angka
                hargaInput.value = rawValue;
            });
        });
    </script>
</body>

</html>
