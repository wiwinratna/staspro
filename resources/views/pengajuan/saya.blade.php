@extends('layouts.panel')

@section('title','Pengajuan Saya')

@push('styles')
<style>
  :root{
    --brand:#16a34a;
    --brand-700:#15803d;
    --brand-50:#ecfdf5;

    --ink:#0f172a;
    --ink-600:#475569;
    --line:#e2e8f0;

    --shadow:0 10px 30px rgba(15,23,42,.08);
  }

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

  .hero-title{ font-size:1.65rem; font-weight:900; margin:0; letter-spacing:-.2px; color:var(--ink); }
  .hero-sub{ margin:6px 0 0; color:var(--ink-600); font-weight:600; }

  .tabs{ margin-top:12px; display:flex; gap:8px; flex-wrap:wrap; }
  .tab-btn{
    height:36px; display:inline-flex; align-items:center; gap:8px;
    padding:0 14px; border-radius:999px; font-weight:900; text-decoration:none;
    border:1px solid rgba(226,232,240,.95); background:#fff; color:var(--ink);
    box-shadow:0 12px 24px rgba(15,23,42,.06); transition:.15s; white-space:nowrap;
  }
  .tab-btn:hover{ background:var(--brand-50); color:var(--brand-700); transform:translateY(-1px); }
  .tab-btn.active{
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    color:#fff; border-color:transparent; box-shadow:0 16px 28px rgba(22,163,74,.18);
  }

  .tools-row{
    margin-top:14px; display:flex; align-items:center; justify-content:space-between;
    gap:12px; flex-wrap:wrap;
  }
  .tools-left{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .tools-right{ margin-left:auto; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }

  .search-wrap{
    height:38px; display:flex; align-items:center; gap:10px;
    background:#fff; border:1px solid rgba(226,232,240,.95); border-radius:999px;
    padding:0 12px; box-shadow:0 10px 26px rgba(15,23,42,.05);
    width:420px; max-width:100%;
  }
  .search-wrap i{ color:var(--ink-600); line-height:1; }
  .search-input{ height:100%; width:100%; border:0; outline:0; font-weight:800; background:transparent; padding:0; }

  .btn-primary-pill{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:900; padding:0 14px;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    border:0; box-shadow:0 16px 28px rgba(22,163,74,.18);
    white-space:nowrap; color:#fff; text-decoration:none; transition:.15s;
  }
  .btn-primary-pill:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }

  .btn-manual{
    height:36px; display:inline-flex; align-items:center; gap:8px;
    padding:0 14px; border-radius:999px; font-weight:900;
    background:#fff; color:var(--ink); border:1px solid rgba(226,232,240,.95);
    text-decoration:none; box-shadow:0 12px 24px rgba(15,23,42,.06);
    white-space:nowrap; transition:.15s;
  }
  .btn-manual:hover{ background:var(--brand-50); color:var(--brand-700); transform:translateY(-1px); }

  @media(max-width:991.98px){
    .search-wrap{ width:100%; }
    .tools-right{ width:100%; margin-left:0; justify-content:flex-start; }
  }

  .kpi-mini{
    background:#fff; border:1px solid rgba(226,232,240,.95); border-radius:16px;
    padding:12px 14px; box-shadow:0 10px 26px rgba(15,23,42,.05);
    position:relative; overflow:hidden;
  }
  .kpi-mini::before{
    content:""; position:absolute; inset:0;
    background: radial-gradient(420px 140px at 0% 0%, rgba(22,163,74,.10), transparent 60%);
    pointer-events:none;
  }
  .kpi-mini .label{
    color:var(--ink-600); font-weight:900; font-size:.78rem;
    text-transform:uppercase; letter-spacing:.06em; position:relative;
  }
  .kpi-mini .val{ font-size:1.35rem; font-weight:900; margin-top:4px; color:var(--ink); position:relative; }

  .table-card{
    background:#fff; border:1px solid rgba(226,232,240,.95); border-radius:18px;
    box-shadow:0 10px 26px rgba(15,23,42,.05); overflow:hidden;
  }

  .badge-wf{
    font-weight:900; border-radius:999px; padding:.4rem .7rem;
    display:inline-flex; align-items:center; gap:8px; white-space:nowrap;
  }
  .badge-wf .dot{ width:8px; height:8px; border-radius:999px; background:currentColor; opacity:.55; }
  .wf-submitted{ background:rgba(59,130,246,.12); color:#1d4ed8; }
  .wf-approved { background:rgba(245,158,11,.14); color:#b45309; }
  .wf-funded   { background:rgba(16,185,129,.14); color:#047857; }
  .wf-finalized{ background:rgba(22,163,74,.14); color:#15803d; }
  .wf-unknown  { background:rgba(148,163,184,.18); color:#334155; }

  .funded-info{
    margin-top:6px; display:inline-flex; align-items:center; gap:8px;
    padding:6px 10px; border-radius:999px;
    background:rgba(16,185,129,.10); color:#047857;
    font-weight:900; font-size:.82rem;
  }
  .funded-info .muted{ color:rgba(71,85,105,.95); font-weight:800; }

  .btn-pill{
    border-radius:999px; font-weight:900; padding:.35rem .7rem; white-space:nowrap;
  }
</style>
@endpush

@section('content')

<section class="hero">
  <div class="hero-inner">
    <h1 class="hero-title">Pengajuan Saya</h1>
    <p class="hero-sub">Pantau status pengajuan project sebelum benar-benar aktif.</p>

    <div class="tabs">
      <a href="{{ route('project.index') }}" class="tab-btn">
        <i class="bi bi-kanban"></i> Project
      </a>
      <a href="{{ route('pengajuan.saya') }}" class="tab-btn active">
        <i class="bi bi-clipboard-check"></i> Pengajuan Saya
      </a>
    </div>

    <div class="tools-row">
      <div class="tools-left">
        <div class="search-wrap">
          <i class="bi bi-search"></i>
          <input id="searchPengajuan" class="search-input" placeholder="Cari pengajuan (nama / tahun / status)">
        </div>

        <a href="{{ route('project.create') }}" class="btn-primary-pill">
          <i class="bi bi-plus-lg"></i> Ajukan Project
        </a>
      </div>

      <div class="tools-right">
        <a class="btn-manual"
           href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
           target="_blank" rel="noopener" title="Buka Manual Book">
          <i class="bi bi-book"></i> Manual Book
        </a>
      </div>
    </div>
  </div>
</section>

<section class="row g-3 mt-2">
  <div class="col-md-3"><div class="kpi-mini"><div class="label">Submitted</div><div class="val">{{ $countSubmitted }}</div></div></div>
  <div class="col-md-3"><div class="kpi-mini"><div class="label">Approved</div><div class="val">{{ $countApproved }}</div></div></div>
  <div class="col-md-3"><div class="kpi-mini"><div class="label">Funded</div><div class="val">{{ $countFunded }}</div></div></div>
  <div class="col-md-3"><div class="kpi-mini"><div class="label">Finalized</div><div class="val">{{ $countFinalized }}</div></div></div>
</section>

<section class="mt-3 table-card">
  <div class="p-3 d-flex align-items-center justify-content-between">
    <div style="font-weight:900;">
      <i class="bi bi-clipboard-check"></i> Daftar Pengajuan
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Project</th>
          <th>Status Pengajuan</th>
          <th>Submitted</th>
          <th>Approved</th>
          <th>Funded</th>
          <th>Finalized</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @forelse($rows as $r)
          @php
            $wf = strtolower($r->workflow_status ?? 'unknown');

            $wfClass = match($wf){
              'submitted' => 'wf-submitted',
              'approved'  => 'wf-approved',
              'funded'    => 'wf-funded',
              'finalized' => 'wf-finalized',
              default     => 'wf-unknown'
            };

            $label = match($wf){
              'submitted' => 'Submitted',
              'approved'  => 'Approved',
              'funded'    => 'Dana Cair',
              'finalized' => 'Final',
              default     => 'Unknown'
            };

            $searchText = strtolower(
              ($r->nama_project ?? '').' '.($r->tahun ?? '').' '.($r->status ?? '').' '.($label ?? '')
            );

            $fundedTotal = (int)($r->funded_total ?? 0);

            // ✅ cek apakah RAB pernah diset ulang (tanpa nambah kolom)
            $rabEverSet = \Illuminate\Support\Facades\DB::table('detail_subkategori')
              ->where('id_project', $r->id)
              ->whereNotNull('anggaran_revisi')
              ->exists();

            $isCreator = (int)($r->user_id_created ?? 0) === (int)Auth::id();
          @endphp

          <tr class="pengajuan-item" data-search="{{ $searchText }}">
            <td>
              <div class="fw-bold">{{ $r->nama_project }}</div>
              <div class="text-muted small">
                Tahun {{ $r->tahun }}
                @if($r->status) • Status: {{ $r->status }} @endif
              </div>

              @if($wf === 'funded')
                <div class="funded-info">
                  <i class="bi bi-cash-coin"></i>
                  Dana cair masuk: Rp {{ number_format($fundedTotal, 0, ',', '.') }}
                  <span class="muted">• jadi saldo awal</span>
                </div>
              @endif
            </td>

            <td>
              <span class="badge badge-wf {{ $wfClass }}">
                <span class="dot"></span> {{ $label }}
              </span>
            </td>

            <td class="small text-muted">{{ $r->submitted_at ? \Carbon\Carbon::parse($r->submitted_at)->format('d M Y') : '-' }}</td>
            <td class="small text-muted">{{ $r->approved_at ? \Carbon\Carbon::parse($r->approved_at)->format('d M Y') : '-' }}</td>
            <td class="small text-muted">{{ $r->funded_at ? \Carbon\Carbon::parse($r->funded_at)->format('d M Y') : '-' }}</td>
            <td class="small text-muted">{{ $r->finalized_at ? \Carbon\Carbon::parse($r->finalized_at)->format('d M Y') : '-' }}</td>

            <td class="text-end">
              <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                <a href="{{ route('project.show', $r->id) }}" class="btn btn-sm btn-outline-success btn-pill">
                  <i class="bi bi-eye"></i> Detail
                </a>

                {{-- ✅ Set Ulang RAB hanya muncul kalau:
                    - status funded
                    - yang login adalah pengaju/ketua
                    - RAB belum pernah diset ulang (belum ada anggaran_revisi sama sekali)
                --}}
                @if($wf === 'funded' && $isCreator && !$rabEverSet)
                  <a href="{{ route('project.show', $r->id) }}?mode=rab" class="btn btn-sm btn-success btn-pill">
                    <i class="bi bi-pencil-square"></i> Set Ulang RAB
                  </a>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              Belum ada pengajuan. Klik <b>Ajukan Project</b> untuk mulai.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

<script>
  document.getElementById('searchPengajuan')?.addEventListener('input', function(){
    const q = (this.value || '').toLowerCase().trim();
    document.querySelectorAll('.pengajuan-item').forEach(el=>{
      el.style.display = (el.dataset.search || '').includes(q) ? '' : 'none';
    });
  });
</script>

@endsection
