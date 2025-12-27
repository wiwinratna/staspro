{{-- resources/views/navbar.blade.php --}}

<style>
  /* supaya dropdown link terasa nyatu */
  .nav-profile{
    padding:6px 10px;
    border-radius:999px;
    transition:.15s ease;
  }
  .nav-profile:hover{
    background:rgba(255,255,255,.12);
  }

  /* WRAP: bikin ring ijo + glow halus */
  .nav-avatar-wrap{
    width:34px;
    height:34px;
    border-radius:999px;
    padding:2px;
    background:linear-gradient(135deg, rgba(236,253,245,.85), rgba(187,247,208,.35));
    box-shadow:
      0 0 0 1px rgba(255,255,255,.25),
      0 10px 18px rgba(2,6,23,.18);
    display:inline-flex;
    align-items:center;
    justify-content:center;
  }

  /* AVATAR: biar clean & nyatu */
  .nav-avatar{
    width:30px;
    height:30px;
    border-radius:999px;
    object-fit:cover;
    background:rgba(255,255,255,.95);
    border:1px solid rgba(22,163,74,.35); /* ring hijau */
    display:block;
  }

  /* fallback icon kalau default png terlihat "flat" */
  .nav-avatar.default{
    object-fit:contain;
    padding:4px;
    background:rgba(236,253,245,.9);
  }
</style>

<div class="dropdown">
  <a href="#"
     class="nav-profile d-inline-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle"
     id="profileDropdown"
     data-bs-toggle="dropdown"
     aria-expanded="false">

    <span class="nav-avatar-wrap">
      <img
        src="{{ Auth::user()->profile_picture
            ? asset('images/' . Auth::user()->profile_picture)
            : asset('images/default-profile.png') }}"
        alt="Profile"
        class="nav-avatar {{ Auth::user()->profile_picture ? '' : 'default' }}"
        loading="lazy"
        onerror="this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';this.classList.add('default');"
      >
    </span>

    <span class="fw-semibold">{{ Auth::user()->name }}</span>
  </a>

  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
    <li>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <input type="hidden" name="redirect" value="{{ auth()->user()->role }}">
        <button type="submit" class="dropdown-item text-danger">
          Logout
        </button>
      </form>
    </li>
  </ul>
</div>
