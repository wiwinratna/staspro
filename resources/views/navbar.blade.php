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
                <li><a class="dropdown-item" href="#">Edit Profil</a></li>
                <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
