{{-- resources/views/dashboard.blade.php (atau sesuai view kamu) --}}
@extends('layouts.panel')

@section('title','Dashboard')

@push('styles')
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
    --warn:#f59e0b;
    --info:#3b82f6;

    --shadow:0 10px 30px rgba(15,23,42,.08);
    --shadow2:0 18px 40px rgba(15,23,42,.10);
  }

  *{box-sizing:border-box}

  /* Hero */
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

  .hero-row{
    position:relative;
    z-index:2;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
  }

  .hero-left{ min-width:260px; flex:1 1 520px; }
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

  /* ✅ Manual Book (KANAN, sejajar hero) */
  .hero-actions{
    flex:0 0 auto;
    display:flex;
    align-items:flex-start;
    justify-content:flex-end;
  }
  .btn-manual{
    height:36px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 14px;
    border-radius:999px;
    font-weight:800;
    background:#fff;
    color:var(--ink);
    border:1px solid rgba(226,232,240,.95);
    text-decoration:none;
    box-shadow:0 12px 24px rgba(15,23,42,.06);
    white-space:nowrap;
    transition:.15s;
  }
  .btn-manual:hover{
    background:var(--brand-50);
    color:var(--brand-700);
    transform:translateY(-1px);
  }

  /* ✅ Filter (DIBAWAH paragraf sub, rata kiri) */
  .filter-block{
    margin-top:12px;
    display:flex;
    align-items:flex-end;
    gap:10px;
    flex-wrap:wrap;
  }
  .filter-label{
    font-size:.78rem;
    font-weight:700;
    color:rgba(15,23,42,.75);
    margin-bottom:4px;
  }
  .filter-input{
    min-width:190px;
    border-radius:14px !important;
    font-weight:600;
    border:1px solid rgba(226,232,240,.95);
  }
  .btn-apply{
    height:36px;
    border-radius:999px;
    font-weight:800;
    padding:0 14px;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    border:0;
    box-shadow:0 16px 28px rgba(22,163,74,.18);
  }
  .btn-apply:hover{ filter:brightness(.98); transform:translateY(-1px); }
  .range-pill{
    height:36px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 12px;
    border-radius:999px;
    background:var(--brand-50);
    color:var(--brand-700);
    border:1px solid rgba(2,6,23,.06);
    font-weight:800;
    white-space:nowrap;
  }

  /* Chips (sebaris, manual book tetap di kanan) */
  .hero-bottom{
    margin-top:12px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    position:relative;
    z-index:2;
    flex-wrap:wrap;
  }
  .chips{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin:0;
  }
  .chip{
    display:inline-flex; align-items:center; gap:6px;
    padding:.34rem .72rem;
    border-radius:999px;
    font-weight:700;
    font-size:.82rem;
    border:1px solid rgba(2,6,23,.06);
    white-space:nowrap;
  }
  .chip.green{ background:var(--brand-50); color:var(--brand-700); }
  .chip.blue{ background:rgba(59,130,246,.12); color:#1d4ed8; }
  .chip.orange{ background:rgba(245,158,11,.14); color:#b45309; }
  .chip.red{ background:rgba(239,68,68,.12); color:#b91c1c; }

  /* KPI */
  .kpi{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:20px;
    padding:16px;
    box-shadow:0 10px 26px rgba(15,23,42,.06);
    position:relative;
    overflow:hidden;
    height:100%;
    transition:.18s;
  }
  .kpi:hover{ transform:translateY(-2px); box-shadow:var(--shadow2); }
  .kpi::before{
    content:"";
    position:absolute; inset:0;
    background:radial-gradient(720px 150px at 0% 0%, rgba(22,163,74,.14), transparent 60%);
    pointer-events:none;
  }
  .kpi .top{ display:flex; justify-content:space-between; gap:12px; position:relative; z-index:2; }
  .kpi .label{ color:var(--ink-600); font-weight:600; font-size:.9rem; }
  .kpi .value{ font-weight:800; font-size:1.65rem; margin-top:6px; letter-spacing:-.2px; }
  .kpi .hint{ color:var(--ink-600); font-weight:500; font-size:.86rem; margin-top:6px; }
  .kpi-ico{
    width:44px;height:44px;border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    background:var(--brand-50);
    color:var(--brand-700);
    border:1px solid rgba(2,6,23,.06);
    flex:0 0 auto;
  }

  /* notif badge in card */
  .notif-badge{
    position:absolute; top:12px; right:12px;
    min-width:26px; height:26px; padding:0 8px;
    border-radius:999px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.8rem; font-weight:800;
    background:var(--danger); color:#fff;
    box-shadow:0 14px 22px rgba(239,68,68,.28);
    z-index:3;
  }
  .ring{
    position:absolute; inset:-8px; border-radius:24px;
    border:2px solid rgba(239,68,68,.22);
    pointer-events:none; z-index:2;
  }

  .mini-badges{ display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; position:relative; z-index:2; }
  .mini{
    display:inline-flex; align-items:center; gap:6px;
    padding:.25rem .6rem;
    border-radius:999px;
    font-weight:700;
    font-size:.78rem;
    border:1px solid rgba(2,6,23,.06);
    white-space:nowrap;
  }
  .mini.green{ background:rgba(22,163,74,.10); color:var(--brand-700); }
  .mini.red{ background:rgba(239,68,68,.10); color:#b91c1c; }
  .mini.blue{ background:rgba(59,130,246,.10); color:#1d4ed8; }
  .mini.orange{ background:rgba(245,158,11,.12); color:#b45309; }

  /* List cards */
  .list-card{
    margin-top:14px;
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    box-shadow:var(--shadow);
    overflow:hidden;
  }
  .list-head{
    padding:14px 16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    background:
      radial-gradient(700px 140px at 0% 0%, rgba(22,163,74,.10), transparent 60%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.78));
    border-bottom:1px solid rgba(226,232,240,.95);
  }
  .list-title{
    margin:0;
    font-weight:800;
    font-size:1.02rem;
    display:flex; align-items:center; gap:8px;
  }
  .list-body{ padding:6px 16px 14px; }
  .row-item{
    padding:12px 0;
    border-bottom:1px dashed rgba(226,232,240,.95);
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
  }
  .row-item:last-child{ border-bottom:0; }
  .row-main{ min-width:0; }
  .row-main .name{
    font-weight:700;
    margin:0;
    font-size:.96rem;
    line-height:1.2;
  }
  .row-main .meta{
    margin:4px 0 0;
    font-weight:500;
    color:var(--ink-600);
    font-size:.84rem;
  }
  .tag{
    flex:0 0 auto;
    padding:.25rem .65rem;
    border-radius:999px;
    font-weight:700;
    font-size:.78rem;
    border:1px solid rgba(2,6,23,.06);
    white-space:nowrap;
  }
  .tag.green{ background:rgba(22,163,74,.10); color:var(--brand-700); }
  .tag.orange{ background:rgba(245,158,11,.12); color:#b45309; }
  .tag.blue{ background:rgba(59,130,246,.10); color:#1d4ed8; }
  .tag.red{ background:rgba(239,68,68,.10); color:#b91c1c; }

  /* Chart */
  .chart-card{
    margin-top:14px;
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    padding:16px;
    box-shadow:var(--shadow);
    height:420px;
    display:flex;
    flex-direction:column;
    overflow:hidden;
  }
  .chart-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:8px;
  }
  .chart-title{ margin:0; font-weight:800; }
  .chart-body{ flex:1; min-height:0; }
  .chart-body canvas{ width:100% !important; height:100% !important; }

  /* Mobile hero */
  @media(max-width:991.98px){
    .filter-input{ min-width:160px; }
    .hero-bottom{ justify-content:flex-start; }
  }
</style>
@endpush

@section('content')

  <!-- HERO -->
  <section class="hero">

    <!-- Top row: kiri (title+sub+filter), kanan (manual book) -->
    <div class="hero-row">
      <div class="hero-left">
        <h1 class="title">
          {{ Auth::user()->role === 'admin' ? 'Control Dashboard' : 'Dashboard Peneliti' }}
        </h1>

        <p class="sub">
          @if(Auth::user()->role === 'admin')
            Pantau ringkasan project, request pembelian, kas, dan pencatatan keuangan berdasarkan rentang tanggal.
          @else
            Lihat ringkasan project yang dapat Anda akses dan aktivitas request pembelian milik Anda.
          @endif
        </p>

        @if(Auth::user()->role === 'admin')
          <!-- ✅ FILTER PINDAH KE SINI (DIBAWAH PARAGRAF) -->
          <form method="GET" action="{{ route('dashboard') }}" class="filter-block">
            <div>
              <div class="filter-label">Dari</div>
              <input type="date" name="start_date"
                     class="form-control form-control-sm filter-input"
                     value="{{ $startDate ?? '' }}">
            </div>

            <div>
              <div class="filter-label">Sampai</div>
              <input type="date" name="end_date"
                     class="form-control form-control-sm filter-input"
                     value="{{ $endDate ?? '' }}">
            </div>

            <button type="submit" class="btn btn-sm btn-apply text-white">
              <i class="bi bi-funnel"></i> Terapkan
            </button>

            <span class="range-pill">
              <i class="bi bi-calendar3"></i>
              {{ $startDate ?? '-' }} — {{ $endDate ?? '-' }}
            </span>
          </form>
        @endif
      </div>

      <div class="hero-actions">
        <a class="btn-manual"
           href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
           target="_blank" rel="noopener">
          <i class="bi bi-book"></i> Manual Book
        </a>
      </div>
    </div>

    <!-- Chips ringkas -->
    <div class="hero-bottom">
      <div class="chips">
        <span class="chip green"><i class="bi bi-kanban"></i> {{ $totalProjects ?? 0 }} Project</span>
        <span class="chip green"><i class="bi bi-lightning-charge"></i> {{ $activeProjects ?? ($aktifProjects ?? 0) }} Aktif</span>
        <span class="chip orange"><i class="bi bi-lock"></i> {{ $closedProjects ?? ($ditutupProjects ?? 0) }} Ditutup</span>

        @if(Auth::user()->role === 'admin')
          <span class="chip blue"><i class="bi bi-bag-check"></i> {{ $totalRequestsPeriod ?? 0 }} Request (Periode)</span>
          <span class="chip red"><i class="bi bi-bell"></i> {{ $newRequests ?? 0 }} Request Baru</span>
        @else
          <span class="chip blue"><i class="bi bi-bag-check"></i> {{ $myRequestsPeriod ?? 0 }} Request Anda (Periode)</span>
        @endif
      </div>
      <!-- kanan kosong biar space rapi (manual book udah di atas) -->
      <div style="min-width:1px;"></div>
    </div>

  </section>

  <!-- KPI ROW -->
  <section class="row g-3 mt-2">

    <!-- Project -->
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('project.index') }}" class="text-decoration-none text-reset">
        <div class="kpi">
          <div class="top">
            <div>
              <div class="label">Project</div>
              <div class="value">{{ $totalProjects ?? 0 }}</div>
              <div class="hint">{{ $activeProjects ?? 0 }} aktif • {{ $closedProjects ?? 0 }} ditutup</div>

              <div class="mini-badges">
                <span class="mini green"><i class="bi bi-check2-circle"></i> Aktif</span>
                <span class="mini orange"><i class="bi bi-lock"></i> Ditutup</span>
              </div>
            </div>
            <div class="kpi-ico"><i class="bi bi-kanban fs-5"></i></div>
          </div>
        </div>
      </a>
    </div>

    <!-- Request Pembelian -->
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('requestpembelian.index') }}" class="text-decoration-none text-reset">
        <div class="kpi">
          @if(Auth::user()->role === 'admin' && ($newRequests ?? 0) > 0)
            <span class="notif-badge">{{ $newRequests }}</span>
            <span class="ring"></span>
          @endif

          <div class="top">
            <div>
              <div class="label">
                {{ Auth::user()->role === 'admin' ? 'Request Pembelian (Periode)' : 'Request Pembelian Anda (Periode)' }}
              </div>

              <div class="value">
                {{ Auth::user()->role === 'admin' ? ($totalRequestsPeriod ?? 0) : ($myRequestsPeriod ?? 0) }}
              </div>

              <div class="hint">
                @if(Auth::user()->role === 'admin')
                  submit: {{ $submitRequestsPeriod ?? 0 }} • approve: {{ $approvedRequestsPeriod ?? 0 }} • reject: {{ $rejectedRequestsPeriod ?? 0 }} • done: {{ $doneRequestsPeriod ?? 0 }}
                @else
                  submit: {{ $mySubmitRequestsPeriod ?? 0 }} • approve: {{ $myApprovedRequestsPeriod ?? 0 }} • reject: {{ $myRejectedRequestsPeriod ?? 0 }} • done: {{ $myDoneRequestsPeriod ?? 0 }}
                @endif
              </div>

              <div class="mini-badges">
                <span class="mini red"><i class="bi bi-send"></i> submit</span>
                <span class="mini green"><i class="bi bi-check2"></i> approve</span>
                <span class="mini orange"><i class="bi bi-x-circle"></i> reject</span>
                <span class="mini blue"><i class="bi bi-check2-circle"></i> done</span>
              </div>

              @if(Auth::user()->role === 'admin' && ($newRequests ?? 0) > 0)
                <div class="mt-2" style="font-weight:700;color:#b91c1c;font-size:.88rem;">
                  <i class="bi bi-bell-fill"></i> {{ $newRequests }} request baru (submit_request)
                </div>
              @endif
            </div>
            <div class="kpi-ico"><i class="bi bi-bag-check fs-5"></i></div>
          </div>
        </div>
      </a>
    </div>

    <!-- Admin: Pencatatan Keuangan (Periode) | Peneliti: ringkas -->
    <div class="col-12 col-md-6 col-lg-4">
      @if(Auth::user()->role === 'admin')
        <a href="{{ route('pencatatan_keuangan') }}" class="text-decoration-none text-reset">
          <div class="kpi">
            <div class="top">
              <div>
                <div class="label">Pencatatan Keuangan (Periode)</div>

                @php
                  $masuk = $pencatatanMasukPeriod ?? 0;
                  $keluar = $pencatatanKeluarPeriod ?? 0;
                  $net = $masuk - $keluar;
                @endphp

                <div class="value">Rp {{ number_format($keluar, 0, ',', '.') }}</div>
                <div class="hint">Total pengeluaran pada rentang tanggal</div>

                <div class="mini-badges">
                  <span class="mini green"><i class="bi bi-arrow-down-circle"></i> Masuk: Rp {{ number_format($masuk, 0, ',', '.') }}</span>
                  <span class="mini red"><i class="bi bi-arrow-up-circle"></i> Keluar: Rp {{ number_format($keluar, 0, ',', '.') }}</span>
                  <span class="mini blue"><i class="bi bi-calculator"></i> Selisih: Rp {{ number_format($net, 0, ',', '.') }}</span>
                </div>

                <div class="mt-2" style="font-weight:600;color:var(--ink-600);font-size:.86rem;">
                  {{ $pencatatanCountPeriod ?? 0 }} transaksi pada periode
                </div>
              </div>
              <div class="kpi-ico"><i class="bi bi-journal-text fs-5"></i></div>
            </div>
          </div>
        </a>
      @else
        <div class="kpi">
          <div class="top">
            <div>
              <div class="label">Ringkasan Anda</div>
              <div class="value">{{ $myProjectsCount ?? ($totalProjects ?? 0) }}</div>
              <div class="hint">Jumlah project yang Anda akses / miliki</div>

              <div class="mini-badges">
                <span class="mini green"><i class="bi bi-kanban"></i> Project</span>
                <span class="mini blue"><i class="bi bi-bag-check"></i> Request</span>
              </div>
            </div>
            <div class="kpi-ico"><i class="bi bi-person-workspace fs-5"></i></div>
          </div>
        </div>
      @endif
    </div>

    <!-- Admin extras -->
    @if(Auth::user()->role === 'admin')
      <div class="col-12 col-md-6 col-lg-8">
        <a href="{{ route('kas.index') }}" class="text-decoration-none text-reset">
          <div class="kpi">
            <div class="top">
              <div>
                <div class="label">Kas (Periode)</div>

                @php
                  $kasMasuk = $kasMasukPeriod ?? 0;
                  $kasKeluar = $kasKeluarPeriod ?? 0;
                  $kasSaldo = $kasMasuk - $kasKeluar;
                @endphp

                <div class="value">Rp {{ number_format($kasSaldo, 0, ',', '.') }}</div>
                <div class="hint">Saldo kas pada rentang tanggal</div>

                <div class="mini-badges">
                  <span class="mini green"><i class="bi bi-arrow-down-circle"></i> Masuk: Rp {{ number_format($kasMasuk, 0, ',', '.') }}</span>
                  <span class="mini red"><i class="bi bi-arrow-up-circle"></i> Keluar: Rp {{ number_format($kasKeluar, 0, ',', '.') }}</span>
                </div>

                <div class="mt-2" style="font-weight:600;color:var(--ink-600);font-size:.86rem;">
                  {{ $kasCountPeriod ?? 0 }} transaksi kas pada periode
                </div>
              </div>
              <div class="kpi-ico"><i class="bi bi-wallet2 fs-5"></i></div>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="{{ route('users.index') }}" class="text-decoration-none text-reset">
          <div class="kpi">
            <div class="top">
              <div>
                <div class="label">Jumlah Peneliti (Tim)</div>
                <div class="value">{{ $totalTeams ?? 0 }}</div>
                <div class="hint">Total user dengan role peneliti</div>
              </div>
              <div class="kpi-ico"><i class="bi bi-people fs-5"></i></div>
            </div>
          </div>
        </a>
      </div>
    @endif
  </section>

  <!-- LISTS -->
  <section class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="list-card">
        <div class="list-head">
          <h3 class="list-title"><i class="bi bi-kanban"></i> Project Terbaru</h3>
          <span class="chip green"><i class="bi bi-stars"></i> Top 3</span>
        </div>
        <div class="list-body">
          @php $items = $latestProjects ?? collect(); @endphp

          @forelse($items->take(3) as $p)
            <div class="row-item">
              <div class="row-main">
                <p class="name">{{ $p->nama_project ?? '-' }}</p>
                <p class="meta">
                  Tahun {{ $p->tahun ?? '-' }} • Durasi {{ $p->durasi ?? '-' }}
                </p>
              </div>

              @php
                $st = strtolower($p->status ?? '');
                $isAktif = ($st === 'aktif' || $st === 'active');
              @endphp

              <span class="tag {{ $isAktif ? 'green' : 'orange' }}">
                <i class="bi {{ $isAktif ? 'bi-lightning-charge' : 'bi-lock' }}"></i>
                {{ $isAktif ? 'aktif' : 'ditutup' }}
              </span>
            </div>
          @empty
            <div class="py-3" style="color:var(--ink-600);font-weight:600;">
              Belum ada data project.
            </div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="list-card">
        <div class="list-head">
          <h3 class="list-title"><i class="bi bi-bag-check"></i> Request Terbaru</h3>
          <span class="chip green"><i class="bi bi-stars"></i> Top 3</span>
        </div>
        <div class="list-body">
          @php $reqs = $latestRequests ?? collect(); @endphp

          @forelse($reqs->take(3) as $r)
            @php
              $status = strtolower($r->status_request ?? '');
              $tagClass = 'blue';
              $label = $status ?: 'status';
              if(str_contains($status,'submit')) { $tagClass='red'; $label='submit'; }
              elseif(str_contains($status,'approve')) { $tagClass='green'; $label='approve'; }
              elseif(str_contains($status,'reject')) { $tagClass='orange'; $label='reject'; }
              elseif(str_contains($status,'done')) { $tagClass='blue'; $label='done'; }
            @endphp

            <div class="row-item">
              <div class="row-main">
                <p class="name">No. {{ $r->no_request ?? '-' }}</p>
                <p class="meta">Tgl: {{ $r->tgl_request ?? '-' }}</p>
              </div>
              <span class="tag {{ $tagClass }}">
                <i class="bi bi-dot"></i> {{ $label }}
              </span>
            </div>
          @empty
            <div class="py-3" style="color:var(--ink-600);font-weight:600;">
              Belum ada data request.
            </div>
          @endforelse

          <div class="pt-2">
            <a href="{{ route('requestpembelian.index') }}" class="text-decoration-none" style="font-weight:700;color:var(--brand-700);">
              Lihat semua request <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CHART -->
  @if(Auth::user()->role === 'admin')
    <section class="chart-card">
      <div class="chart-head">
        <h5 class="chart-title">Grafik Pengeluaran per Proyek (Periode)</h5>
        <span class="chip green"><i class="bi bi-bar-chart"></i> Monitoring</span>
      </div>
      <div class="chart-body">
        <canvas id="chartProject"></canvas>
      </div>
    </section>
  @endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Chart (admin only)
  @if(Auth::user()->role === 'admin')
    const el = document.getElementById('chartProject');
    if(el){
      const ctx = el.getContext('2d');
      const labels = {!! json_encode($namaProjects ?? []) !!};
      const dataVal = {!! json_encode($pengeluaranPerProject ?? []) !!};

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Pengeluaran (Rp)',
            data: dataVal,
            backgroundColor: 'rgba(22,163,74,0.22)',
            borderColor: 'rgba(22,163,74,1)',
            borderWidth: 2,
            borderRadius: 10
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (c) => ' Rp ' + new Intl.NumberFormat('id-ID').format(c.parsed.y || 0)
              }
            }
          },
          scales: {
            x: { ticks:{ color:'#475569' }, grid:{ color:'rgba(2,6,23,.06)' } },
            y: { beginAtZero:true, ticks:{ color:'#475569' }, grid:{ color:'rgba(2,6,23,.06)' } }
          }
        }
      });
    }
  @endif
</script>
@endpush
