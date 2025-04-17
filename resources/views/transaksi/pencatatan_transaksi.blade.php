<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pencatatan Transaksi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
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
    <!-- Navbar -->
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
        <a href="{{ route('sumberdana.index') }}">Sumber Dana</a>
        <a href="{{ route('pencatatan_transaksi') }}" class="active">Pencatatan Transaksi</a>
        <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
        <a href="{{ route('users.index') }}">Management User</a>
    @endif
</div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Pencatatan Transaksi</h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <h3>Rp. {{ number_format($totalNominalFiltered ?? $totalNominalKeseluruhan ?? 0, 0, ',', '.') }}</h3>
                    <p>
                        @if(request()->has('start_date') && request()->has('end_date'))
                            @if(request('start_date') === request('end_date'))
                                Total nominal transaksi pada tanggal {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}.
                            @else
                                Total nominal transaksi dari {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }} sampai {{ \Carbon\Carbon::parse(request('end_date'))->locale('id')->translatedFormat('d F Y') }}
                            @endif
                        @else
                            Total nominal transaksi keseluruhan
                        @endif
                    </p>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <form class="d-flex align-items-center" method="GET" action="{{ route('filter_transaksi') }}" id="filterForm">
                    <label for="startDate" class="me-2">Mulai:</label>
                    <input type="date" id="startDate" class="form-control me-3" name="start_date" required>
                    <label for="endDate" class="me-2">Sampai:</label>
                    <input type="date" id="endDate" class="form-control me-3" name="end_date" required>
                    <a href="{{ route('pencatatan_transaksi') }}" class="btn btn-outline-secondary ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </form>

                <button class="btn btn-success" onclick="window.location.href='/form_input_transaksi'">
                    Tambah Pencatatan Transaksi
                </button>
            </div>

            <!-- Tabel Transaksi dengan text-center -->
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Tim Peneliti</th>
                        <th>Sub Kategori Pendanaan</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Metode Pembayaran</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksis as $index => $transaksi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                            <td>{{ $transaksi->project->nama_project ?? 'Tidak Ada' }}</td>
                            <td>{{ $transaksi->subKategoriPendanaan->nama ?? 'Tidak Ada' }}</td>
                            <td>{{ $transaksi->deskripsi_transaksi }}</td>
                            <td>Rp. {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($transaksi->metode_pembayaran) }}</td>
                            <td>
                                @if($transaksi->bukti_transaksi)
                                <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#modalBukti{{ $transaksi->id }}">
                                    Lihat Bukti
                                </button>

                                <div class="modal fade" id="modalBukti{{ $transaksi->id }}" tabindex="-1" aria-labelledby="modalBuktiLabel{{ $transaksi->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalBuktiLabel{{ $transaksi->id }}">Bukti Transaksi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                @if ($transaksi->bukti_transaksi)
                                                    <img src="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" alt="Bukti Transaksi" class="img-fluid mb-3">
                                                    <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" class="btn btn-primary" download>Unduh Bukti</a>
                                                @else
                                                    <p>Tidak ada bukti transaksi tersedia.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('transaksi.edit', $transaksi->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Transaksi">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $transaksi->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Transaksi">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <div id="notifToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="notifMessage">
                    <!-- Pesan Notifikasi -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let notif = localStorage.getItem("notif");
        
        if (notif) {
            // Masukkan pesan ke dalam toast
            document.getElementById("notifMessage").textContent = notif;

            // Tampilkan toast Bootstrap
            let toast = new bootstrap.Toast(document.getElementById("notifToast"));
            toast.show();

            // Hapus dari localStorage setelah ditampilkan
            localStorage.removeItem("notif");
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(transaksiId) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data transaksi akan dihapus secara permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/transaksi/${transaksiId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Content-Type": "application/json",
                    },
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire("Berhasil!", data.message, "success").then(() => {
                            location.reload(); // Refresh halaman setelah sukses
                        });
                    } else {
                        Swal.fire("Gagal!", data.message, "error");
                    }
                })
                .catch((error) => {
                    Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus data.", "error");
                });
            }
        });
    }
</script>
<script>
    function confirmDelete(transaksiId) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data transaksi akan dihapus secara permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/transaksi/${transaksiId}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ _method: "DELETE" }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "Sukses!",
                            text: "Data berhasil dihapus",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: "Terjadi kesalahan, coba lagi",
                            icon: "error",
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        title: "Gagal!",
                        text: "Terjadi kesalahan, coba lagi",
                        icon: "error",
                    });
                });
            }
        });
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const startDateInput = document.getElementById("startDate");
        const endDateInput = document.getElementById("endDate");
        const filterForm = document.getElementById("filterForm");

        // Event listener untuk input tanggal mulai
        startDateInput.addEventListener("change", function () {
            if (startDateInput.value && endDateInput.value) {
                filterForm.submit(); // Kirim form jika kedua tanggal terisi
            }
        });

        // Event listener untuk input tanggal akhir
        endDateInput.addEventListener("change", function () {
            if (startDateInput.value && endDateInput.value) {
                filterForm.submit(); // Kirim form jika kedua tanggal terisi
            }
        });
    });
</script>

</body>
</html>
