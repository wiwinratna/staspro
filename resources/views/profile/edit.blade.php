@extends('layouts.app')

@section('title', 'Edit Profil')
@extends('layouts.app')
@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Edit Profil</h2>
        <form id="editProfileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" id="name" name="name" value="{{ auth()->user()->name }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ auth()->user()->email }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                    {{ $canEditEmail ?? true ? '' : 'readonly' }}>
                @if(!($canEditEmail ?? true))
                    <p class="text-sm text-gray-500 mt-1">Email tidak dapat diubah.</p>
                @endif
            </div>

            {{-- Tombol Simpan Perubahan --}}
            <div class="text-center space-x-3">
                <button type="button" id="openModal"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow">
                    Edit Password
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Password --}}
<div id="editPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6 relative">
        <h2 class="text-2xl font-bold text-center mb-4">Ubah Password</h2>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>

            <div class="text-center space-x-3">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow">
                    Simpan Password
                </button>
                <button type="button" id="closeModal"
                        class="px-6 py-2 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition shadow">
                    Batal
                </button>
            </div>
        </form>
    </div>
    <div id="modalOverlay" class="fixed inset-0 bg-black opacity-50 z-40"></div>
</div>

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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Form untuk edit profil
        const formEditProfil = document.getElementById("editProfileForm");

        // Pastikan form submit
        formEditProfil.addEventListener("submit", function (e) {
            e.preventDefault(); // Mencegah form submit langsung

            const formData = new FormData(formEditProfil);
            fetch(formEditProfil.action, {
                method: formEditProfil.method,
                body: formData
            })
            .then(response => response.json()) 
            .then(data => {
                if (data.status === 'success') { 
                    Swal.fire({
                        title: 'Profil Berhasil Diperbarui!',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "{{ route('dashboard') }}"; 
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Ada kesalahan saat memperbarui profil.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Tidak dapat menghubungi server.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    });
</script>
@endsection