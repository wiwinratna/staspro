{{-- resources/views/navbar.blade.php --}}
<div class="dropdown">
  <a href="#"
     class="d-inline-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle"
     id="profileDropdown"
     data-bs-toggle="dropdown"
     aria-expanded="false">
     
    <img
      src="{{ Auth::user()->profile_picture ? asset('images/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}"
      alt="Profile"
      class="rounded-circle"
      width="32"
      height="32"
      style="object-fit: cover;"
    >

    <span class="fw-semibold">{{ Auth::user()->name }}</span>
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
