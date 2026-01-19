{{-- resources/views/detail_project.blade.php --}}
@extends('layouts.app')

@section('content')
@php
  $mode = request('mode', 'detail'); // 'detail' | 'rab'
  $isRabMode = ($mode === 'rab');

  $role = strtolower(Auth::user()->role ?? '');
  $isAdmin = ($role === 'admin');
  $isBendahara = ($role === 'bendahara');

  $isClosed = (strtolower($project->status ?? 'aktif') === 'ditutup');

  $wf = strtolower($project->workflow_status ?? '');
  $isFunded = ($wf === 'funded');
  $isFinalized = ($wf === 'finalized');

  $creatorId = (int)($project->user_id_created ?? $project->created_by ?? 0);
  $isCreator = $creatorId > 0 ? ((int)Auth::id() === $creatorId) : false;

  // ✅ dari controller: apakah sudah pernah submit revisi
  $hasRabSubmitted = (bool)($hasRabSubmitted ?? false);

  // ✅ mode edit hanya sekali (setelah submit -> read-only sampai finalize)
  $canEditRab = $isRabMode && $isFunded && !$isClosed && $isCreator && !$isFinalized && !$hasRabSubmitted;

  $fundedTotal = (int)($fundedTotal ?? 0);
  $rupiah = fn($n)=>'Rp '.number_format((int)$n,0,',','.');

  $defaultStep = $isRabMode ? 'dana' : 'ringkasan';

  // ===== Ketua logic =====
  $ketuaId = (int)($project->ketua_id ?? $creatorId ?? 0);
  $canChangeKetua = (!$isClosed && ( $isAdmin || $isCreator ));
@endphp

