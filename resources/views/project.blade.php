<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background: linear-gradient(135deg, #006400, #228B22);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            height: 180px;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
            justify-content: center; /* Horizontal center */
            align-items: center;     /* Vertical center */
            text-align: center;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h3 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: bold;
            text-align: center;
        }

        .text a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            background-color: #006400;
            border: 2px solid #006400;
            transition: 0.3s ease-in-out;
        }

        .text a:hover {
            background-color: white;
            color: #006400;
            text-decoration: none;
        }

        .card-icons {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
            opacity: 0; /* Default state is hidden */
            transition: opacity 0.3s ease; /* Smooth transition for opacity */
        }

        .card:hover .card-icons {
            opacity: 1; /* Ikon muncul saat hover */
        }

        .card-icons i {
            color: white;
            cursor: pointer;
        }

        .card-icons i:hover {
            color: #ffd700;
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
        <div class="container-fluid p-4">
            <h1 class="mb-4">Project</h1>

            @if ($message = Session::get('success'))
                <p class="text-success">{{ $message }}</p>
            @endif
            @if ($message = Session::get('error'))
                <p class="text-danger">{{ $message }}</p>
            @endif

            @if (Auth::user()->role == 'admin')
                <div class="text mb-3">
                    <a href="{{ route('project.create') }}"><span class="me-1">+</span> Input Project</a>
                </div>
            @endif

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach ($projects as $p)
                    <div class="col-md-3">
                        <div class="card" onclick="window.location='{{ route('project.show', $p->id) }}'">
                        @if (Auth::user()->role == 'admin')
                            <div class="card-icons">
                                <a href="{{ route('project.edit', $p->id) }}" onclick="event.stopPropagation();">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('project.destroy', $p->id) }}" method="POST" id="delete-form-{{ $p->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <i class="fas fa-trash" onclick="event.stopPropagation(); confirmDelete({{ $p->id }})"></i>
                                </form>
                            </div>
                        @endif
                            <h3>{{ $p->nama_project }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Project ini memiliki data terkait seperti request pembelian. Jika dilanjutkan, semua data tersebut juga akan dihapus. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit(); // Submit form untuk hapus project
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
    @endif

    @if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session("error") }}',
            showConfirmButton: true
        });
    </script>
    @endif
</body>

</html>
