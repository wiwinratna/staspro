<nav class="navbar navbar-expand-lg bg-dark">
    <div class="container-fluid d-flex justify-content-end align-items-center">
        <!-- Profile Section -->
        <div class="dropdown" style="background:transparent !important;">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" 
               id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
               <img src="{{ Auth::user()->profile_picture ? asset('images/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}" 
                    alt="Profile" class="rounded-circle" width="35" height="35">
                <span class="ms-2">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <!-- Edit Profil -->
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        Edit Profil
                    </a>
                </li>
                <!-- Pengaturan -->
                <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <!-- Logout -->
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
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileLabel">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ Auth::user()->profile_picture ? asset('images/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}" 
                     alt="Profile" class="rounded-circle mb-3" width="80" height="80">
                <h5>{{ Auth::user()->name }}</h5>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label text-start d-block">Nama</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-start d-block">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-start d-block">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="******">
                        <small><a href="#" class="text-primary">Edit Password</a></small>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