<style>
  :root{
    --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
    --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0;
    --bg:#f6f7fb; --card:#ffffff;
    --shadow:0 10px 30px rgba(15,23,42,.08);
    --shadow-2:0 18px 45px rgba(15,23,42,.12);
  }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:var(--ink);}

  .topbar{
    position:sticky; top:0; z-index:1030;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    color:#fff;border-bottom:1px solid rgba(255,255,255,.18);
    height:56px;
  }
  .brand{display:flex;align-items:center;gap:10px;font-weight:800;letter-spacing:.2px}
  .brand-badge{
    font-size:.72rem;font-weight:800;padding:.22rem .55rem;border-radius:999px;
    background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.22);
    white-space:nowrap;
  }

  .app{display:flex;min-height:calc(100vh - 56px)}
  .sidebar{
    width:260px;background:var(--card);
    border-right:1px solid var(--line);
    padding:14px;position:sticky; top:56px;
    height:calc(100vh - 56px);overflow:auto;
  }
  .menu-title{font-size:.72rem;letter-spacing:.08em;color:var(--ink-600);text-transform:uppercase;margin:8px 0;font-weight:700;}
  .nav-link-custom{
    display:flex;align-items:center;gap:10px;
    padding:9px 10px;border-radius:14px;text-decoration:none;
    color:var(--ink);font-weight:600;font-size:.92rem;line-height:1;
    transition:.18s;white-space:nowrap;
  }
  .nav-link-custom i{font-size:1.05rem}
  .nav-link-custom:hover{background:var(--brand-50);color:var(--brand-700);transform:translateX(2px)}
  .nav-link-custom.active{
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    color:#fff;box-shadow:0 16px 28px rgba(2,6,23,.12);font-weight:700;
  }

  .content{flex:1;padding:18px 18px 22px;min-width:0}
  .main-inner{width:100%;max-width:100%}

  .hero{
    border-radius:22px;padding:18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow:var(--shadow);
    position:relative;overflow:hidden;margin-bottom:14px;
  }
  .hero::after{
    content:"";position:absolute;inset:-1px;
    background:
      radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
      radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
    pointer-events:none;opacity:.65;
  }
  .hero-inner{position:relative;z-index:2;width:100%}
  .hero-left .title{font-size:1.65rem;font-weight:900;margin:0;letter-spacing:-.2px}
  .hero-left .sub{margin:6px 0 0;color:var(--ink-600);font-weight:500}
  .tools-row{margin-top:14px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
  .tools-right{margin-left:auto;display:flex;align-items:center;gap:10px;flex-wrap:wrap}

  .btn-manual{
    height:38px;display:inline-flex;align-items:center;gap:8px;
    border-radius:999px;font-weight:800;padding:0 14px;
    background:#fff;color:var(--ink);
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    white-space:nowrap;text-decoration:none;
    transition:.15s;
  }
  .btn-manual:hover{background:var(--brand-50);transform:translateY(-1px);color:var(--brand-700)}

  .card{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    box-shadow:var(--shadow);
  }
  .card h5{font-weight:900}
  .subtle{color:var(--ink-600)}
  .label-sm{font-size:.72rem;letter-spacing:.08em;color:var(--ink-600);text-transform:uppercase;font-weight:900}
  .value-lg{font-weight:900}

  .badge-proj{
    display:inline-flex;align-items:center;gap:6px;
    padding:.35rem .55rem;border-radius:999px;font-weight:900;border:1px solid transparent;
    white-space:nowrap;
  }
  .badge-proj-planned{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe}
  .badge-proj-on-going{background:#ecfdf5;color:#166534;border-color:#bbf7d0}
  .badge-proj-paused{background:#fff7ed;color:#9a3412;border-color:#fed7aa}
  .badge-proj-cancelled{background:#fef2f2;color:#b91c1c;border-color:#fecaca}
  .badge-proj-completed{background:#eefdfb;color:#0f766e;border-color:#99f6e4}
  .badge-proj-closed{background:#f1f5f9;color:#0f172a;border-color:#cbd5e1}

  .table-modern{
    margin:0;font-size:.92rem;
    border-collapse:separate;border-spacing:0;
    overflow:hidden;border-radius:16px;
    border:1px solid rgba(226,232,240,.95);
  }
  .table-modern thead th{
    background:#f8fafc;color:var(--ink-600);
    font-weight:900;text-transform:uppercase;
    font-size:.72rem;letter-spacing:.08em;
    padding:14px 12px;
    border-bottom:1px solid rgba(226,232,240,.95);
    position:sticky;top:0;z-index:1;
    white-space:nowrap;
  }
  .table-modern tbody td{
    padding:14px 12px;
    vertical-align:middle;
    border-top:1px solid #eef2f7;
    font-weight:600;
  }
  .table-modern tbody tr:hover{background:var(--brand-50);transition:.12s}
  .tnum{font-variant-numeric:tabular-nums}
  .tr-over{background:#fef2f2}
  .tr-near{background:#fff7ed}

  .btn-eq{
    height:40px;
    display:inline-flex;align-items:center;justify-content:center;
    border-radius:14px;font-weight:900;
  }

  /* ===== Anggota cards ===== */
  .member-card{
    border-radius:18px;
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    box-shadow:0 10px 24px rgba(15,23,42,.05);
    padding:14px;
    height:100%;
    position:relative;
    overflow:hidden;
  }
  .btn-mini{
    height:34px;
    border-radius:999px;
    font-weight:900;
    padding:0 12px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    white-space:nowrap;
  }
  .member-card::after{
    content:"";
    position:absolute; inset:-1px;
    background:radial-gradient(600px 160px at 10% 0%, rgba(22,163,74,.10), transparent 60%);
    pointer-events:none;
  }
  .member-inner{position:relative; z-index:2}
  .member-name{font-weight:900; font-size:1.02rem; margin:0}
  .member-meta{margin-top:8px; display:flex; gap:8px; flex-wrap:wrap}
  .pill-badge{
    border-radius:999px; padding:7px 10px; font-weight:900; font-size:.78rem;
    display:inline-flex; align-items:center; gap:8px; border:1px solid transparent;
    white-space:nowrap;
  }
  .pill-ketua{background:rgba(22,163,74,.12); color:#166534; border-color:rgba(22,163,74,.20)}
  .pill-anggota{background:#f1f5f9; color:#0f172a; border-color:#e2e8f0}
  .pill-you{background:#ede9fe; color:#5b21b6; border-color:#ddd6fe}
  .btn-mini{
    height:34px; border-radius:999px; font-weight:900; padding:0 12px;
    display:inline-flex; align-items:center; justify-content:center;
  }

  .backdrop{display:none;position:fixed;inset:0;background:rgba(15,23,42,.38);z-index:1035}
  .backdrop.show{display:block}

  .stepper{
    display:flex; gap:10px; flex-wrap:wrap;
    border-bottom:1px solid rgba(226,232,240,.95);
    padding-bottom:10px; margin:10px 0 14px;
  }
  .step-btn{
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    border-radius:999px;
    padding:10px 14px;
    font-weight:900;
    display:flex; align-items:center; gap:10px;
    box-shadow:0 10px 20px rgba(15,23,42,.05);
    cursor:pointer;
  }
  .step-btn .num{
    width:28px; height:28px; border-radius:10px;
    display:grid; place-items:center;
    background:#f1f5f9; border:1px solid #e2e8f0;
    font-weight:900;
  }
  .step-btn.active{
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    color:#fff; border-color:transparent;
  }
  .step-btn.active .num{
    background:rgba(255,255,255,.18);
    border-color:rgba(255,255,255,.28);
    color:#fff;
  }
  .step-pane{display:none;}
  .step-pane.active{display:block;}

  /* ===== RAB ===== */
  .rab-alert{
    border-radius:18px;border:1px solid rgba(16,185,129,.22);
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.16), transparent 60%),
      linear-gradient(135deg, rgba(236,253,245,.78), rgba(255,255,255,.9));
    box-shadow:0 10px 26px rgba(15,23,42,.06);
  }
  .rab-kpi{
    display:flex;align-items:center;justify-content:space-between;
    gap:10px;flex-wrap:wrap;font-weight:900;
  }
  .rab-kpi .pill{
    display:inline-flex;align-items:center;gap:8px;
    padding:7px 12px;border-radius:999px;
    background:rgba(16,185,129,.10);color:#047857;
    border:1px solid rgba(16,185,129,.18);
    font-size:.88rem;
  }
  .rab-kpi .pill.warn{background:rgba(245,158,11,.12);color:#b45309;border-color:rgba(245,158,11,.22)}
  .rab-kpi .pill.danger{background:rgba(239,68,68,.12);color:#b91c1c;border-color:rgba(239,68,68,.22)}

  .rab-input{
    height:42px;border-radius:14px;font-weight:900;padding-right:12px;
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 8px 18px rgba(15,23,42,.05);
  }
  .rab-input:focus{
    border-color:rgba(22,163,74,.45) !important;
    box-shadow:0 0 0 .2rem rgba(22,163,74,.12) !important;
  }
  .awal-pill{
    display:inline-flex;align-items:center;gap:8px;
    padding:6px 10px;border-radius:999px;
    background:#f1f5f9;border:1px solid #e2e8f0;
    color:#0f172a;font-weight:900;
    white-space:nowrap;
  }

  .savebar{position:sticky;bottom:14px;z-index:1020;margin-top:12px}
  .savebar-inner{
    background:#fff;border:1px solid rgba(226,232,240,.95);
    border-radius:18px;box-shadow:var(--shadow-2);
    padding:10px 12px;display:flex;align-items:center;justify-content:space-between;
    gap:10px;flex-wrap:wrap;
  }
  .savebar-meta{font-weight:800;color:var(--ink-600);font-size:.9rem}
  .savebar-meta b{color:var(--ink)}
  .btn-save{
    height:42px;border-radius:999px;font-weight:900;padding:0 16px;
    box-shadow:0 14px 26px rgba(22,163,74,.18);
  }

  @media (max-width:991.98px){
    .sidebar{position:fixed;left:-290px;top:56px;height:calc(100vh - 56px);z-index:1040;transition:left .2s}
    .sidebar.open{left:0}
    .content{padding:14px}
  }
  @media print{
    .topbar,.sidebar,.hero,.backdrop,.savebar{display:none !important}
    .content{padding:0}
    .card{box-shadow:none;border:1px solid #ccc}
  }
</style>

<!-- Topbar -->
<nav class="navbar topbar">
  <div class="container-fluid">
    <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle" aria-label="Buka tutup sidebar">
      <i class="bi bi-list"></i>
    </button>

    <div class="brand">
      <span>STAS-RG</span>
      <span class="brand-badge">
        @if($isAdmin) ADMIN
        @elseif($isBendahara) BENDAHARA
        @else PENELITI
        @endif
      </span>
    </div>

    <div class="ms-auto">@include('navbar')</div>
  </div>
</nav>

<div class="app">
  <!-- Sidebar -->
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

    @if(in_array($role, ['admin','bendahara']))
      <div class="menu-title mt-3">Keuangan</div>

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

      <a class="nav-link-custom {{ request()->routeIs('funding.*') ? 'active' : '' }}" href="{{ route('funding.index') }}">
        <i class="bi bi-cash-stack"></i> Dana Cair
      </a>
    @endif

    @if($isAdmin)
      <div class="menu-title mt-3">Administrasi</div>

      <a class="nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <i class="bi bi-people"></i> Management User
      </a>
    @endif
  </aside>

  <div class="backdrop" id="backdrop"></div>

  <!-- Content -->
  <main class="content">
    <div class="main-inner">

      {{-- ALERT --}}
      @if ($message = Session::get('success'))
        <div class="alert alert-success">{{ $message }}</div>
      @endif
      @if ($message = Session::get('error'))
        <div class="alert alert-danger">{{ $message }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          <div class="fw-bold mb-1">Ada yang perlu dibenerin:</div>
          <ul class="mb-0">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- HERO -->
      <section class="hero">
        <div class="hero-inner">
          <div class="hero-left">
            <h1 class="title">{{ $isRabMode ? 'Set Ulang RAB' : 'Detail Project' }}</h1>
            <p class="sub">
              @if($isRabMode)
                Masukkan revisi anggaran. Revisi tidak akan dipakai sebelum admin/bendahara finalize.
              @else
                Ringkasan proyek, anggota tim, pendanaan, anggaran, dan detail pembelian.
              @endif
            </p>
          </div>

          <div class="tools-row">
            <div class="tools-right">
              <a class="btn-manual"
                 href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
                 target="_blank" rel="noopener">
                <i class="bi bi-book"></i> Manual Book
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $isRabMode ? 'Set Ulang RAB' : 'Detail' }}</li>
        </ol>
      </nav>

      <!-- Header -->
      @php
        $statusRaw = strtolower($project->status_project ?? $project->status ?? 'on_going');
        $statusMap = [
          'planned'   => ['cls'=>'badge-proj-planned',   'label'=>'Planned'],
          'on_going'  => ['cls'=>'badge-proj-on-going',  'label'=>'On Going'],
          'ongoing'   => ['cls'=>'badge-proj-on-going',  'label'=>'On Going'],
          'paused'    => ['cls'=>'badge-proj-paused',    'label'=>'Paused'],
          'cancelled' => ['cls'=>'badge-proj-cancelled', 'label'=>'Cancelled'],
          'canceled'  => ['cls'=>'badge-proj-cancelled', 'label'=>'Cancelled'],
          'completed' => ['cls'=>'badge-proj-completed', 'label'=>'Completed'],
          'done'      => ['cls'=>'badge-proj-completed', 'label'=>'Completed'],
          'ditutup'   => ['cls'=>'badge-proj-closed',    'label'=>'Ditutup'],
          'closed'    => ['cls'=>'badge-proj-closed',    'label'=>'Ditutup'],
          'aktif'     => ['cls'=>'badge-proj-on-going',  'label'=>'Aktif'],
        ];
        $projBadge = $statusMap[$statusRaw]['cls'] ?? 'badge-proj-on-going';
        $projLabel = $statusMap[$statusRaw]['label'] ?? ucwords(str_replace('_',' ', $statusRaw));
      @endphp

      <div class="d-flex flex-wrap align-items-baseline justify-content-between mb-1">
        <div class="mb-2">
          <div class="h3 mb-1" style="font-weight:900">{{ $project->nama_project }}</div>
          <div class="subtle">
            Ringkasan proyek & anggaran • Diupdate {{ \Carbon\Carbon::parse($project->updated_at ?? now())->diffForHumans() }}

            @if(!$isFinalized)
              <span class="ms-2 badge bg-warning-subtle text-warning-emphasis" style="border-radius:999px">
                Revisi RAB belum finalize (pakai anggaran awal)
              </span>
            @else
              <span class="ms-2 badge bg-success-subtle text-success" style="border-radius:999px">
                RAB sudah finalized (pakai anggaran revisi)
              </span>
            @endif
          </div>

          @if($isRabMode)
            <div class="small mt-1">
              <a href="{{ route('project.show', $project->id) }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Kembali ke Detail
              </a>
            </div>
          @endif
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="badge-proj {{ $projBadge }}">{{ $projLabel }}</span>

          {{-- tombol set ulang: hanya sekali sebelum submit revisi --}}
          @if(
            !$isRabMode &&
            $isFunded &&
            !$isClosed &&
            $isCreator &&
            !$isFinalized &&
            empty($hasRabSubmitted)
          )
            <a href="{{ route('project.show', $project->id) }}?mode=rab"
               class="btn btn-success btn-sm"
               style="border-radius:999px;font-weight:900">
              <i class="bi bi-pencil-square me-1"></i> Set Ulang RAB
            </a>
          @endif

          {{-- badge setelah submit revisi --}}
          @if(!empty($hasRabSubmitted) && !$isFinalized)
            <span class="badge bg-warning-subtle text-warning-emphasis" style="border-radius:999px">
              Revisi RAB sudah dikirim (menunggu finalisasi)
            </span>
          @endif
        </div>
      </div>

      <!-- Stepper -->
      <div class="stepper" id="stepper">
        <button type="button" class="step-btn" data-step="ringkasan">
          <span class="num">1</span> Ringkasan
        </button>
        <button type="button" class="step-btn" data-step="dana">
          <span class="num">2</span> Detail Dana
        </button>
        <button type="button" class="step-btn" data-step="pembelian">
          <span class="num">3</span> Detail Pembelian
        </button>
      </div>

      {{-- Alert aturan (khusus mode rab) --}}
      @if($isRabMode)
        @if(!$isFunded)
          <div class="alert alert-warning rab-alert">
            <b>Belum bisa set ulang RAB.</b> Project belum berstatus <b>Dana Cair</b>.
          </div>
        @elseif($isFinalized)
          <div class="alert alert-secondary rab-alert">
            <b>Project sudah finalized.</b> RAB terkunci dan tidak bisa diubah lagi.
          </div>
        @elseif($isClosed)
          <div class="alert alert-secondary rab-alert">
            <b>Project sudah ditutup.</b> Data terkunci (read-only).
          </div>
        @elseif(!$isCreator)
          <div class="alert alert-warning rab-alert">
            <b>Akses dibatasi.</b> Set ulang RAB hanya bisa dilakukan oleh pengaju/ketua project.
          </div>
        @elseif(!empty($hasRabSubmitted))
          <div class="alert alert-warning rab-alert">
            <b>Revisi sudah dikirim.</b> Saat ini <b>menunggu finalisasi</b> admin/bendahara. Data revisi tidak bisa diubah lagi.
          </div>
        @else
          <div class="alert rab-alert mb-3">
            <div class="rab-kpi">
              <div>
                <i class="bi bi-cash-coin me-1"></i>
                Dana cair masuk: <span class="tnum">{{ $rupiah($fundedTotal) }}</span>
              </div>
              <span class="pill" id="pillSisa">Sisa kuota: <span class="tnum" id="sisaKuota">Rp 0</span></span>
            </div>
            <div class="small subtle mt-1">
              Total revisi RAB tidak boleh melebihi dana cair. Revisi <b>tidak langsung dipakai</b> sebelum admin/bendahara finalize.
            </div>
          </div>
        @endif
      @endif

      <!-- ====== PANE 1: RINGKASAN ====== -->
      <div class="step-pane" id="pane-ringkasan">
        <div class="row g-3 mt-1">
          <div class="col-lg-8">
            <div class="card p-3 h-100">
              <h5 class="mb-2 text-center">Deskripsi Project</h5>

              @php
                $plain = trim(preg_replace('/\s+/u',' ', strip_tags($project->deskripsi ?? '')));
                $wordLimit = 60;
                $words = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY);
                $snippet = implode(' ', array_slice($words, 0, $wordLimit));
                $isTrimmed = count($words) > $wordLimit;
              @endphp

              <p class="mb-2">{{ $snippet }}@if($isTrimmed)…@endif</p>

              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                <h5 class="mb-0">Anggota Tim Riset</h5>
                <div class="small subtle">
                  Ketua: <b>
                    @php
                      $ketuaName = null;
                      foreach(($anggota ?? []) as $a){ if((int)$a->id === $ketuaId){ $ketuaName = $a->name; break; } }
                    @endphp
                    {{ $ketuaName ?? 'Belum ditentukan' }}
                  </b>
                </div>
              </div>

              @if(($anggota ?? collect())->count() === 0)
                <div class="text-center subtle py-4">Belum ada anggota tim.</div>
              @else
                <div class="row g-3 mt-1">
                  @foreach($anggota as $a)
                    @php
                      $isKetua = ((int)$a->id === $ketuaId);
                      $isMe = ((int)$a->id === (int)Auth::id());
                    @endphp

                    <div class="col-12 col-md-6 col-xl-4">
                      <div class="member-card">
                        <div class="member-inner d-flex flex-column h-100">

                          {{-- Header: Nama + badges --}}
                          <div class="member-head">
                            <p class="member-name mb-1">{{ $a->name }}</p>
                            <div class="member-meta">
                              @if($isKetua)
                                <span class="pill-badge pill-ketua">
                                  <i class="bi bi-star-fill"></i> Ketua Project
                                </span>
                              @else
                                <span class="pill-badge pill-anggota">Anggota</span>
                              @endif

                              @if($isMe)
                                <span class="pill-badge pill-you">
                                  <i class="bi bi-person-check"></i> Kamu
                                </span>
                              @endif
                            </div>
                          </div>

                          {{-- Body (optional helper text) --}}
                          @if($isKetua)
                            <div class="member-note small subtle mt-2">
                            </div>
                          @else
                            <div class="member-note small subtle mt-2">&nbsp;</div>
                          @endif

                          {{-- Actions: selalu di bawah --}}
                          <div class="member-actions mt-auto d-flex gap-2 justify-content-end flex-wrap">
                            @if($canChangeKetua && !$isKetua)
                            <form action="{{ route('project.setKetua', $project->id) }}" method="POST" class="m-0">

                                @csrf
                                <input type="hidden" name="ketua_id" value="{{ $a->id }}">
                                <button type="submit" class="btn btn-outline-success btn-mini">
                                  Jadikan Ketua
                                </button>
                              </form>

                              <form action="{{ route('project.member.remove', [$project->id, $a->id]) }}" method="POST" class="m-0"
                                    onsubmit="return confirm('Yakin hapus anggota ini dari project?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-mini">
                                  Hapus
                                </button>
                              </form>
                            @endif
                          </div>
                          @if($isKetua)
                            <div class="small subtle mt-2">
                              Ketua bertanggung jawab mengelola tim & proses pengajuan.
                            </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>

                <div class="small subtle mt-2">Total: {{ ($anggota ?? collect())->count() }} orang</div>
              @endif

              {{-- Tambah Anggota --}}
              @if($isAdmin)
                <div class="mt-3">
                  <form id="addMemberForm" action="{{ route('detailproject.store') }}" method="POST" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="id_project" value="{{ $project->id }}">

                    <div class="col-md-9">
                      <label for="id_user" class="form-label mb-1">Tambah Anggota (Search)</label>
                      <select id="id_user" name="id_user" class="form-select"
                              placeholder="Ketik nama untuk mencari…" autocomplete="off"
                              data-url="{{ \Illuminate\Support\Facades\Route::has('users.search') ? route('users.search') : '' }}">
                        @foreach ($users as $user)
                          <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-3">
                      <button type="submit" class="btn btn-success w-100 btn-eq">Tambah</button>
                    </div>

                    <div class="col-12">
                      <div class="form-text">Cari nama. Support pencarian server-side jika route <code>users.search</code> tersedia.</div>
                    </div>
                  </form>
                </div>
              @endif
            </div>
          </div>

          <div class="col-lg-4 d-flex flex-column gap-3">
            <!-- Aksi -->
            <div class="card p-3">
              <h5 class="mb-3">Aksi</h5>

              <div class="d-grid gap-2">
                <a class="btn btn-outline-secondary btn-eq" href="{{ route('project.downloadproposal', $project->id) }}">
                  <i class="bi bi-download me-1"></i> Download Proposal
                </a>

                <a class="btn btn-outline-secondary btn-eq" href="{{ route('project.downloadrab', $project->id) }}">
                  <i class="bi bi-download me-1"></i> Download RAB
                </a>

                @if($canEditRab)
                  <small class="subtle">Tombol simpan revisi ada di bawah (sticky bar) di tab <b>Detail Dana</b>.</small>
                @endif

                @if($isAdmin)
                  @if(!$isClosed)
                    <button type="button" class="btn btn-danger btn-eq js-close-project"
                      data-id="{{ $project->id }}" data-nama="{{ $project->nama_project }}">
                      <i class="bi bi-lock-fill me-1"></i> Tutup Project
                    </button>

                    <small class="subtle">
                      Saat ditutup, sistem menghitung sisa dana dan otomatis memasukkannya ke Kas (jika ada).
                    </small>
                  @else
                    <div class="alert alert-secondary mb-0">
                      <strong><i class="bi bi-check-circle me-1"></i> Project sudah ditutup.</strong><br>
                      Transaksi project akan terkunci (read-only).
                    </div>
                  @endif
                @endif

                @if(!$canChangeKetua)
                  <small class="subtle">
                    Catatan: Ganti ketua hanya bisa dilakukan oleh <b>Admin</b> atau <b>Pengaju</b>,
                    serta project belum <b>ditutup</b> dan belum <b>finalized</b>.
                  </small>
                @endif
              </div>
            </div>

            <!-- Pendanaan -->
            @php
              $sdObj   = $project->sumberDana ?? ($sumber_dana ?? null);
              $sdJenis = $sdObj ? ucfirst(strtolower($sdObj->jenis_pendanaan ?? 'Internal')) : 'Internal';
              $sdNama  = $sdObj->nama_sumber_dana ?? 'Tidak tersedia';
            @endphp

            <div class="card p-3">
              <h5 class="mb-3">Pendanaan</h5>
              <div class="row g-2">
                <div class="col-6">
                  <div class="label-sm">Sumber Dana</div>
                  <div class="value-lg">{{ $sdJenis }}</div>
                </div>
                <div class="col-6">
                  <div class="label-sm">Kategori</div>
                  <div class="value-lg">{{ $sdNama }}</div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- ====== PANE 2: DETAIL DANA ====== -->
      <div class="step-pane" id="pane-dana">
        <div class="row g-3 mt-1">
          <div class="col-12">
            <div class="card p-3">
              <h5 class="text-center">Detail Dana</h5>

              @php
                $total_awal_local = 0;
                $total_fix_local  = 0;
                $total_realisasi_local = 0;
              @endphp

              @if($canEditRab)
                <form id="formRevisiRab" method="POST" action="{{ route('project.rab.revise.save', $project->id) }}">
                  @csrf
              @endif

              <div class="table-responsive">
                <table class="table table-modern table-striped align-middle mt-3" width="100%">
                  <thead>
                    <tr>
                      <th class="text-center">Sub Kategori</th>
                      <th class="text-center">Rincian Anggaran Awal</th>
                      <th class="text-center">Anggaran (Revisi)</th>
                      <th class="text-center">Realisasi Anggaran</th>
                      <th class="text-center">Sisa Anggaran</th>
                    </tr>
                  </thead>

                  <tbody>
                    @forelse ($detail_dana as $dd)
                      @php
                        $awal = (int)($dd->nominal ?? 0);

                        // ✅ revisi tidak dipakai sebelum finalized
                        $fix = $awal;
                        if($isFinalized){
                          $fix = (int)($dd->anggaran_revisi ?? $awal);
                        }

                        $realisasi = (int)($dd->realisasi_anggaran ?? 0);
                        $sisa = $fix - $realisasi;

                        $rowClass = $sisa < 0 ? 'tr-over' : ($fix > 0 && $sisa <= ($fix * 0.10) ? 'tr-near' : '');

                        $total_awal_local += $awal;
                        $total_fix_local  += $fix;
                        $total_realisasi_local += $realisasi;
                      @endphp

                      <tr class="{{ $rowClass }}">
                        <td>{{ $dd->nama_subkategori }}</td>

                        <td class="text-end tnum" style="min-width:190px">
                          <span class="awal-pill">
                            <i class="bi bi-lock-fill"></i>
                            Rp {{ number_format($awal, 0, ',', '.') }}
                          </span>
                        </td>

                        @if($canEditRab)
                          <td class="text-end tnum" style="min-width:240px">
                            <input
                              type="text"
                              name="revisi[{{ $dd->id }}]"
                              value="{{ old('revisi.'.$dd->id, (int)($dd->anggaran_revisi ?? $awal)) }}"
                              class="form-control text-end rab-input"
                              data-revisi-input
                            >
                            <div class="form-text text-start">Isi angka. Boleh pakai titik/koma (nanti dibersihin).</div>
                          </td>
                        @else
                          <td class="text-end tnum" style="min-width:190px">
                            @if($isFinalized)
                              Rp {{ number_format((int)($dd->anggaran_revisi ?? $awal), 0, ',', '.') }}
                            @else
                              @if(!is_null($dd->anggaran_revisi))
                                Rp {{ number_format((int)($dd->anggaran_revisi), 0, ',', '.') }}
                                <span class="ms-1 badge bg-warning-subtle text-warning-emphasis" style="border-radius:999px">
                                  Menunggu finalisasi
                                </span>
                              @else
                                <span class="badge bg-warning-subtle text-warning-emphasis" style="border-radius:999px">
                                  Menunggu finalisasi
                                </span>
                              @endif
                            @endif
                          </td>
                        @endif

                        <td class="text-end tnum">Rp {{ number_format($realisasi, 0, ',', '.') }}</td>
                        <td class="text-end tnum">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="text-center subtle py-4">Belum ada data anggaran.</td>
                      </tr>
                    @endforelse

                    @php
                      $sisaTotal = $total_fix_local - $total_realisasi_local;
                    @endphp

                    <tr class="fw-bold">
                      <td>Total</td>
                      <td class="text-end tnum">Rp {{ number_format($total_awal_local, 0, ',', '.') }}</td>

                      @if($canEditRab)
                        <td class="text-end tnum" id="totalRevisiCell">Rp 0</td>
                      @else
                        <td class="text-end tnum">Rp {{ number_format($total_fix_local, 0, ',', '.') }}</td>
                      @endif

                      <td class="text-end tnum">Rp {{ number_format($total_realisasi_local, 0, ',', '.') }}</td>
                      <td class="text-end tnum">Rp {{ number_format($sisaTotal, 0, ',', '.') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              @if($canEditRab)
                </form>
              @endif

              @php
                $pct = $total_fix_local > 0 ? round($total_realisasi_local / $total_fix_local * 100) : 0;
                $pct = max(0, min(100, $pct));
                $ratio = $total_fix_local > 0 ? ($sisaTotal / $total_fix_local) : 0;

                $barClass = $pct >= 90 ? 'bg-warning' : 'bg-success';
                $badgeClass = $sisaTotal < 0 ? 'bg-danger-subtle text-danger'
                              : ($ratio <= 0.10 ? 'bg-warning-subtle text-warning-emphasis'
                                                : 'bg-success-subtle text-success');
              @endphp

              <div class="mt-2">
                <div class="progress" style="height:10px;border-radius:999px;overflow:hidden">
                  <div class="progress-bar {{ $barClass }}" style="width: {{ $pct }}%"></div>
                </div>
                <div class="d-flex justify-content-between small subtle mt-1">
                  <span>Realisasi {{ $pct }}% dari anggaran</span>
                  <span class="badge {{ $badgeClass }} p-2" style="border-radius:999px">
                    Sisa: Rp {{ number_format($sisaTotal, 0, ',', '.') }}
                  </span>
                </div>
              </div>

            </div>

            {{-- Sticky save bar (hanya saat bisa edit) --}}
            @if($canEditRab)
              <div class="savebar">
                <div class="savebar-inner">
                  <div class="savebar-meta">
                    <span><b>Total revisi:</b> <span class="tnum" id="savebarTotal">Rp 0</span></span>
                    <span class="mx-2">•</span>
                    <span><b>Sisa kuota:</b> <span class="tnum" id="savebarSisa">Rp 0</span></span>
                  </div>

                  <button type="submit" form="formRevisiRab" class="btn btn-success btn-save" data-save-rab>
                    <i class="bi bi-check2-circle me-1"></i> Simpan Revisi RAB
                  </button>
                </div>
              </div>

              <input type="hidden" id="rabLimit" value="{{ (int)$fundedTotal }}">
            @endif

          </div>
        </div>
      </div>

      <!-- ====== PANE 3: DETAIL PEMBELIAN ====== -->
      <div class="step-pane" id="pane-pembelian">
        <div class="row g-3 mt-1">
          <div class="col-12">
            <div class="card p-3">
              <h5 class="text-center">Detail Pembelian</h5>

              <div class="table-responsive">
                <table class="table table-modern table-striped align-middle mt-3" width="100%">
                  <thead>
                    <tr>
                      <th class="text-center">Nama Barang</th>
                      <th class="text-center">Kuantitas</th>
                      <th class="text-center">Harga</th>
                      <th class="text-center">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $total_request_local = 0; @endphp

                    @forelse ($detail_request as $dr)
                      <tr>
                        <td class="text-center">{{ $dr->nama_barang }}</td>
                        <td class="text-center tnum">{{ $dr->kuantitas }}</td>
                        <td class="text-end tnum">Rp {{ number_format($dr->harga, 0, ',', '.') }}</td>
                        <td class="text-end tnum">Rp {{ number_format($dr->total, 0, ',', '.') }}</td>
                      </tr>
                      @php $total_request_local += (int)($dr->total ?? 0); @endphp
                    @empty
                      <tr>
                        <td colspan="4" class="text-center subtle py-4">Belum ada request pembelian.</td>
                      </tr>
                    @endforelse

                    @if (count($detail_request) > 0)
                      @php $totalReqShow = $total_request_local; @endphp
                      <tr class="fw-bold">
                        <td colspan="3">Total Request Pembelian</td>
                        <td class="text-end tnum">Rp {{ number_format($totalReqShow, 0, ',', '.') }}</td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

{{-- scripts --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
  Swal.fire({ icon:'success', title:'Berhasil', text:@json(session('success')), confirmButtonText:'OK' });
</script>
@endif
@if(session('error'))
<script>
  Swal.fire({ icon:'error', title:'Gagal', text:@json(session('error')), confirmButtonText:'OK' });
</script>
@endif

<script>
  // Sidebar mobile toggle
  const sidebar   = document.getElementById('appSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const backdrop  = document.getElementById('backdrop');

  const openSidebar  = ()=>{ sidebar?.classList.add('open'); backdrop?.classList.add('show'); };
  const closeSidebar = ()=>{ sidebar?.classList.remove('open'); backdrop?.classList.remove('show'); };

  toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
  backdrop?.addEventListener('click', closeSidebar);

  // Tom Select (anggota)
  (function(){
    const el = document.getElementById('id_user');
    if(!el) return;

    const remoteUrl = el.getAttribute('data-url') || "";

    new TomSelect(el,{
      create:false,
      maxItems:1,
      persist:false,
      preload:'focus',
      valueField:'id',
      labelField:'name',
      searchField:['name'],
      sortField:{field:'name',direction:'asc'},
      maxOptions:50,
      render:{
        option:(d,e)=> `<div>${e(d.name||d.text||'')}</div>`,
        item:(d,e)=> `<div>${e(d.name||d.text||'')}</div>`
      },
      load:(q,cb)=>{
        if(!remoteUrl){ cb(); return; }
        fetch(remoteUrl + '?q=' + encodeURIComponent(q||''), {headers:{'Accept':'application/json'}})
          .then(r=> r.ok ? r.json() : [])
          .then(arr=> cb((arr||[]).map(u=>({id:u.id, name:u.name||u.text||''}))))
          .catch(()=> cb());
      }
    });
  })();

  // Stepper logic
  (function(){
    const defaultStep = @json($defaultStep ?? 'ringkasan');

    const btns = Array.from(document.querySelectorAll('#stepper .step-btn'));
    const panes = {
      ringkasan: document.getElementById('pane-ringkasan'),
      dana: document.getElementById('pane-dana'),
      pembelian: document.getElementById('pane-pembelian'),
    };

    function activate(step){
      btns.forEach(b => b.classList.toggle('active', b.dataset.step === step));
      Object.keys(panes).forEach(k => panes[k]?.classList.toggle('active', k === step));
      document.querySelector('.main-inner')?.scrollIntoView({behavior:'smooth', block:'start'});
    }

    btns.forEach(b => b.addEventListener('click', ()=> activate(b.dataset.step)));
    activate(defaultStep);
  })();

  // ===== RAB MODE: hitung total revisi + limit dana cair =====
  (function(){
    const IS_RAB = {{ $canEditRab ? 'true' : 'false' }};
    if(!IS_RAB) return;

    const LIMIT = Number(document.getElementById('rabLimit')?.value || 0);
    const fmt = (n)=> 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(n||0));

    const pill = document.getElementById('pillSisa');
    const sisaEl = document.getElementById('sisaKuota');
    const totalCell = document.getElementById('totalRevisiCell');
    const saveBtns = Array.from(document.querySelectorAll('[data-save-rab]'));

    const sbTotal = document.getElementById('savebarTotal');
    const sbSisa  = document.getElementById('savebarSisa');

    function onlyDigits(str){ return String(str||'').replace(/[^0-9]/g,''); }
    function formatInput(inp){
      const n = Number(onlyDigits(inp.value)||0);
      inp.value = n ? new Intl.NumberFormat('id-ID').format(n) : '';
    }

    function calc(){
      let total = 0;
      document.querySelectorAll('[data-revisi-input]').forEach(inp=>{
        total += Number(onlyDigits(inp.value)||0);
      });

      const sisa = LIMIT - total;

      if(totalCell) totalCell.textContent = fmt(total);
      if(sisaEl) sisaEl.textContent = fmt(sisa);
      if(sbTotal) sbTotal.textContent = fmt(total);
      if(sbSisa)  sbSisa.textContent  = fmt(sisa);

      pill?.classList.remove('warn','danger');
      if(LIMIT > 0 && sisa < 0) pill?.classList.add('danger');
      else if(LIMIT > 0 && sisa <= LIMIT * 0.10) pill?.classList.add('warn');

      const shouldDisable = (LIMIT > 0 && total > LIMIT);
      saveBtns.forEach(btn => btn.disabled = shouldDisable);
    }

    document.addEventListener('input', (e)=>{
      if(e.target.matches('[data-revisi-input]')) calc();
    });

    document.addEventListener('blur', (e)=>{
      if(e.target.matches('[data-revisi-input]')){
        formatInput(e.target);
        calc();
      }
    }, true);

    document.querySelectorAll('[data-revisi-input]').forEach(inp=> formatInput(inp));
    calc();
  })();

  // Tutup Project
  document.querySelectorAll('.js-close-project').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      const id = btn.dataset.id;
      const nama = btn.dataset.nama || 'project';

      const res = await Swal.fire({
        icon: 'warning',
        title: 'Tutup project?',
        html: `Project <b>${nama}</b> akan ditutup. Sistem menghitung sisa dana dan (jika ada) memasukkan ke kas.`,
        showCancelButton: true,
        confirmButtonText: 'Ya, tutup',
        cancelButtonText: 'Batal'
      });

      if(!res.isConfirmed) return;

      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      fetch(`{{ url('/project') }}/${id}/close`,{
        method:'POST',
        headers:{ 'X-CSRF-TOKEN': token, 'Accept':'application/json' }
      })
      .then(r=> r.json().then(j=>({ok:r.ok, j})))
      .then(({ok,j})=>{
        if(!ok || !j.success) throw new Error(j.message || 'Gagal menutup project');

        Swal.fire({
          icon:'success',
          title:'Berhasil',
          html:`Project ditutup.<br>Sisa: <b>${new Intl.NumberFormat('id-ID').format(j.data?.sisa||0)}</b>`
        }).then(()=> location.reload());
      })
      .catch(err=>{
        Swal.fire({icon:'error', title:'Gagal', text: err.message || 'Terjadi kesalahan'});
      });
    });
  });
</script>
@endsection
