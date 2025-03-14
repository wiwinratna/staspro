<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Pembelian</title>
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

        .sidebar a:hover, .sidebar a.active {
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
            <a href="{{ route('requestpembelian.index') }}"class="active">Request Pembelian</a>
            <a href="{{ route('pencatatan_transaksi') }}">Pencatatan Transaksi</a>
            <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
        </div>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <h1 class="mb-4">Request Pembelian</h1>

            @if ($message = Session::get('success'))
                <p class="text-success">{{ $message }}</p>
            @endif
            @if ($message = Session::get('error'))
                <p class="text-danger">{{ $message }}</p>
            @endif
            <div class="text">
                <a href="{{ route('requestpembelian.create') }}" class="px-3"><span class="me-1">+</span>Input
                    Request Pembelian</a>
            </div>

            <div>
                <table id="table" class="table">
                    <thead>
                        <tr>
                            <th>Nomor Request</th>
                            <th>Tim Penelitian</th>
                            <th>Nama Barang</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($request_pembelian as $r)
                            <tr>
                                <td>{{ $r->no_request }}</td>
                                <td>{{ $r->nama_project }}</td>
                                <td>{{ $r->nama_barang }}</td>
                                <td>{{ $r->total_harga }}</td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27"
                                        viewBox="0 0 24 24" fill="none" stroke="#439E2C" stroke-width="1"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-check">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path
                                            d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 15l2 2l4 -4" />
                                    </svg>
                                    <svg width="23" height="23" viewBox="0 0 27 27" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21 21.625L21.3475 19.8925C21.53 18.9763 22.02 18.13 22.195 17.2125C22.2325 17.0217 22.2508 16.8258 22.25 16.625C22.2495 16.1901 22.1582 15.76 21.9819 15.3624C21.8056 14.9648 21.5483 14.6083 21.2264 14.3159C20.9045 14.0234 20.5251 13.8013 20.1124 13.6639C19.6998 13.5265 19.263 13.4767 18.83 13.5178C18.397 13.5588 17.9773 13.6898 17.5979 13.9024C17.2184 14.1149 16.8875 14.4043 16.6263 14.7521C16.3651 15.0999 16.1793 15.4983 16.081 15.922C15.9826 16.3457 15.9738 16.7852 16.055 17.2125C16.23 18.1313 16.72 18.975 16.9037 19.8925L17.25 21.625M21 21.625H17.25M21 21.625L24.1212 22.4575C24.6541 22.5761 25.1307 22.8728 25.4722 23.2986C25.8138 23.7245 26 24.2541 26 24.8C26 25.4625 25.4625 26 24.8 26H13.45C13.1317 26 12.8265 25.8736 12.6015 25.6485C12.3764 25.4235 12.25 25.1183 12.25 24.8C12.25 23.675 13.0313 22.7013 14.1288 22.4575L17.25 21.625M19.75 9.75V8.5C19.75 4.965 19.75 3.19625 18.6513 2.09875C17.5538 1 15.785 1 12.25 1H8.5C4.965 1 3.19625 1 2.09875 2.09875C1 3.19625 1 4.965 1 8.5V18.5C1 22.035 1 23.8038 2.09875 24.9013C3.19625 26 4.965 26 8.5 26"
                                            stroke="{{ $r->status_request == 'approve_admin' || $r->status_request == 'submit_payment' || $r->status_request == 'approve_payment' || $r->status_request == 'done' ? '#439E2C' : 'black' }}"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M7.25 9.33375C7.25 9.33375 8.03125 9.33375 8.8125 11C8.8125 11 11.2938 6.83375 13.5 6M6 16H11M6 19.75H11"
                                            stroke="{{ $r->status_request == 'approve_admin' || $r->status_request == 'submit_payment' || $r->status_request == 'approve_payment' || $r->status_request == 'done' ? '#439E2C' : 'black' }}"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M9.25 15.5V5.3125L6 8.5625L4.25 6.75L10.5 0.5L16.75 6.75L15 8.5625L11.75 5.3125V15.5H9.25ZM3 20.5C2.3125 20.5 1.72417 20.2554 1.235 19.7663C0.745833 19.2771 0.500833 18.6883 0.5 18V14.25H3V18H18V14.25H20.5V18C20.5 18.6875 20.2554 19.2763 19.7663 19.7663C19.2771 20.2563 18.6883 20.5008 18 20.5H3Z"
                                            fill="{{ $r->status_request == 'submit_payment' || $r->status_request == 'approve_payment' || $r->status_request == 'done' ? '#439E2C' : 'black' }}" />
                                    </svg>
                                    <svg width="23" height="23" viewBox="0 0 27 27" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21 21.625L21.3475 19.8925C21.53 18.9763 22.02 18.13 22.195 17.2125C22.2325 17.0217 22.2508 16.8258 22.25 16.625C22.2495 16.1901 22.1582 15.76 21.9819 15.3624C21.8056 14.9648 21.5483 14.6083 21.2264 14.3159C20.9045 14.0234 20.5251 13.8013 20.1124 13.6639C19.6998 13.5265 19.263 13.4767 18.83 13.5178C18.397 13.5588 17.9773 13.6898 17.5979 13.9024C17.2184 14.1149 16.8875 14.4043 16.6263 14.7521C16.3651 15.0999 16.1793 15.4983 16.081 15.922C15.9826 16.3457 15.9738 16.7852 16.055 17.2125C16.23 18.1313 16.72 18.975 16.9037 19.8925L17.25 21.625M21 21.625H17.25M21 21.625L24.1212 22.4575C24.6541 22.5761 25.1307 22.8728 25.4722 23.2986C25.8138 23.7245 26 24.2541 26 24.8C26 25.4625 25.4625 26 24.8 26H13.45C13.1317 26 12.8265 25.8736 12.6015 25.6485C12.3764 25.4235 12.25 25.1183 12.25 24.8C12.25 23.675 13.0313 22.7013 14.1288 22.4575L17.25 21.625M19.75 9.75V8.5C19.75 4.965 19.75 3.19625 18.6513 2.09875C17.5538 1 15.785 1 12.25 1H8.5C4.965 1 3.19625 1 2.09875 2.09875C1 3.19625 1 4.965 1 8.5V18.5C1 22.035 1 23.8038 2.09875 24.9013C3.19625 26 4.965 26 8.5 26"
                                            stroke="{{ $r->status_request == 'approve_payment' || $r->status_request == 'done' ? '#439E2C' : 'black' }}"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M7.25 9.33375C7.25 9.33375 8.03125 9.33375 8.8125 11C8.8125 11 11.2938 6.83375 13.5 6M6 16H11M6 19.75H11"
                                            stroke="{{ $r->status_request == 'approve_payment' || $r->status_request == 'done' ? '#439E2C' : 'black' }}"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M22.9587 11.4998L20.417 8.604L20.7712 4.77067L17.0107 3.9165L15.042 0.604004L11.5003 2.12484L7.95866 0.604004L5.98991 3.9165L2.22949 4.76025L2.58366 8.59359L0.0419922 11.4998L2.58366 14.3957L2.22949 18.2394L5.98991 19.0936L7.95866 22.4061L11.5003 20.8748L15.042 22.3957L17.0107 19.0832L20.7712 18.229L20.417 14.3957L22.9587 11.4998ZM9.41699 16.7082L5.25033 12.5415L6.71908 11.0728L9.41699 13.7603L16.2816 6.89567L17.7503 8.37484L9.41699 16.7082Z"
                                            fill="{{ $r->status_request == 'done' ? '#439E2C' : 'black' }}" />
                                    </svg>
                                </td>
                                <td>
                                    <a href="{{ route('requestpembelian.detail', $r->id) }}">
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
                                    <a href="{{ route('requestpembelian.edit', $r->id) }}">
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
                            <th>Nomor Request</th>
                            <th>Tim Penelitian</th>
                            <th>Nama Barang</th>
                            <th>Total Harga</th>
                            <th>Status</th>
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
            const url = "{{ url('requestpembelian/destroy/') }}";
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
