<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Upload Bukti Pembayaran</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

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
    }

    *{ box-sizing:border-box }
    body{
      background:var(--bg);
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--ink);
    }

    /* Topbar */
    .topbar{ background:linear-gradient(135deg,var(--brand-700),var(--brand)); color:#fff; }
    .brand-title{ font-weight:700; letter-spacing:.3px; }

    /* Layout */
    .app{ display:flex; min-height:calc(100vh - 56px); }
    .sidebar{
      width:260px; background:var(--card); border-right:1px solid var(--line);
      padding:18px; position:sticky; top:0; height:calc(100vh - 56px);
    }
    .menu-title{
      font-size:.75rem; letter-spacing:.08em; text-transform:uppercase;
      color:var(--ink-600); font-weight:600; margin:8px 0 12px;
    }
    .nav-link-custom{
      display:flex; align-items:center; gap:10px; padding:10px 12px;
      border-radius:12px; text-decoration:none; color:var(--ink); font-weight:500;
      transition:.2s;
    }
    .nav-link-custom:hover{ background:var(--brand-50); color:var(--brand-700); }
    .nav-link-custom.active{ background:var(--brand); color:#fff; box-shadow:0 6px 16px rgba(22,163,74,.25); }

    .content{ flex:1; padding:24px; }
    .page-title{ font-size:1.55rem; font-weight:800; }
    .page-sub{ color:var(--ink-600); margin-top:4px; }

    /* Card */
    .card-soft{
      background:var(--card);
      border:1px solid var(--line);
      border-radius:18px;
      box-shadow:0 8px 22px rgba(15,23,42,.06);
    }

    /* FIX tombol putih */
    .btn-brand{
      background:var(--brand) !important;
      border-color:var(--brand) !important;
      color:#fff !important;
      font-weight:800;
      padding:.6rem 1.2rem;
      border-radius:12px;
    }
    .btn-brand:hover{
      background:var(--brand-700) !important;
      border-color:var(--brand-700) !important;
      color:#fff !important;
    }

    /* Mobile */
    @media(max-width:991px){
      .sidebar{ position:fixed; left:-280px; z-index:1040; transition:.2s; }
      .sidebar.open{ left:0; }
      .backdrop{ position:fixed; inset:0; background:rgba(15,23,42,.4); display:none; z-index:1035; }
      .backdrop.show{ display:block; }
      .content{ padding:18px; }
    }
  </style>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
@php
  // ambil header (kalau controller belum kirim)
  $header = $header ?? \App\Models\RequestpembelianHeader::find($detail->id_request_pembelian_header);

  $isAdmin = Auth::user()->role === 'admin';

  // normalisasi status biar aman (kadang ada spasi / huruf besar)
  $statusHeaderRaw = $header->status_request ?? '';
  $statusHeader = strtolower(trim($statusHeaderRaw));
  $statusHeader = str_replace(' ', '_', $statusHeader);

  // aturan upload: approve_request / reject_payment
  $allowUpload = in_array($statusHeader, ['approve_request','reject_payment']);
@endphp

<!-- TOPBAR -->
<nav class="navbar topbar navbar-expand-lg">
  <div class="container-fluid">
    <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    <div class="brand-title">STAS-RG • Request Pembelian</div>
    <div class="ms-auto">@include('navbar')</div>
  </div>
</nav>

<div class="app">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="appSidebar">
    <div class="menu-title">Menu</div>

    <a class="nav-link-custom" href="{{ route('dashboard') }}">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a class="nav-link-custom" href="{{ route('project.index') }}">
      <i class="bi bi-kanban"></i> Project
    </a>

    <a class="nav-link-custom active" href="{{ route('requestpembelian.index') }}">
      <i class="bi bi-bag-check"></i> Request Pembelian
    </a>

    {{-- KAS cuma admin (peneliti ga muncul di halaman upload) --}}
    @if($isAdmin)
      <a class="nav-link-custom {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
        <i class="bi bi-wallet2"></i> Kas
      </a>

      <div class="menu-title mt-3">Administrasi</div>
      <a class="nav-link-custom" href="{{ route('sumberdana.index') }}"><i class="bi bi-cash-coin"></i> Sumber Dana</a>
      <a class="nav-link-custom" href="{{ route('pencatatan_keuangan') }}"><i class="bi bi-journal-text"></i> Pencatatan Keuangan</a>
      <a class="nav-link-custom" href="{{ route('laporan_keuangan') }}"><i class="bi bi-graph-up"></i> Laporan Keuangan</a>
      <a class="nav-link-custom" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Management User</a>
    @endif
  </aside>

  <div class="backdrop" id="backdrop"></div>

  <!-- CONTENT -->
  <main class="content">

    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
      <div>
        <div class="page-title">Upload Bukti Pembayaran</div>
        <div class="page-sub">
          Upload bukti bayar hanya bisa setelah <b>Approve Request</b> atau saat <b>Reject Payment</b> (upload ulang).
        </div>
      </div>

      <a href="{{ route('requestpembelian.detail', $detail->id_request_pembelian_header) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left-short me-1"></i> Kembali
      </a>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
      <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger mt-3">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card card-soft mt-3">
      <div class="card-body">

        <div class="mb-3">
          <div class="fw-bold mb-1">Info</div>
          <div class="text-muted" style="font-size:.92rem">
            Nomor Request: <b>{{ $header->no_request ?? '-' }}</b> •
            Status: <b>{{ $statusHeader ?: '-' }}</b>
          </div>
        </div>

        @if(!$header)
          <div class="alert alert-danger mb-0">
            Data header tidak ditemukan. Coba kembali ke halaman detail.
          </div>

        @elseif(!$allowUpload)
          <div class="alert alert-warning mb-0">
            Bukti bayar belum bisa diunggah. Status harus <b>approve_request</b> atau <b>reject_payment</b>.
          </div>

        @else
          <form action="{{ route('requestpembelian.storebukti', $detail->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- ✅ PENTING: controller biasanya butuh ini --}}
            <input type="hidden" name="id_request_pembelian_header" value="{{ $detail->id_request_pembelian_header }}">

            <div class="mb-3">
              <label class="form-label fw-bold" for="bukti_bayar">Bukti Bayar</label>
              <input type="file" class="form-control" id="bukti_bayar" name="bukti_bayar" accept="image/*" required>
              <div class="form-text">Format: JPG/PNG. Maks 2MB.</div>
            </div>

            <button type="submit" class="btn btn-brand">
              <i class="bi bi-upload me-1"></i> Submit Bukti
            </button>
          </form>
        @endif

      </div>
    </div>

  </main>
</div>

<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // sidebar mobile toggle
  const sidebar = document.getElementById('appSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const backdrop = document.getElementById('backdrop');

  const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
  const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

  toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
  backdrop?.addEventListener('click', closeSidebar);
</script>

</body>
</html>
