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

        .error-border {
            border: 2px solid red !important;
        }

        .error-message {
            color: red;
            font-size: 0.8rem;
            margin-top: 4px;
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
            @if (Auth::user()->role == 'admin')
                <a href="{{ route('sumberdana.index') }}">Sumber Dana</a>
                <a href="{{ route('pencatatan_transaksi') }}" class="active">Pencatatan Transaksi</a>
                <a href="{{ route('laporan_keuangan') }}">Laporan Keuangan</a>
                <a href="{{ route('users.index') }}">Management User</a>
            @endif
        </div>

        <div class="content container-fluid p-4">
            <a href="javascript:window.history.back();" class="btn btn-secondary mb-4">
                <i class="bi bi-arrow-left-circle"></i> Kembali
            </a>
            <h1 class="mb-4" style="font-weight: bold; font-size: 2rem;">{{ isset($transaksi) ? 'Edit Transaksi' : 'Tambah Transaksi' }}</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="formTransaksi" method="POST" 
                action="{{ isset($transaksi) ? route('transaksi.update', $transaksi->id) : route('transaksi.store') }}" 
                enctype="multipart/form-data">
                @csrf
                @if(isset($transaksi))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-6">
                    <label for="tanggal" class="form-label">Tanggal Transaksi</label>
                    <input type="text" id="tanggal" name="tanggal" class="form-control flatpickr"
                        placeholder="Pilih tanggal"
                        value="{{ old('tanggal', isset($transaksi) ? \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y') : '') }}">
                </div>
                    
                    <div class="col-md-6">
                        <label for="project" class="form-label">Tim Project</label>
                        <select id="project" name="project" class="form-control">
                        <option value="" disabled selected>Pilih Tim Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project', $transaksi->project_id ?? '') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama_project }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="subkategori_sumberdana" class="form-label">Sub Kategori Pendanaan</label>
                        <select id="subkategori_sumberdana" name="subkategori_sumberdana" class="form-control">
                            <option value="" disabled selected>Pilih Sub Kategori Pendanaan</option>
                            @foreach ($subKategoriSumberdana as $subkategori)
                                <option value="{{ $subkategori->id }}" {{ old('subkategori_sumberdana', $transaksi->subkategori_sumberdana_id ?? '') == $subkategori->id ? 'selected' : '' }}>
                                    {{ $subkategori->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                        <select id="jenis_transaksi" name="jenis_transaksi" class="form-control">
                            <option value="" disabled selected>Pilih jenis transaksi</option>
                            <option value="pemasukan" {{ old('jenis_transaksi', $transaksi->jenis_transaksi ?? '') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="pengeluaran" {{ old('jenis_transaksi', $transaksi->jenis_transaksi ?? '') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="deskripsi" class="form-label">Deskripsi Transaksi</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control">{{ old('deskripsi', $transaksi->deskripsi_transaksi ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="text" id="jumlah_transaksi" name="jumlah_transaksi" class="form-control" inputmode="numeric" value="{{ old('jumlah_transaksi', $transaksi->jumlah_transaksi ?? '') }}">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select id="metode_pembayaran" name="metode_pembayaran" class="form-control">
                            <option value="" disabled selected>Pilih metode pembayaran</option>
                            <option value="cash" {{ old('metode_pembayaran', $transaksi->metode_pembayaran ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer bank" {{ old('metode_pembayaran', $transaksi->metode_pembayaran ?? '') == 'transfer bank' ? 'selected' : '' }}>Transfer Bank</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label for="bukti_transaksi" class="form-label">Bukti Transaksi</label>

                        {{-- Tampilkan link file lama kalau ada --}}
                        @if (isset($transaksi) && $transaksi->bukti_transaksi)
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalBuktiTransaksi">
                                📎 Lihat Bukti Transaksi Sebelumnya
                            </button>
                        </div>
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti bukti transaksi.</small>
                        @endif

                        {{-- Input file baru --}}
                        <input type="file" class="form-control" id="bukti_transaksi" name="bukti_transaksi" accept=".jpg,.jpeg,.png,.pdf">
                    </div>

                    <!-- Modal untuk menampilkan bukti transaksi -->
                    @if (isset($transaksi) && $transaksi->bukti_transaksi)
                    <div class="modal fade" id="modalBuktiTransaksi" tabindex="-1" aria-labelledby="modalBuktiTransaksiLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalBuktiTransaksiLabel">Bukti Transaksi Sebelumnya</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" alt="Bukti Transaksi" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-success">{{ isset($transaksi) ? 'Update' : 'Simpan' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        const selectedSubkategoriId = "{{ old('subkategori_sumberdana', $transaksi->subkategori_sumberdana_id ?? '') }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Inisialisasi Flatpickr
            flatpickr("#tanggal", {
                enableTime: false,
                dateFormat: "d-m-Y", // tampilan di website
                defaultDate: "{{ $tanggalFormatted }}",
                maxDate: "today" // Batasi pemilihan tanggal hingga hari ini
            });

            const projectSelect = document.getElementById("project");
            const subkategoriSelect = document.getElementById("subkategori_sumberdana");
            const selectedSubkategoriId = "{{ old('subkategori_sumberdana', $transaksi->subkategori_sumberdana_id ?? '') }}";

            console.log("Selected Subkategori ID:", selectedSubkategoriId); // Debugging

            // Jika halaman dalam mode edit dan project sudah ada isinya
            if (projectSelect.value !== "") {
                subkategoriSelect.innerHTML = '<option value="">Memuat...</option>';
                subkategoriSelect.disabled = true;

                fetch(`/get-subkategori?project_id=${projectSelect.value}`)
                    .then(response => response.json())
                    .then(data => {
                        subkategoriSelect.disabled = false;
                        subkategoriSelect.innerHTML = '<option value="" disabled>Pilih Sub Kategori Pendanaan</option>';
                        data.forEach(sub => {
                            const option = document.createElement("option");
                            option.value = sub.id;
                            option.textContent = sub.nama;

                            // Cek apakah subkategori ini adalah yang dipilih sebelumnya
                            if (sub.id == selectedSubkategoriId) {
                                option.selected = true; // Tandai sebagai terpilih
                                console.log("Subkategori terpilih:", sub.nama); // Debugging
                            }
                            subkategoriSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Tidak dapat mengambil sub kategori pendanaan.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }

            // Saat project dipilih, ambil subkategori dari server
            projectSelect.addEventListener("change", function () {
                const projectId = this.value;

                subkategoriSelect.innerHTML = '<option value="">Memuat...</option>';
                subkategoriSelect.disabled = true;

                fetch(`/get-subkategori?project_id=${projectId}`)
                    .then(response => response.json())
                    .then(data => {
                        subkategoriSelect.disabled = false;
                        subkategoriSelect.innerHTML = '<option value="" disabled selected>Pilih Sub Kategori Pendanaan</option>';

                        data.forEach(sub => {
                            const option = document.createElement("option");
                            option.value = sub.id;
                            option.textContent = sub.nama;

                            // Cek apakah subkategori ini adalah yang dipilih sebelumnya
                            if (sub.id == selectedSubkategoriId) {
                                option.selected = true; // Tandai sebagai terpilih
                                console.log("Subkategori terpilih saat project berubah:", sub.nama); // Debugging
                            }
                            subkategoriSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Tidak dapat mengambil sub kategori pendanaan.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            });

            // Submit form
            document.getElementById("formTransaksi").addEventListener("submit", function (e) {
                e.preventDefault(); // Mencegah form terkirim langsung

                const requiredFields = [
                    "tanggal",
                    "project",
                    "subkategori_sumberdana",
                    "jenis_transaksi",
                    "deskripsi",
                    "jumlah_transaksi",
                    "metode_pembayaran"
                ];

                let isValid = true;

                // Reset semua error
                requiredFields.forEach(id => {
                    const field = document.getElementById(id);
                    field.classList.remove("error-border");
                    if (field.nextElementSibling && field.nextElementSibling.classList.contains("error-message")) {
                        field.nextElementSibling.remove();
                    }
                });

                // Validasi setiap field yang wajib
                requiredFields.forEach(id => {
                    const field = document.getElementById(id);
                    const value = (field.type === "file") ? field.files.length : field.value.trim();

                    if (!value) {
                        isValid = false;
                        field.classList.add("error-border"); // Tandai field yang tidak terisi

                        const errorMsg = document.createElement("div");
                        errorMsg.className = "error-message";
                        errorMsg.innerText = "Harap isi"; // Pesan kesalahan
                        field.parentNode.appendChild(errorMsg);
                    }
                });

                // Cek apakah ada file baru yang diunggah
                const fileInput = document.getElementById("bukti_transaksi");
                if (fileInput.files.length > 0) {
                    // Jika ada file baru yang diunggah, tambahkan validasi
                    if (!fileInput.value) {
                        isValid = false;
                        fileInput.classList.add("error-border"); // Tandai field yang tidak terisi

                        const errorMsg = document.createElement("div");
                        errorMsg.className = "error-message";
                        errorMsg.innerText = "Harap unggah bukti transaksi baru jika ingin mengganti."; // Pesan kesalahan
                        fileInput.parentNode.appendChild(errorMsg);
                    }
                }

                if (!isValid) {
                    // Tampilkan pesan kesalahan umum jika ada field yang tidak valid
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Harap isi semua field yang wajib.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return; // Hentikan eksekusi jika ada field yang tidak valid
                }

                // Kirim data pakai Fetch jika semua field valid
                const formData = new FormData(this);
                fetch(this.action, {
                    method: this.method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "Sukses!",
                            text: "{{ isset($transaksi) ? 'Transaksi berhasil diedit!' : 'Transaksi berhasil disimpan!' }}",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.href = "{{ route('pencatatan_transaksi') }}";
                        }, 2000);
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan saat menyimpan data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menyimpan data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
    </script>
    <script>
        const jumlahInput = document.getElementById("jumlah_transaksi");

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

        jumlahInput.addEventListener("input", function(e) {
            const formatted = formatRupiah(this.value);
            this.value = formatted;
        });

        // Optional: Hapus format saat submit form
        document.getElementById("formTransaksi").addEventListener("submit", function() {
            jumlahInput.value = jumlahInput.value.replace(/[^0-9]/g, '');
        });
    </script>
        <!-- JS Bootstrap & Popper -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>