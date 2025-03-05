<div class="dropdown">
    <!-- Profile Button -->
    <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="profileDropdown"
        data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://ui-avatars.com/api/?name=User+Name&background=random&size=40 class="rounded-circle me-2"
            alt="Profile">
        <span>Profile</span>
    </button>

    <!-- Dropdown Menu -->
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
        <li>
            <a class="dropdown-item" href="#">Edit Profil</a>
        </li>
        <li>
            <a class="dropdown-item" href="#">Pengaturan</a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
        </li>
    </ul>
</div>
