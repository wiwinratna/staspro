@extends('layouts.panel')
@section('title','Funding')

@push('styles')
<style>
  /* ===== Nuansa sama dashboard ===== */
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
  .hero-row{ position:relative; z-index:2; display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap; }
  .hero-left{ min-width:260px; flex:1 1 520px; }
  .hero-left .title{ font-size:1.65rem; font-weight:800; margin:0; letter-spacing:-.2px; }
  .hero-left .sub{ margin:6px 0 0; color:var(--ink-600); font-weight:500; }

  /* tombol pill ala dashboard */
  .btn-pill{
    height:36px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 14px;
    border-radius:999px;
    font-weight:800;
    white-space:nowrap;
    text-decoration:none;
  }
  .btn-add{
    background:#fff;
    color:var(--ink);
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 12px 24px rgba(15,23,42,.06);
    transition:.15s;
  }
  .btn-add:hover{
    background:var(--brand-50);
    color:var(--brand-700);
    transform:translateY(-1px);
  }

  /* KPI card nuansa dashboard */
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
    font-size:1.1rem;
  }

  /* list card ala dashboard */
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
  .list-body{ padding:12px 16px 14px; }

  /* chip & tag */
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

  .tag{
    display:inline-flex; align-items:center; justify-content:center;
    padding:.25rem .65rem;
    border-radius:999px;
    font-weight:700;
    font-size:.78rem;
    border:1px solid rgba(2,6,23,.06);
    white-space:nowrap;
    text-decoration:none;
  }
  .tag.green{ background:rgba(22,163,74,.10); color:var(--brand-700); }
  .tag.green:hover{ background:var(--brand-50); color:var(--brand-700); }

  /* filter form lebih “dashboard” */
  .filter-grid .form-label{
    font-size:.78rem;
    font-weight:800;
    color:rgba(15,23,42,.75);
    margin-bottom:6px;
  }
  .filter-grid .form-control,
  .filter-grid .form-select{
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
    color:#fff;
  }
  .btn-apply:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }
  .btn-reset{
    height:36px;
    border-radius:999px;
    font-weight:800;
    padding:0 14px;
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
  }
  .btn-reset:hover{ background:var(--brand-50); color:var(--brand-700); transform:translateY(-1px); }

  /* table nuansa dashboard */
  .table-wrap{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:18px;
    overflow:hidden;
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
    position:sticky;
    top:0;
    z-index:5;
  }
  .table-modern tbody td{
    padding:14px 12px;
    vertical-align:middle;
    border-top:1px solid #eef2f7;
    font-weight:500;
  }
  .table-striped > tbody > tr:nth-of-type(odd){ background:#fcfcfd; }
  .table-modern tbody tr:hover{ background:var(--brand-50); transition:.12s; }
</style>
@endpush

@section('content')
  <section class="hero">
    <div class="hero-row">
      <div class="hero-left">
        <h1 class="title">Funding</h1>
        <p class="sub">Pencatatan dana cair (masuk) per project.</p>
      </div>

      <div class="hero-actions">
        {{-- tombolnya dibikin nuansa pill dashboard (tanpa ubah route) --}}
        <a href="{{ route('funding.create') }}" class="btn-pill btn-add">
          <i class="bi bi-plus-circle"></i> Input Dana Cair
        </a>
      </div>
    </div>
  </section>

  {{-- RINGKASAN --}}
  <section class="row g-3 mt-2">
    <div class="col-md-6 col-lg-4">
      <div class="kpi">
        <div class="top">
          <div>
            <div class="label">Total Dana Cair</div>
            <div class="value">Rp {{ number_format($totalDana,0,',','.') }}</div>
            <div class="hint">Sesuai filter periode</div>
          </div>
          <div class="kpi-ico"><i class="bi bi-cash-coin"></i></div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="kpi">
        <div class="top">
          <div>
            <div class="label">Jumlah Pencairan</div>
            <div class="value">{{ $jumlahTransaksi }}</div>
            <div class="hint">Total transaksi funding</div>
          </div>
          <div class="kpi-ico"><i class="bi bi-receipt"></i></div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="kpi">
        <div class="top">
          <div>
            <div class="label">Project Terdampak</div>
            <div class="value">{{ $jumlahProject }}</div>
            <div class="hint">Project yg menerima funding</div>
          </div>
          <div class="kpi-ico"><i class="bi bi-diagram-3"></i></div>
        </div>
      </div>
    </div>
  </section>

  {{-- FILTER --}}
  <section class="list-card mt-3">
    <div class="list-head">
      <h3 class="list-title"><i class="bi bi-funnel"></i> Filter</h3>
      <span class="chip green"><i class="bi bi-sliders"></i> Opsional</span>
    </div>

    <div class="list-body">
      <form method="GET" action="{{ route('funding.index') }}" class="row g-2 align-items-end filter-grid">

        <div class="col-md-4">
          <label class="form-label">Tim / Project</label>
          <select name="project_id" class="form-select">
            <option value="">Semua Tim</option>
            @foreach($projects as $p)
              <option value="{{ $p->id }}" @selected((string)$projectId === (string)$p->id)>
                {{ $p->nama_project }} ({{ $p->tahun }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Metode</label>
          <select name="metode" class="form-select">
            <option value="">Semua Metode</option>
            @foreach($metodeOptions as $m)
              <option value="{{ $m }}" @selected($metode === $m)>{{ $m }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Sumber</label>
          <select name="sumber" class="form-select">
            <option value="">Semua Sumber</option>
            @foreach($sumberOptions as $s)
              <option value="{{ $s }}" @selected($sumber === $s)>{{ $s }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Dari</label>
          <input type="date" name="start_date" value="{{ $start }}" class="form-control">
        </div>

        <div class="col-md-2">
          <label class="form-label">Sampai</label>
          <input type="date" name="end_date" value="{{ $end }}" class="form-control">
        </div>

        <div class="col-12 d-flex gap-2 mt-1">
          <button class="btn btn-apply">
            <i class="bi bi-check2-circle"></i> Terapkan
          </button>
          <a href="{{ route('funding.index') }}" class="btn btn-reset">
            Reset
          </a>
        </div>

      </form>
    </div>
  </section>

  {{-- TABLE --}}
  <section class="list-card mt-3">
    <div class="list-head">
      <h3 class="list-title"><i class="bi bi-list-ul"></i> Riwayat Dana Cair</h3>
      <span class="chip green"><i class="bi bi-collection"></i> Total: {{ $rows->total() }}</span>
    </div>

    <div class="list-body">
      <div class="table-wrap">
        <div class="table-responsive">
          <table class="table table-modern table-striped align-middle">
            <thead>
              <tr>
                <th style="width:60px">No</th>
                <th>Tanggal</th>
                <th>Tim</th>
                <th>Sumber</th>
                <th>Metode</th>
                <th class="text-end">Nominal</th>
                <th>Bukti</th>
                <th>Diinput Oleh</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $i => $r)
                <tr>
                  <td>{{ $rows->firstItem() + $i }}</td>
                  <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y') }}</td>
                  <td>{{ $r->nama_project }}</td>
                  <td>{{ $r->sumber_dana ?? '-' }}</td>
                  <td>{{ $r->metode_penerimaan ?? '-' }}</td>
                  <td class="text-end">Rp {{ number_format($r->nominal,0,',','.') }}</td>
                  <td>
                    @if($r->bukti)
                      <a class="tag green" href="{{ route('funding.bukti', $r->id) }}">
                        <i class="bi bi-download"></i> Download
                      </a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>{{ $r->created_name }}</td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-muted py-4">Belum ada data funding.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-2">
        {{ $rows->links() }}
      </div>
    </div>
  </section>
@endsection
