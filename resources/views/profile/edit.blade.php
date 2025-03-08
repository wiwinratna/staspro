<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Edit Profil</h2>

    <form id="profileForm" enctype="multipart/form-data">
        @csrf
        <div>
            Nama: <input type="text" name="name" id="name" value="{{ auth()->user()->name }}">
        </div>
        <div>
            Email: <input type="email" name="email" id="email" value="{{ auth()->user()->email }}">
        </div>
        <div>
            Password: <input type="password" name="password" id="password">
        </div>
        <div>
            Foto Profil: <input type="file" name="profile_picture" id="profile_picture">
            <br>
            @if(auth()->user()->profile_picture)
                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" id="profile_preview" width="100">
            @else
                <img src="" id="profile_preview" width="100" style="display: none;">
            @endif
        </div>
        <button type="submit">Update Profile</button>
    </form>

    <script>
        $(document).ready(function () {
            $('#profileForm').submit(function (e) {
                e.preventDefault(); // Mencegah reload halaman
                
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('profile.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        alert(response.message);

                        // Update preview gambar
                        if (response.profile_picture) {
                            $('#profile_preview').attr('src', response.profile_picture).show();
                        }
                    },
                    error: function (xhr) {
                        alert("Terjadi kesalahan saat memperbarui profil.");
                    }
                });
            });
        });
    </script>
</body>
</html>
