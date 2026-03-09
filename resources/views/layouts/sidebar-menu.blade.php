<div class="menu-title">Menu</div>

@php
  $dashRoute = Auth::user()->role === 'bendahara'
    ? 'bendahara.dashboard'
    : (Auth::user()->role === 'admin' ? 'dashboard' : 'peneliti.dashboard');
  $projectMenuOpen = request()->routeIs('project.*') || request()->routeIs('pengajuan.*');
  $komponenMenuOpen = request()->routeIs('requestpembelian.*');
  $masterDataOpen = request()->routeIs('sumberdana.*') || request()->routeIs('users.*');
@endphp

<a class="nav-link-custom {{ request()->routeIs($dashRoute) ? 'active' : '' }}" href="{{ route($dashRoute) }}">
  <i class="bi bi-speedometer2"></i> Dashboard
</a>

<div class="menu-title mt-3">Project</div>

<a class="nav-link-custom d-flex justify-content-between align-items-center {{ $projectMenuOpen ? 'menu-open' : '' }}"
  data-bs-toggle="collapse"
  href="#menuProject"
  role="button"
  aria-expanded="{{ $projectMenuOpen ? 'true' : 'false' }}"
  aria-controls="menuProject">
    <span>
      <i class="bi bi-kanban"></i> Project
    </span>
    <i class="bi bi-chevron-down"></i>
</a>

<div class="collapse {{ $projectMenuOpen ? 'show' : '' }}" id="menuProject">
  <div class="ms-2 mt-1 d-grid gap-1">
    <a class="nav-link-custom {{ request()->routeIs('project.index') ? 'active' : '' }}" href="{{ route('project.index') }}">
      <i class="bi bi-play-circle"></i> Project Berjalan
    </a>

    @if(in_array(Auth::user()->role, ['admin','bendahara']))
      <a class="nav-link-custom {{ request()->routeIs('pengajuan.masuk') ? 'active' : '' }}" href="{{ route('pengajuan.masuk') }}">
        <i class="bi bi-inbox"></i> Pengajuan Masuk
      </a>
    @elseif(Auth::user()->role === 'peneliti')
      <a class="nav-link-custom {{ request()->routeIs('pengajuan.saya') ? 'active' : '' }}" href="{{ route('pengajuan.saya') }}">
        <i class="bi bi-clipboard-check"></i> Pengajuan Saya
      </a>
    @endif

    @if(in_array(Auth::user()->role, ['admin','bendahara']))
      <a class="nav-link-custom {{ request()->routeIs('funding.*') ? 'active' : '' }}" href="{{ route('funding.index') }}">
        <i class="bi bi-cash-coin"></i> Dana Cair
      </a>
    @endif
  </div>
</div>

<div class="menu-title mt-3">Transaksi</div>

<a class="nav-link-custom d-flex justify-content-between align-items-center {{ $komponenMenuOpen ? 'menu-open' : '' }}"
  data-bs-toggle="collapse"
  href="#menuPengajuanKomponen"
  role="button"
  aria-expanded="{{ $komponenMenuOpen ? 'true' : 'false' }}"
  aria-controls="menuPengajuanKomponen">
    <span>
      <i class="bi bi-bag-check"></i> Pengajuan Komponen
    </span>
    <i class="bi bi-chevron-down"></i>
</a>

<div class="collapse {{ $komponenMenuOpen ? 'show' : '' }}" id="menuPengajuanKomponen">
  <div class="ms-2 mt-1 d-grid gap-1">
    <a class="nav-link-custom {{ request()->routeIs('requestpembelian.index') ? 'active' : '' }}" href="{{ route('requestpembelian.index') }}">
      <i class="bi bi-list-ul"></i> Daftar Pengajuan Komponen
    </a>
    @if(Auth::user()->role !== 'peneliti')
      <a class="nav-link-custom {{ request()->routeIs('requestpembelian.track') ? 'active' : '' }}" href="{{ route('requestpembelian.track') }}">
        <i class="bi bi-activity"></i> Track Pengajuan Komponen
      </a>
    @endif
  </div>
</div>

