<!DOCTYPE html>
<html lang="id">

<head>
  @extends('layouts.app')
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Pembelian</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --brand:#16a34a;
      --brand-700:#15803d;
      --brand-50:#ecfdf5;

      --ink:#0f172a;
      --ink-600:#475569;
      --line:#e2e8f0;

      --bg:#f6f7fb;
      --card:#ffffff;

      --danger:#ef4444;

      --shadow:0 10px 30px rgba(15,23,42,.08);
      --shadow2:0 18px 40px rgba(15,23,42,.10);
    }

    *{ box-sizing:border-box }
    body{
      margin:0;
      background:var(--bg);
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--ink);
    }

    /* Topbar */
    .topbar{
      position:sticky; top:0; z-index:1030;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      border-bottom:1px solid rgba(255,255,255,.18);
      height:56px;
    }
    .brand{
      display:flex;align-items:center;gap:10px;
      font-weight:800;
      letter-spacing:.2px;
    }
    .brand-badge{
      font-size:.72rem; font-weight:800;
      padding:.22rem .55rem; border-radius:999px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.22);
      white-space:nowrap;
    }

    /* Layout */
    .app{ display:flex; min-height:calc(100vh - 56px); }

    .sidebar{
      width:260px;
      background:var(--card);
      border-right:1px solid var(--line);
      padding:14px;
      position:sticky; top:56px;
      height:calc(100vh - 56px);
      overflow:auto;
    }

    .menu-title{
      font-size:.72rem;
      letter-spacing:.08em;
      color:var(--ink-600);
      text-transform:uppercase;
      margin:8px 0;
      font-weight:700;
    }

    .nav-link-custom{
      display:flex; align-items:center; gap:10px;
      padding:9px 10px;
      border-radius:14px;
      text-decoration:none;
      color:var(--ink);
      font-weight:600;
      font-size:.92rem;
      line-height:1;
      transition:.18s;
      white-space:nowrap;
    }
    .nav-link-custom i{ font-size:1.05rem; }

    .nav-link-custom:hover{
      background:var(--brand-50);
      color:var(--brand-700);
      transform:translateX(2px);
    }
    .nav-link-custom.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      box-shadow:0 16px 28px rgba(2,6,23,.12);
      font-weight:700;
    }

    .content{ flex:1; padding:18px 18px 22px; }

    /* HERO */
    .hero{
      border-radius:22px;
      padding:18px;
      background:
        radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
        radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
        linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
      border:1px solid rgba(226,232,240,.95);
      box-shadow:var(--shadow);
      position:relative;
      overflow:hidden;
      margin-bottom:14px;
    }
    .hero::after{
      content:"";
      position:absolute; inset:-1px;
      background:
        radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
        radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
      pointer-events:none;
      opacity:.65;
    }
    .hero-inner{ position:relative; z-index:2; width:100%; }

    .hero-left .title{
      font-size:1.65rem;
      font-weight:800;
      margin:0;
      letter-spacing:-.2px;
    }
    .hero-left .sub{
      margin:6px 0 0;
      color:var(--ink-600);
      font-weight:500;
    }

    .tools-row{
      margin-top:14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }
    .tools-left{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .tools-right{
      margin-left:auto;
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }

    .btn-brand{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
      color:#fff;
      white-space:nowrap;
      text-decoration:none;
    }
    .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }

    .btn-soft{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:#fff;
      color:var(--ink);
      border:1px solid rgba(226,232,240,.95);
      box-shadow:0 10px 26px rgba(15,23,42,.05);
      white-space:nowrap;
      text-decoration:none;
    }
    .btn-soft:hover{
      background:var(--brand-50);
      transform:translateY(-1px);
      color:var(--brand-700);
      border-color:rgba(226,232,240,.95);
    }

    /* Card */
    .card-soft{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    .card-soft .card-body{ padding:18px; }

    .form-label{
      font-weight:800;
      color:var(--ink);
      font-size:.92rem;
    }
    .form-control, .form-select{
      border-radius:14px;
      border:1px solid rgba(226,232,240,.95);
      padding:.65rem .85rem;
      font-weight:600;
    }
    .help-text{ color:var(--ink-600); font-size:.86rem; font-weight:500; }

    /* Table */
    .table-wrap{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      overflow:hidden;
      margin-top:12px;
      box-shadow:var(--shadow);
    }
    .table-modern{ margin:0; font-size:.92rem; border-collapse:separate; border-spacing:0; }
    .table-modern thead th{
      background:#f8fafc;
      color:var(--ink-600);
      font-weight:900;
      text-transform:uppercase;
      font-size:.72rem;
      letter-spacing:.08em;
      padding:14px 12px;
      border-bottom:1px solid rgba(226,232,240,.95);
    }
    .table-modern tbody td{
      padding:14px 12px;
      vertical-align:middle;
      border-top:1px solid #eef2f7;
      font-weight:500;
    }
    .table-striped > tbody > tr:nth-of-type(odd){ background:#fcfcfd; }
    .table-modern tbody tr:hover{ background:var(--brand-50); transition:.12s; }

    .action-link a{
      font-weight:800;
      text-decoration:none;
      margin-right:10px;
      color:var(--brand-700);
    }
    .action-link a:hover{ text-decoration:underline; }

    /* Mobile sidebar */
    .backdrop{
      display:none;
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.38);
      z-index:1035;
    }
    .backdrop.show{ display:block; }
    @media(max-width:991px){
      .sidebar{
        position:fixed;
        left:-290px;
        top:56px;
        height:calc(100vh - 56px);
        z-index:1040;
        transition:left .2s;
      }
      .sidebar.open{ left:0; }
      .content{ padding:14px; }
    }
  </style>
</head>

<body>

  <!-- TOPBAR -->
  <nav class="navbar topbar">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>

      <div class="brand">
        <span>STAS-RG</span>
        <span class="brand-badge">{{ Auth::user()->role === 'admin' ? 'ADMIN' : 'PENELITI' }}</span>
      </div>

      <div class="ms-auto">
        @include('navbar')
      </div>
    </div>
  </nav>

  <div class="app">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="appSidebar">
      <div class="menu-title">Menu</div>

      <a class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>

      <a class="nav-link-custom {{ request()->routeIs('project.*') ? 'active' : '' }}" href="{{ route('project.index') }}">
        <i class="bi bi-kanban"></i> Project
      </a>

      <a class="nav-link-custom {{ request()->routeIs('requestpembelian.*') ? 'active' : '' }}" href="{{ route('requestpembelian.index') }}">
        <i class="bi bi-bag-check"></i> Request Pembelian
      </a>

      @if (Auth::user()->role == 'admin')
        <div class="menu-title mt-3">Administrasi</div>

        <a class="nav-link-custom {{ request()->routeIs('sumberdana.*') ? 'active' : '' }}" href="{{ route('sumberdana.index') }}">
          <i class="bi bi-cash-coin"></i> Sumber Dana
        </a>

        <a class="nav-link-custom {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
          <i class="bi bi-wallet2"></i> Kas
        </a>

        <a class="nav-link-custom {{ request()->routeIs('pencatatan_keuangan') ? 'active' : '' }}" href="{{ route('pencatatan_keuangan') }}">
          <i class="bi bi-journal-text"></i> Pencatatan Keuangan
        </a>

        <a class="nav-link-custom {{ request()->routeIs('laporan_keuangan') ? 'active' : '' }}" href="{{ route('laporan_keuangan') }}">
          <i class="bi bi-graph-up"></i> Laporan Keuangan
        </a>

        <a class="nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
          <i class="bi bi-people"></i> Management User
        </a>
      @endif
    </aside>

    <div class="backdrop" id="backdrop"></div>

    <!-- CONTENT -->
    <main class="content">

      <!-- HERO -->
      <section class="hero">
        <div class="hero-inner">
          <div class="hero-left">
            <h1 class="title">Tambah Request Pembelian</h1>
            <p class="sub">Lengkapi tim penelitian, tanggal request, dan detail barang yang diajukan.</p>
          </div>

          <div class="tools-row">
            <div class="tools-left">
              <a href="{{ route('requestpembelian.index') }}" class="btn-soft">
                <i class="bi bi-arrow-left-short"></i> Lihat Daftar Request
              </a>
            </div>

            <div class="tools-right">
              <a
                href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
                target="_blank"
                rel="noopener"
                class="btn-soft"
                title="Buka Manual Book"
              >
                <i class="bi bi-journal-bookmark"></i> Manual Book
              </a>
            </div>
          </div>
        </div>
      </section>

      <div class="card card-soft mb-3">
        <div class="card-body">
          <form action="{{ route('requestpembelian.update', $request_pembelian->id) }}" method="POST">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="id_project" class="form-label">Tim Penelitian</label>
                <select class="form-select" id="id_project" name="id_project">
                  <option value="" selected disabled> -> Pilih Tim Penelitian <- </option>
                  @foreach ($project as $p)
                    <option value="{{ $p->id }}" {{ $p->id == $request_pembelian->id_project ? 'selected' : '' }}>
                      {{ $p->nama_project }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label for="tgl_request" class="form-label">Tanggal Request</label>
                <input type="date" class="form-control" id="tgl_request" name="tgl_request"
                  value="{{ $request_pembelian->tgl_request }}" max="{{ date('Y-m-d') }}">
              </div>
            </div>

            <div class="mt-3">
              <button class="btn-brand" type="submit">
                <i class="bi bi-check2-circle"></i> SUBMIT
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="table-wrap">
        <div class="p-3 border-bottom" style="background:#fff;">
          <div class="fw-bold">Detail Barang</div>
          <div class="help-text">Tambah item barang, qty, harga, dan link pembelian.</div>
        </div>

        <div class="p-3">
          <form action="{{ route('requestpembelian.storedetail') }}" method="POST">
            @csrf
            <input type="hidden" name="id_request_pembelian_header" value="{{ $request_pembelian->id }}">

            <table class="table table-modern table-striped align-middle">
              <thead>
                <tr>
                  <th>Nama Barang</th>
                  <th style="width:120px;">Qty</th>
                  <th style="width:180px;">Harga</th>
                  <th>Link Pembelian</th>
                  <th style="width:160px;"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($detail as $d)
                  <tr>
                    <td>{{ $d->nama_barang }}</td>
                    <td>{{ $d->kuantitas }}</td>
                    <td>{{ $d->harga }}</td>
                    <td>{{ $d->link_pembelian }}</td>
                    <td class="action-link">
                      <a href="{{ route('requestpembelian.editdetail', $d->id) }}">Edit</a>
                      <a href="{{ route('requestpembelian.destroydetail', $d->id) }}">Delete</a>
                    </td>
                  </tr>
                @endforeach

                <tr>
                  <td>
                    <input type="text" class="form-control" name="nama_barang" placeholder="Nama Barang">
                  </td>
                  <td>
                    <input type="number" class="form-control" name="kuantitas" placeholder="Qty">
                  </td>
                  <td>
                    <input type="text" class="form-control" id="harga" name="harga" placeholder="Harga"
                      value="{{ old('harga', $d->harga ?? '') }}">
                  </td>
                  <td>
                    <input type="text" class="form-control" name="link_pembelian" placeholder="Link Pembelian">
                  </td>
                  <td>
                    <button class="btn-brand" type="submit">
                      <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // sidebar toggle (mobile)
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');

    const openSidebar = () => { sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar = () => { sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

    toggleBtn?.addEventListener('click', () => {
      sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    backdrop?.addEventListener('click', closeSidebar);
  </script>

  <script>
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

    document.addEventListener("DOMContentLoaded", function () {
      const hargaInput = document.getElementById("harga");

      if (!hargaInput) return;

      hargaInput.addEventListener("input", function () {
        const value = this.value.replace(/Rp. /, '');
        const formatted = formatRupiah(value);
        this.value = formatted;
      });

      // Optional: Hapus format saat submit form detail (form terdekat)
      const detailForm = hargaInput.closest("form");
      detailForm?.addEventListener("submit", function () {
        hargaInput.value = hargaInput.value.replace(/[^0-9]/g, '');
      });
    });
  </script>

</body>

</html>
