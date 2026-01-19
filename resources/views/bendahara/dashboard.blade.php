@extends('layouts.panel')

@section('title','Dashboard Bendahara')

@push('styles')
<style>
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
    position:relative; z-index:2;
    display:flex; align-items:flex-start; justify-content:space-between;
    gap:14px; flex-wrap:wrap;
  }
  .hero-left{ min-width:260px; flex:1 1 520px; }
  .hero-left .title{ font-size:1.65rem; font-weight:800; margin:0; letter-spacing:-.2px; }
  .hero-left .sub{ margin:6px 0 0; color:var(--ink-600); font-weight:500; }

  .hero-actions{ flex:0 0 auto; display:flex; justify-content:flex-end; }
  .btn-manual{
    height:36px; display:inline-flex; align-items:center; gap:8px;
    padding:0 14px; border-radius:999px;
    font-weight:800; background:#fff; color:var(--ink);
    border:1px solid rgba(226,232,240,.95);
    text-decoration:none;
    box-shadow:0 12px 24px rgba(15,23,42,.06);
    white-space:nowrap;
    transition:.15s;
  }
  .btn-manual:hover{ background:var(--brand-50); color:var(--brand-700); transform:translateY(-1px); }

  /* Filter */
  .filter-block{ margin-top:12px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
  .filter-label{ font-size:.78rem; font-weight:700; color:rgba(15,23,42,.75); margin-bottom:4px; }
  .filter-input{ min-width:190px; border-radius:14px !important; font-weight:600; border:1px solid rgba(226,232,240,.95); }
  .btn-apply{
    height:36px; border-radius:999px; font-weight:800; padding:0 14px;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    border:0; box-shadow:0 16px 28px rgba(22,163,74,.18);
  }

  .range-pill{
    height:36px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 12px; border-radius:999px;
    background:var(--brand-50); color:var(--brand-700);
    border:1px solid rgba(2,6,23,.06);
    font-weight:800; white-space:nowrap;
  }

  /* Chips */
  .hero-bottom{ margin-top:12px; display:flex; align-items:center; justify-content:space-between; gap:12px; position:relative; z-index:2; flex-wrap:wrap; }
  .chips{ display:flex; flex-wrap:wrap; gap:8px; margin:0; }
  .chip{
    display:inline-flex; align-items:center; gap:6px;
    padding:.34rem .72rem; border-radius:999px;
    font-weight:700; font-size:.82rem;
    border:1px solid rgba(2,6,23,.06);
    white-space:nowrap;
  }
  .chip.green{ background:var(--brand-50); color:var(--brand-700); }
  .chip.red{ background:rgba(239,68,68,.12); color:#b91c1c; }
  .chip.blue{ background:rgba(59,130,246,.12); color:#1d4ed8; }

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
  .kpi .value{ font-weight:800; font-size:1.4rem; margin-top:6px; letter-spacing:-.2px; }
  .kpi .hint{ color:var(--ink-600); font-weight:500; font-size:.86rem; margin-top:6px; }
  .kpi-ico{
    width:44px;height:44px;border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    background:var(--brand-50);
    color:var(--brand-700);
    border:1px solid rgba(2,6,23,.06);
    flex:0 0 auto;
  }

  /* List */
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
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    background:
      radial-gradient(700px 140px at 0% 0%, rgba(22,163,74,.10), transparent 60%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.78));
    border-bottom:1px solid rgba(226,232,240,.95);
  }
  .list-title{ margin:0; font-weight:800; font-size:1.02rem; display:flex; align-items:center; gap:8px; }
  .list-body{ padding:6px 16px 14px; }
  .row-item{
    padding:12px 0;
    border-bottom:1px dashed rgba(226,232,240,.95);
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
  }
  .row-item:last-child{ border-bottom:0; }
  .row-main{ min-width:0; }
  .row-main .name{ font-weight:700; margin:0; font-size:.96rem; line-height:1.2; }
  .row-main .meta{ margin:4px 0 0; font-weight:500; color:var(--ink-600); font-size:.84rem; }
  .tag{
    flex:0 0 auto;
    padding:.25rem .65rem;
    border-radius:999px;
    font-weight:700;
    font-size:.78rem;
    border:1px solid rgba(2,6,23,.06);
    white-space:nowrap;
    text-decoration:none;
  }
  .tag.red{ background:rgba(239,68,68,.12); color:#b91c1c; }
</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="hero">
  <div class="hero-row">
    <div class="hero-left">
      <h1 class="title">Dashboard Bendahara</h1>
      <p class="sub">Monitoring verifikasi pembayaran, pengeluaran, dan saldo kas STAS-RG.</p>

      <form method="GET" action="{{ route('bendahara.dashboard') }}" class="filter-block">
        <div>
          <div class="filter-label">Dari</div>
          <input type="date" name="start_date" class="form-control form-control-sm filter-input"
                 value="{{ $start?->toDateString() }}">
        </div>
        <div>
          <div class="filter-label">Sampai</div>
          <input type="date" name="end_date" class="form-control form-control-sm filter-input"
                 value="{{ $end?->toDateString() }}">
        </div>

        <button type="submit" class="btn btn-sm btn-apply text-white">
          <i class="bi bi-funnel"></i> Terapkan
        </button>

        <span class="range-pill">
          <i class="bi bi-calendar3"></i>
          {{ $start?->toDateString() }} — {{ $end?->toDateString() }}
        </span>
      </form>
    </div>

    <div class="hero-actions">
      <a href="{{ route('bendahara.verifikasi') }}" class="btn-manual">
        <i class="bi bi-check2-square"></i> Verifikasi Pembayaran
      </a>
    </div>
  </div>

  <div class="hero-bottom">
    <div class="chips">
      <span class="chip red">
        <i class="bi bi-bell"></i> {{ $pendingCount }} menunggu verifikasi
      </span>
      <span class="chip green">
        <i class="bi bi-cash-stack"></i> Total pending: Rp {{ number_format($pendingNominal,0,',','.') }}
      </span>
      <span class="chip blue">
        <i class="bi bi-x-circle"></i> Ditolak: {{ $rejectPaymentCount }}
      </span>
    </div>
    <div style="min-width:1px;"></div>
  </div>
</section>

{{-- KPI --}}
<section class="row g-3 mt-2">
  <div class="col-12 col-md-6 col-lg-3">
    <div class="kpi">
      <div class="top">
        <div>
          <div class="label">Pending Payment</div>
          <div class="value">{{ $pendingCount }}</div>
          <div class="hint">Request siap diverifikasi</div>
        </div>
        <div class="kpi-ico"><i class="bi bi-bell fs-5"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-lg-3">
    <div class="kpi">
      <div class="top">
        <div>
          <div class="label">Pengeluaran (Periode)</div>
          <div class="value">Rp {{ number_format($donePengeluaranPeriod,0,',','.') }}</div>
          <div class="hint">Dari pencatatan_keuangan</div>
        </div>
        <div class="kpi-ico"><i class="bi bi-arrow-up-circle fs-5"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-lg-3">
    <div class="kpi">
      <div class="top">
        <div>
          <div class="label">Saldo Kas (Periode)</div>
          <div class="value">Rp {{ number_format($kasSaldo,0,',','.') }}</div>
          <div class="hint">Masuk: Rp {{ number_format($kasMasuk,0,',','.') }} • Keluar: Rp {{ number_format($kasKeluar,0,',','.') }}</div>
        </div>
        <div class="kpi-ico"><i class="bi bi-wallet2 fs-5"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-lg-3">
    <div class="kpi">
      <div class="top">
        <div>
          <div class="label">Project</div>
          <div class="value">{{ $projectAktif }} aktif</div>
          <div class="hint">{{ $projectDitutup }} ditutup</div>
        </div>
        <div class="kpi-ico"><i class="bi bi-kanban fs-5"></i></div>
      </div>
    </div>
  </div>
</section>

{{-- LIST --}}
<section class="row g-3 mt-3">
  <div class="col-12 col-lg-7">
    <div class="list-card">
      <div class="list-head">
        <h3 class="list-title"><i class="bi bi-bag-check"></i> Menunggu Verifikasi</h3>
        <span class="chip red">Top 10</span>
      </div>

      <div class="list-body">
        @forelse($pendingList as $r)
          <div class="row-item">
            <div class="row-main">
              <p class="name">{{ $r->no_request }} • {{ $r->nama_project }}</p>
              <p class="meta">
                {{ $r->tgl_request }} • Rp {{ number_format($r->total_harga,0,',','.') }}
              </p>
            </div>

            <a href="{{ route('requestpembelian.detail', $r->id) }}" class="tag red">
              Verifikasi
            </a>
          </div>
        @empty
          <div class="py-3 text-muted" style="font-weight:600;">Tidak ada data submit_payment.</div>
        @endforelse
      </div>
    </div>
  </div>
</section>

@endsection
