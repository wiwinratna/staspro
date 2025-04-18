<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumber Dana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
            background-color: #006400;
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

        .table thead th {
            text-align: center;
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
                <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
                <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
                <a href="{{ route('users.index') }}">Management User</a>
            @endif
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4" style="font-weight: bold; font-size: 2rem;">Sumber Dana</h1>

            @if ($message = Session::get('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: '{{ $message }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                </script>
            @endif

            @if ($message = Session::get('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: '{{ $message }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                </script>
            @endif
            <div class="text">
                <a href="{{ route('sumberdana.create') }}" class="px-3"><span class="me-1">+</span>Input
                    Sumber Dana</a>
            </div>

            <div>
                <table id="table" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Nama Sumber Dana</th>
                            <th class="text-center">Jenis Pendanaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sumberdana as $r)
                            <tr>
                                <td>{{ $r->nama_sumber_dana }}</td>
                                <td class="text-center">{{ Str::title($r->jenis_pendanaan) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('sumberdana.edit', $r->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Sumber Dana">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('sumberdana.destroy', $r->id) }}" method="POST" style="display:inline;" data-id="{{ $r->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $r->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Sumber Dana">
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="popup-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Delete!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah yakin akan menghapus request?</p>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-danger">Ya</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Apakah yakin akan menghapus sumber dana?')) {
                // Temukan form yang sesuai dengan ID
                const form = document.querySelector(`form[data-id="${id}"]`);
                if (form) {
                    form.submit();
                }
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#table');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.querySelector(`form[data-id="${id}"]`);
                    if (form) {
                        form.submit();
                    }
                }
            });
        }
    </script>
</body>

</html>