<a class="nav-link-custom d-flex justify-content-between align-items-center {{ request()->routeIs('pengajuan_transaksi.*') ? 'menu-open' : '' }}"
  data-bs-toggle="collapse"
  href="#menuPengajuanTransaksi"
  role="button"
  aria-expanded="{{ request()->routeIs('pengajuan_transaksi.*') ? 'true' : 'false' }}"
  aria-controls="menuPengajuanTransaksi">
    <span>
      <i class="bi bi-receipt"></i> Pengajuan Non Komponen
    </span>
    <i class="bi bi-chevron-down"></i>
</a>

<div class="collapse {{ request()->routeIs('pengajuan_transaksi.*') ? 'show' : '' }}" id="menuPengajuanTransaksi">
  <div class="ms-2 mt-1 d-grid gap-1">
    <a class="nav-link-custom {{ request()->routeIs('pengajuan_transaksi.index') ? 'active' : '' }}"
      href="{{ route('pengajuan_transaksi.index') }}">
      <i class="bi bi-list-ul"></i> Daftar Pengajuan
    </a>
    <a class="nav-link-custom {{ request()->routeIs('pengajuan_transaksi.create_pengajuan') ? 'active' : '' }}"
      href="{{ route('pengajuan_transaksi.create_pengajuan') }}">
      <i class="bi bi-cash-coin"></i> <span class="menu-text">Pengajuan Dana Non Komponen</span>
    </a>
    <a class="nav-link-custom {{ request()->routeIs('pengajuan_transaksi.create_reimbursement') ? 'active' : '' }}"
      href="{{ route('pengajuan_transaksi.create_reimbursement') }}">
      <i class="bi bi-arrow-repeat"></i> Reimbursement
    </a>
  </div>
</div>

@if(in_array(Auth::user()->role, ['admin','bendahara']))
  <a class="nav-link-custom {{ request()->routeIs('requestpembelian.talangan.*') ? 'active' : '' }}" href="{{ route('requestpembelian.talangan.index') }}">
    <i class="bi bi-arrow-left-right"></i> <span class="menu-text">Rekonsiliasi Talangan</span>
  </a>
@endif

@if(in_array(Auth::user()->role, ['admin','bendahara']))
  <div class="menu-title mt-3">Keuangan</div>

  <a class="nav-link-custom {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
    <i class="bi bi-wallet2"></i> Kas
  </a>

  <a class="nav-link-custom {{ request()->routeIs('pencatatan_keuangan') ? 'active' : '' }}" href="{{ route('pencatatan_keuangan') }}">
    <i class="bi bi-journal-text"></i> <span class="menu-text">Pencatatan Keuangan</span>
  </a>

  <a class="nav-link-custom {{ request()->routeIs('laporan_keuangan') ? 'active' : '' }}" href="{{ route('laporan_keuangan') }}">
    <i class="bi bi-graph-up"></i> Laporan Keuangan
  </a>
@endif

@if(in_array(Auth::user()->role, ['admin','bendahara']))
  <div class="menu-title mt-3">Administrasi</div>

  <a class="nav-link-custom d-flex justify-content-between align-items-center {{ $masterDataOpen ? 'menu-open' : '' }}"
    data-bs-toggle="collapse"
    href="#menuMasterData"
    role="button"
    aria-expanded="{{ $masterDataOpen ? 'true' : 'false' }}"
    aria-controls="menuMasterData">
      <span>
        <i class="bi bi-folder2-open"></i> Master Data
      </span>
      <i class="bi bi-chevron-down"></i>
  </a>

  <div class="collapse {{ $masterDataOpen ? 'show' : '' }}" id="menuMasterData">
    <div class="ms-2 mt-1 d-grid gap-1">
      <a class="nav-link-custom {{ request()->routeIs('sumberdana.*') ? 'active' : '' }}" href="{{ route('sumberdana.index') }}">
        <i class="bi bi-cash-coin"></i> Sumber Dana
      </a>

      @if(Auth::user()->role === 'admin')
        <a class="nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
          <i class="bi bi-people"></i> Management User
        </a>
      @endif
    </div>
  </div>
@endif

<div class="menu-title mt-3">Akun</div>
<a class="nav-link-custom {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
  <i class="bi bi-person-circle"></i> Profile Pengguna
</a>
