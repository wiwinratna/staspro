<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detail Project</title>

  <!-- Fonts, Bootstrap, Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Tom Select (autocomplete) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#fff;

      /* layout */
      --content-max: 1180px;                 /* ubah sesuai selera: 1120/1200/1280 */
      --page-pad: clamp(14px,2.2vw,22px);    /* padding area konten */
      --rail-gap: clamp(14px,1.8vw,24px);    /* JARAK antara sidebar & konten */
    }
    *{box-sizing:border-box}
    html,body{ height:100%; }
    body{ background:var(--bg); font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color:var(--ink); }

    /* Topbar */
    .topbar{ background:linear-gradient(135deg,var(--brand-700),var(--brand)); color:#fff; }
    .brand-title{ font-weight:700; letter-spacing:.2px; }

    /* Shell */
    .app{ display:flex; min-height:calc(100vh - 56px); gap: var(--rail-gap); }

    /* Main area: left-anchored + nyaman (nggak mepet) */
    .main-wrap{ width:100%; padding: var(--page-pad); }
    .main-inner{ width:100%; max-width: var(--content-max); margin-right:auto; }

    /* Sidebar */
    .sidebar{
      width:260px; background:var(--card); border-right:1px solid var(--line);
      padding:18px; position:sticky; top:0;             /* <- nempel ke atas, no gap */
    }
    .menu-title{ font-size:.8rem; letter-spacing:.06em; color:var(--ink-600); text-transform:uppercase; margin:6px 0 8px; font-weight:600; }
    .menu-sep{ height:1px; background:var(--line); margin:12px 0; border-radius:999px; }
    .nav-link-custom{
      display:flex; align-items:center; gap:10px; padding:10px 12px; color:var(--ink);
      border-radius:10px; text-decoration:none; transition:all .18s; font-weight:500;
      white-space:nowrap; overflow:hidden; text-overflow:ellipsis; width:100%;
    }
    .nav-link-custom:hover{ background:var(--brand-50); color:var(--brand-700); }
    .nav-link-custom.active{ background:var(--brand); color:#fff; box-shadow:0 6px 16px rgba(22,163,74,.18); }
    .nav-link-custom i{ font-size:1rem; line-height:1; }

    /* Cards & text */
    .card{ background:var(--card); color:var(--ink); border:1px solid var(--line); border-radius:12px; }
    .card h5{ font-weight:700; }
    .page-title{ font-weight:700; font-size:1.8rem; }
    .subtle{ color:var(--ink-600); }
    .label-sm{ font-size:.8rem; letter-spacing:.04em; color:var(--ink-600); text-transform:uppercase; }
    .value-lg{ font-weight:700; }
    .empty{ color:var(--ink-600); padding:24px; text-align:center; }

    /* Status badge */
    .badge-proj{ display:inline-flex; align-items:center; gap:6px; padding:.35rem .55rem; border-radius:8px; font-weight:700; border:1px solid transparent; }
    .badge-proj-planned{ background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
    .badge-proj-on-going{ background:#ecfdf5; color:#166534; border-color:#bbf7d0; }
    .badge-proj-paused{ background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
    .badge-proj-cancelled{ background:#fef2f2; color:#b91c1c; border-color:#fecaca; }
    .badge-proj-completed{ background:#eefdfb; color:#0f766e; border-color:#99f6e4; }

    /* Buttons */
    .btn-action{ border-radius:8px; font-weight:600; }
    .btn-action-ghost{ background:#fff; border-color:#94a3b8; color:#334155; }
    .btn-action-ghost:hover{ background:#f8fafc; border-color:#64748b; color:#0f172a; }
    .btn:focus-visible{ outline:3px solid #93c5fd; outline-offset:2px; }

    /* Tables */
    .table-modern{ margin:0; vertical-align:middle; border:1px solid var(--line); border-radius:10px; overflow:hidden; }
    .table-modern thead th{ background:#f9fafb; color:var(--ink-600); font-weight:700; border-bottom:1px solid var(--line); position:sticky; top:0; z-index:1; }
    .table-modern td,.table-modern th{ padding:.8rem .8rem; }
    .tnum{ font-variant-numeric: tabular-nums; }

    /* Highlight */
    .tr-over{ background:#fef2f2; }
    .tr-near{ background:#fff7ed; }

    /* Anggota: chip style */
    .chips-scroll{ max-height:160px; overflow:auto; padding-right:6px; }
    .chips-wrap{ display:flex; flex-wrap:wrap; gap:6px; }
    .chip{ background:#f1f5f9; border:1px solid #e2e8f0; color:#0f172a; border-radius:999px; padding:6px 10px; font-weight:600; }

    /* Samakan tinggi input & tombol */
    .ts-wrapper{ width:100%; }
    .ts-wrapper .ts-control{ min-height:40px; }
    .btn-eq{ height:40px; display:inline-flex; align-items:center; justify-content:center; }

    /* Mobile sidebar */
    .backdrop{ display:none; position:fixed; inset:0; background:rgba(15,23,42,.38); z-index:1035; }
    @media (max-width:991.98px){
      .sidebar{ position:fixed; left:-280px; top:0; height:100vh; z-index:1040; transition:left .2s; }
      .sidebar.open{ left:0; }
      .backdrop.show{ display:block; }
      .app{ gap: 0; } /* di mobile biasanya full overlay sidebar */
    }

    /* Print */
    @media print{
      .topbar,.sidebar{ display:none !important; }
      .main-wrap{ width:100%; padding:0; }
      .card{ border:1px solid #ccc; }
    }
  </style>
</head>
<body>
  <!-- Topbar -->
  <nav class="navbar topbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" aria-label="Buka tutup sidebar">
        <i class="bi bi-list"></i>
      </button>
      <div class="brand-title">STAS-RG</div>
      <div class="ms-auto">@include('navbar')</div>
    </div>
  </nav>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      <div class="menu-title">Menu</div>
      <a class="nav-link-custom" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a class="nav-link-custom active" href="{{ route('project.index') }}"><i class="bi bi-kanban"></i> Project</a>
      <a class="nav-link-custom" href="{{ route('requestpembelian.index') }}"><i class="bi bi-bag-check"></i> Request Pembelian</a>

      @if (Auth::user()->role == 'admin')
        <div class="menu-sep"></div>
        <div class="menu-title">Administrasi</div>
        <a class="nav-link-custom" href="{{ route('sumberdana.index') }}"><i class="bi bi-cash-coin"></i> Sumber Dana</a>
        <a class="nav-link-custom" href="{{ route('pencatatan_keuangan') }}"><i class="bi bi-journal-text"></i> Pencatatan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('laporan_keuangan') }}"><i class="bi bi-graph-up"></i> Laporan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Management User</a>
      @endif
    </aside>
    <div class="backdrop" id="backdrop"></div>

    <!-- Main -->
    <main class="flex-grow-1" style="min-width:0;">
      <div class="main-wrap">
        <div class="main-inner">

          @if ($message = Session::get('success')) <div class="alert alert-success">{{ $message }}</div> @endif
          @if ($message = Session::get('error'))   <div class="alert alert-danger">{{ $message }}</div>   @endif

          <!-- Breadcrumb -->
          <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
              <li class="breadcrumb-item active" aria-current="page">Detail</li>
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
            ];
            $projBadge = $statusMap[$statusRaw]['cls'] ?? 'badge-proj-on-going';
            $projLabel = $statusMap[$statusRaw]['label'] ?? ucwords(str_replace('_',' ', $statusRaw));
          @endphp

          <div class="d-flex flex-wrap align-items-baseline justify-content-between mb-1">
            <div class="mb-2">
              <div class="page-title mb-1">{{ $project->nama_project }}</div>
              <div class="subtle">Ringkasan proyek & anggaran • Diupdate {{ \Carbon\Carbon::parse($project->updated_at ?? now())->diffForHumans() }}</div>
            </div>
            <span class="badge-proj {{ $projBadge }}">{{ $projLabel }}</span>
          </div>

          <!-- Row 1 -->
          <div class="row g-3 mt-1">
            <div class="col-lg-8">
              <div class="card p-3 h-100">
                <h5 class="mb-2 text-center">Deskripsi Project</h5>
                @php
                  $plain = trim(preg_replace('/\s+/u',' ', strip_tags($project->deskripsi ?? '')));
                  $wordLimit = 60; /* batas 60 kata */
                  $words = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY);
                  $snippet = implode(' ', array_slice($words, 0, $wordLimit));
                  $isTrimmed = count($words) > $wordLimit;
                @endphp
                <p class="mb-2">{{ $snippet }}@if($isTrimmed)…@endif</p>

                <h5 class="mt-3 mb-2 text-center">Anggota Tim Riset</h5>
                @php $anggotaCount = count($anggota ?? []); @endphp
                @if($anggotaCount===0)
                  <div class="empty">Belum ada anggota tim.</div>
                @else
                  <div class="chips-scroll">
                    <div class="chips-wrap">
                      @foreach($anggota as $a)
                        <span class="chip" title="{{ $a->name }}">{{ $a->name }}</span>
                      @endforeach
                    </div>
                  </div>
                  <div class="small subtle mt-1">Total: {{ $anggotaCount }} orang</div>
                @endif

                @if (Auth::user()->role == 'admin')
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
              <div class="card p-3">
                <h5 class="mb-3">Aksi</h5>
                <div class="d-grid gap-2">
                  <a class="btn btn-action btn-action-ghost" href="{{ route('project.downloadproposal', $project->id) }}">⬇️ Download Proposal</a>
                  <a class="btn btn-action btn-action-ghost" href="{{ route('project.downloadrab', $project->id) }}">⬇️ Download RAB</a>
                </div>
              </div>

              @php
                $sdObj  = $project->sumberDana ?? ($sumber_dana ?? null);
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
                    <div class="label-sm">Kategori Pendanaan</div>
                    <div class="value-lg">{{ $sdNama }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Detail Dana -->
          <div class="row g-3 mt-1">
            <div class="col-12">
              <div class="card p-3">
                <h5 class="text-center">Detail Dana</h5>

                <div class="table-responsive">
                  <table class="table table-bordered table-modern mt-3" width="100%">
                    <thead>
                      <tr>
                        <th class="text-center">Sub Kategori</th>
                        <th class="text-center">Rincian Anggaran</th>
                        <th class="text-center">Realisasi Anggaran</th>
                        <th class="text-center">Sisa Anggaran</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php $total_nominal = 0; $total_realisasi = 0; @endphp
                      @forelse ($detail_dana as $dd)
                        @php
                          $sisa = $dd->nominal - $dd->realisasi_anggaran;
                          $rowClass = $sisa < 0 ? 'tr-over' : ($dd->nominal > 0 && $sisa <= ($dd->nominal * 0.10) ? 'tr-near' : '');
                          $total_nominal += $dd->nominal; $total_realisasi += $dd->realisasi_anggaran;
                        @endphp
                        <tr class="{{ $rowClass }}">
                          <td>{{ $dd->nama_subkategori }}</td>
                          <td class="text-end tnum">Rp {{ number_format($dd->nominal, 0, ',', '.') }}</td>
                          <td class="text-end tnum">Rp {{ number_format($dd->realisasi_anggaran, 0, ',', '.') }}</td>
                          <td class="text-end tnum">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                        </tr>
                      @empty
                        <tr><td colspan="4" class="empty">Belum ada data anggaran.</td></tr>
                      @endforelse
                      <tr class="fw-bold">
                        <td>Total</td>
                        <td class="text-end tnum">Rp {{ number_format($total_nominal, 0, ',', '.') }}</td>
                        <td class="text-end tnum">Rp {{ number_format($total_realisasi, 0, ',', '.') }}</td>
                        <td class="text-end tnum">Rp {{ number_format($total_nominal - $total_realisasi, 0, ',', '.') }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                @php
                  $sisaTotal = $total_nominal - $total_realisasi;
                  $ratio = $total_nominal > 0 ? ($sisaTotal / $total_nominal) : 0;
                  $pct = $total_nominal > 0 ? round($total_realisasi / $total_nominal * 100) : 0;
                  $pct = max(0, min(100, $pct));
                  $barClass = $pct > 100 ? 'bg-danger' : ($pct >= 90 ? 'bg-warning' : 'bg-success');
                  $badgeClass = $sisaTotal < 0 ? 'bg-danger-subtle text-danger'
                                : ($ratio <= 0.10 ? 'bg-warning-subtle text-warning-emphasis'
                                                  : 'bg-success-subtle text-success');
                @endphp

                <div class="mt-2">
                  <div class="progress" style="height:10px">
                    <div class="progress-bar {{ $barClass }}" style="width: {{ $pct }}%"></div>
                  </div>
                  <div class="d-flex justify-content-between small subtle mt-1">
                    <span>Realisasi {{ $pct }}% dari anggaran</span>
                    <span class="badge {{ $badgeClass }} p-2">Sisa: Rp {{ number_format($sisaTotal, 0, ',', '.') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Detail Pembelian -->
          <div class="row g-3 mt-1">
            <div class="col-12">
              <div class="card p-3">
                <h5 class="text-center">Detail Pembelian</h5>

                <div class="table-responsive">
                  <table class="table table-bordered table-modern mt-3" width="100%">
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
                      @forelse ($detail_request as $dd)
                        <tr>
                          <td class="text-center">{{ $dd->nama_barang }}</td>
                          <td class="text-center tnum">{{ $dd->kuantitas }}</td>
                          <td class="text-end tnum">Rp {{ number_format($dd->harga, 0, ',', '.') }}</td>
                          <td class="text-end tnum">Rp {{ number_format($dd->total, 0, ',', '.') }}</td>
                        </tr>
                        @php $total_request_local += $dd->total; @endphp
                      @empty
                        <tr><td colspan="4" class="empty">Belum ada request pembelian.</td></tr>
                      @endforelse
                      @if (count($detail_request) > 0)
                        <tr class="fw-bold">
                          <td colspan="3">Total Request Pembelian</td>
                          <td class="text-end tnum">
                            @php
                              $totalReqShow = isset($total_request_pembelian) ? $total_request_pembelian : $total_request_local;
                            @endphp
                            Rp {{ number_format($totalReqShow, 0, ',', '.') }}
                          </td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div><!-- /main-inner -->
      </div><!-- /main-wrap -->
    </main>
  </div><!-- /app -->

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
  <script>
    // Sidebar mobile toggle
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');
    const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); };
    const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); };
    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);

    // Tom Select: search anggota (remote jika route tersedia; jika tidak pakai opsi lokal)
    (function(){
      const el = document.getElementById('id_user');
      if(!el) return;
      const remoteUrl = el.getAttribute('data-url') || "";
      new TomSelect(el,{
        create:false, maxItems:1, persist:false, preload:'focus',
        valueField:'id', labelField:'name', searchField:['name'],
        sortField:{field:'name',direction:'asc'}, maxOptions:50,
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
  </script>
</body>
</html>
