<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid d-flex align-items-center px-3">
        <!-- Logo STASRG di kiri -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/stasrg-logo.png') }}" alt="Logo STASRG" width="32" height="32" style="object-fit: contain;">
            <span class="fw-semibold fs-5">STAS-RG</span>
        </a>

        <!-- Profile Section di kanan -->
        <div class="dropdown ms-auto"> <!-- ms-auto bikin elemen ini terdorong ke kanan -->
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
               id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ Auth::user()->profile_picture ? asset('images/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}"
                     alt="Profile" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                <span class="fw-normal">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editProfileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileLabel">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Foto Profil -->
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}" 
                            alt="Profile" class="rounded-circle me-3" width="80" height="80" style="object-fit: cover;">
                    </div>

                    <!-- Edit Nama -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" disabled value="**********">
                        <small class="form-text text-muted">Password tidak dapat diubah kecuali Anda mengklik tombol di bawah untuk menggantinya.</small>
                    </div>

                    <!-- Checkbox untuk Ubah Password -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="changePassword" onchange="togglePasswordFields()">
                        <label class="form-check-label" for="changePassword">Ubah Password</label>
                    </div>

                    <!-- Input Password Baru dan Konfirmasi Password (Hidden by default) -->
                    <div id="passwordFields" style="display: none;">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" id="new_password" name="new_password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">Update Profil</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePasswordFields() {
        var passwordFields = document.getElementById("passwordFields");
        var changePasswordCheckbox = document.getElementById("changePassword");

        if (changePasswordCheckbox.checked) {
            passwordFields.style.display = "block"; 
        } else {
            passwordFields.style.display = "none"; 
        }
    }

    $(document).ready(function() {
        $('#editProfileForm').submit(function(e) {
            e.preventDefault();  

            var formData = new FormData(this);  
            $.ajax({
                url: '{{ route('profile.update') }}',  
                type: 'POST',
                data: formData,
                processData: false,  
                contentType: false, 
                success: function(response) {
                    if (response.status === 'success') {
                        // Tampilkan pesan sukses menggunakan SweetAlert
                        Swal.fire({
                            title: 'Profil Berhasil Diperbarui!',
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "{{ route('dashboard') }}"; 
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || 'Ada kesalahan saat memperbarui profil.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan. Silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>