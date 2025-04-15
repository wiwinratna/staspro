<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumber Dana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
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
            @endif
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Sumber Dana</h1>

            @if ($message = Session::get('success'))
                <p class="text-success">{{ $message }}</p>
            @endif
            @if ($message = Session::get('error'))
                <p class="text-danger">{{ $message }}</p>
            @endif
            <div class="text">
                <a href="{{ route('sumberdana.create') }}" class="px-3"><span class="me-1">+</span>Input
                    Sumber Dana</a>
            </div>

            <div>
                <table id="table" class="table">
                    <thead>
                        <tr>
                            <th>Nama Sumber Dana</th>
                            <th>Jenis Pendanaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sumberdana as $r)
                            <tr>
                                <td>{{ $r->nama_sumber_dana }}</td>
                                <td>{{ Str::title($r->jenis_pendanaan) }}</td>
                                <td>
                                    <a href="{{ route('sumberdana.detail', $r->id) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-list">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 6l11 0" />
                                            <path d="M9 12l11 0" />
                                            <path d="M9 18l11 0" />
                                            <path d="M5 6l0 .01" />
                                            <path d="M5 12l0 .01" />
                                            <path d="M5 18l0 .01" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('sumberdana.edit', $r->id) }}">
                                        <svg width="18" height="19" viewBox="0 0 18 19" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M0 14.9795V18.0195C0 18.2995 0.22 18.5195 0.5 18.5195H3.54C3.67 18.5195 3.8 18.4695 3.89 18.3695L14.81 7.45953L11.06 3.70953L0.15 14.6195C0.0500001 14.7195 0 14.8395 0 14.9795ZM17.71 4.55953C17.8027 4.46702 17.8762 4.35713 17.9264 4.23616C17.9766 4.11518 18.0024 3.9855 18.0024 3.85453C18.0024 3.72357 17.9766 3.59388 17.9264 3.47291C17.8762 3.35194 17.8027 3.24205 17.71 3.14953L15.37 0.809534C15.2775 0.71683 15.1676 0.643283 15.0466 0.593101C14.9257 0.54292 14.796 0.51709 14.665 0.51709C14.534 0.51709 14.4043 0.54292 14.2834 0.593101C14.1624 0.643283 14.0525 0.71683 13.96 0.809534L12.13 2.63953L15.88 6.38953L17.71 4.55953Z"
                                                fill="black" />
                                        </svg>
                                    </a>
                                    <button class="border border-0 bg-transparent" data-bs-toggle="modal"
                                        data-bs-target="#popup-modal" onclick="deleteModal(this)"
                                        data-id="{{ $r->id }}">
                                        <svg width="14" height="19" viewBox="0 0 14 19" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M14 1.51709H10.5L9.5 0.51709H4.5L3.5 1.51709H0V3.51709H14M1 16.5171C1 17.0475 1.21071 17.5562 1.58579 17.9313C1.96086 18.3064 2.46957 18.5171 3 18.5171H11C11.5304 18.5171 12.0391 18.3064 12.4142 17.9313C12.7893 17.5562 13 17.0475 13 16.5171V4.51709H1V16.5171Z"
                                                fill="black" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Nama Sumber Dana</th>
                            <th>Jenis Pendanaan</th>
                            <th>Aksi</th>
                        </tr>
                    </tfoot>
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
        function deleteModal(e) {
            const id = e.getAttribute('data-id');
            const url = "{{ url('sumberdana/destroy/') }}";
            document.querySelector('#popup-modal a').href = url + '/' + id;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#table');
    </script>
</body>

</html>
