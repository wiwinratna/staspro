@extends('layouts.panel')

@section('title','Pengajuan Masuk')

@push('styles')
<style>
  .hero{
    border-radius:22px;
    padding:18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 10px 30px rgba(15,23,42,.08);
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

  .hero-row{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
  }
  .hero-left .title{
    font-size:1.65rem;
    font-weight:900;
    margin:0;
    letter-spacing:-.2px;
    color:#0f172a;
  }
  .hero-left .sub{
    margin:6px 0 0;
    color:#475569;
    font-weight:600;
  }

  .tabs{
    margin-top:12px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
  }
  .tab-btn{
    height:36px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 14px;
    border-radius:999px;
    font-weight:900;
    text-decoration:none;
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    color:#0f172a;
    box-shadow:0 12px 24px rgba(15,23,42,.06);
    transition:.15s;
    white-space:nowrap;
  }
  .tab-btn:hover{ background:#ecfdf5; color:#15803d; transform:translateY(-1px); }
  .tab-btn.active{
    background:linear-gradient(135deg,#15803d,#16a34a);
    color:#fff;
    border-color:transparent;
    box-shadow:0 16px 28px rgba(22,163,74,.18);
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
  .search-wrap{
    height:38px;
    display:flex;
    align-items:center;
    gap:10px;
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:999px;
    padding:0 12px;
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    width:420px;
    max-width:100%;
  }
  .search-wrap i{ color:#475569; line-height:1; }
  .search-input{
    height:100%;
    width:100%;
    border:0;
    outline:0;
    font-weight:800;
    background:transparent;
    padding:0;
  }
  .btn-manual{
    height:36px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 14px;
    border-radius:999px;
    font-weight:900;
    background:#fff;
    color:#0f172a;
    border:1px solid rgba(226,232,240,.95);
    text-decoration:none;
    box-shadow:0 12px 24px rgba(15,23,42,.06);
    white-space:nowrap;
    transition:.15s;
  }
  .btn-manual:hover{ background:#ecfdf5; color:#15803d; transform:translateY(-1px); }

  @media(max-width:991.98px){
    .search-wrap{ width:100%; }
    .tools-right{ width:100%; margin-left:0; justify-content:flex-start; }
  }

  /* KPI MINI (klikable) */
  .kpi-mini{
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:16px;
    padding:12px 14px;
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    width:100%;
    cursor:pointer;
    transition:.15s;
    position:relative;
    overflow:hidden;
  }
  .kpi-mini:hover{ transform:translateY(-1px); }
  .kpi-mini.active{
    border-color: rgba(22,163,74,.35);
    box-shadow:0 14px 30px rgba(22,163,74,.14);
  }
  .kpi-mini .label{
    color:#475569;
    font-weight:900;
    font-size:.78rem;
    text-transform:uppercase;
    letter-spacing:.06em;
  }
  .kpi-mini .val{
    font-size:1.35rem;
    font-weight:900;
    margin-top:4px;
    color:#0f172a;
  }
  .kpi-mini .hint{
    margin-top:6px;
    font-size:.75rem;
    color:#64748b;
    font-weight:700;
  }

  .table-card{
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:18px;
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    overflow:hidden;
  }

  .badge-wf{
    font-weight:900;
    border-radius:999px;
    padding:.4rem .7rem;
    display:inline-flex;
    align-items:center;
    gap:8px;
    white-space:nowrap;
  }
  .badge-wf .dot{
    width:8px; height:8px; border-radius:999px;
    background:currentColor;
    opacity:.55;
  }

  .wf-submitted{ background:rgba(59,130,246,.12); color:#1d4ed8; }
  .wf-approved{ background:rgba(245,158,11,.14); color:#b45309; }
  .wf-funded{ background:rgba(16,185,129,.14); color:#047857; }
  .wf-finalized{ background:rgba(22,163,74,.14); color:#15803d; }
  .wf-rejected{ background:rgba(239,68,68,.12); color:#b91c1c; }
  .wf-unknown{ background:rgba(148,163,184,.18); color:#334155; }

  .btn-pill{
    border-radius:999px;
    font-weight:900;
    padding:.35rem .7rem;
    white-space:nowrap;
  }
  .btn-approve{
    background:linear-gradient(135deg,#15803d,#16a34a);
    border:0;
    color:#fff;
    box-shadow:0 10px 18px rgba(22,163,74,.18);
  }
  .btn-approve:hover{ filter:brightness(.98); color:#fff; }
  .btn-reject{
    background:#fff;
    border:1px solid rgba(239,68,68,.35);
    color:#b91c1c;
  }
  .btn-reject:hover{ background:rgba(239,68,68,.08); color:#b91c1c; }

  .filter-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:.25rem .7rem;
    border-radius:999px;
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    font-weight:900;
    color:#0f172a;
  }

  .mini-note{
    font-size:.78rem;
    color:#64748b;
    font-weight:800;
  }
</style>
@endpush

@section('content')

<section class="hero">
  <div class="hero-inner">
    <div class="hero-row">
      <div class="hero-left">
        <h1 class="title">Pengajuan Masuk</h1>
        <p class="sub">Kelola pengajuan project dari peneliti: approve dan finalize.</p>

        <div class="tabs">
          <a href="{{ route('project.index') }}" class="tab-btn">
            <i class="bi bi-kanban"></i> Project
          </a>
          <a href="{{ route('pengajuan.masuk') }}" class="tab-btn active">
            <i class="bi bi-inbox"></i> Pengajuan Masuk
          </a>
        </div>

        <div class="tools-row">
          <div class="tools-left">
            <div class="search-wrap">
              <i class="bi bi-search"></i>
              <input id="searchPengajuan" class="search-input" placeholder="Cari pengajuan (nama / tahun / status)">
            </div>
          </div>

          <div class="tools-right">
            <a class="btn-manual"
               href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
               target="_blank" rel="noopener">
              <i class="bi bi-book"></i> Manual Book
            </a>
          </div>
        </div>

        <div class="mini-note mt-2">
          Alur: <b>Submitted</b> → <b>Approved</b> (menunggu dana cair) → <b>Dana Cair</b> (ketua set ulang RAB) → <b>Finalized</b>.
        </div>
      </div>
    </div>
  </div>
</section>

@if ($message = Session::get('success'))
  <div class="alert alert-success">{{ $message }}</div>
@endif
@if ($message = Session::get('error'))
  <div class="alert alert-danger">{{ $message }}</div>
@endif

<section class="row g-3 mt-2">
  <div class="col-md-3 d-flex">
    <div class="kpi-mini kpi-filter active" data-filter="all">
      <div class="label">All</div>
      <div class="val">{{ $rows->count() }}</div>
      <div class="hint">Klik untuk tampilkan semua</div>
    </div>
  </div>

  <div class="col-md-3 d-flex">
    <div class="kpi-mini kpi-filter" data-filter="submitted">
      <div class="label">Submitted</div>
      <div class="val">{{ $countSubmitted ?? 0 }}</div>
      <div class="hint">Klik untuk filter submitted</div>
    </div>
  </div>

  <div class="col-md-3 d-flex">
    <div class="kpi-mini kpi-filter" data-filter="approved">
      <div class="label">Approved</div>
      <div class="val">{{ $countApproved ?? 0 }}</div>
      <div class="hint">Menunggu dana cair</div>
    </div>
  </div>

  <div class="col-md-3 d-flex">
    <div class="kpi-mini kpi-filter" data-filter="funded">
      <div class="label">Dana Cair</div>
      <div class="val">{{ $countFunded ?? 0 }}</div>
      <div class="hint">Menunggu set ulang RAB</div>
    </div>
  </div>

  <div class="col-md-3 d-flex">
    <div class="kpi-mini kpi-filter" data-filter="finalized">
      <div class="label">Finalized</div>
      <div class="val">{{ $countFinalized ?? 0 }}</div>
      <div class="hint">Sudah final</div>
    </div>
  </div>
</section>

<section class="mt-3 table-card">
  <div class="p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div style="font-weight:900;">
      <i class="bi bi-inbox"></i> Daftar Pengajuan Masuk
    </div>

    <div class="filter-pill">
      <i class="bi bi-funnel"></i>
      Filter: <span id="activeFilterLabel">All</span>
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
          <th>Dana Cair</th>
          <th>Finalized</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>

      <tbody id="pengajuanTbody">
        @forelse($rows as $r)
          @php
            $wf = strtolower($r->workflow_status ?? 'unknown');

            $wfClass = match($wf){
              'submitted' => 'wf-submitted',
              'approved'  => 'wf-approved',
              'funded'    => 'wf-funded',
              'finalized' => 'wf-finalized',
              'rejected'  => 'wf-rejected',
              default     => 'wf-unknown'
            };

            $label = match($wf){
              'submitted' => 'Submitted',
              'approved'  => 'Approved (nunggu dana cair)',
              'funded'    => 'Dana Cair (set ulang RAB)',
              'finalized' => 'Finalized',
              'rejected'  => 'Rejected',
              default     => 'Unknown'
            };

            // ✅ indikator ketua sudah set ulang RAB (tanpa nambah kolom)
            $rabDone = \Illuminate\Support\Facades\DB::table('detail_subkategori')
              ->where('id_project', $r->id)
              ->whereNotNull('anggaran_revisi')
              ->exists();

            $roleNow = strtolower(auth()->user()->role ?? '');

            // ✅ tombol FINALIZE: muncul hanya saat funded + rabDone (admin/bendahara)
            $canFinalize = ($wf === 'funded')
              && in_array($roleNow, ['admin','bendahara'], true)
              && $rabDone;

            $search = strtolower(
              ($r->nama_project ?? '').' '.($r->tahun ?? '').' '.($r->status ?? '').' '.$label
            );
          @endphp

          <tr class="pengajuan-item"
              data-search="{{ $search }}"
              data-wf="{{ $wf }}">
            <td>
              <div class="fw-bold">{{ $r->nama_project }}</div>
              <div class="text-muted small">
                Tahun {{ $r->tahun }}
                @if($r->status) • Status: {{ $r->status }} @endif
              </div>

              {{-- info kecil per step --}}
              @if($wf === 'approved')
                <div class="small mt-1">
                  <span class="badge bg-secondary-subtle text-secondary" style="border-radius:999px;font-weight:900">
                    Menunggu dana cair
                  </span>
                </div>
              @elseif($wf === 'funded')
                <div class="small mt-1">
                  @if($rabDone)
                    <span class="badge bg-success-subtle text-success" style="border-radius:999px;font-weight:900">
                      RAB revisi siap (boleh finalize)
                    </span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary" style="border-radius:999px;font-weight:900">
                      Menunggu ketua set ulang RAB
                    </span>
                  @endif
                </div>
              @endif
            </td>

            <td>
              <span class="badge badge-wf {{ $wfClass }}">
                <span class="dot"></span> {{ $label }}
              </span>
            </td>

            <td class="small text-muted">
              {{ $r->submitted_at ? \Carbon\Carbon::parse($r->submitted_at)->format('d M Y') : '-' }}
            </td>
            <td class="small text-muted">
              {{ $r->approved_at ? \Carbon\Carbon::parse($r->approved_at)->format('d M Y') : '-' }}
            </td>
            <td class="small text-muted">
              {{ $r->funded_at ? \Carbon\Carbon::parse($r->funded_at)->format('d M Y') : '-' }}
            </td>
            <td class="small text-muted">
              {{ $r->finalized_at ? \Carbon\Carbon::parse($r->finalized_at)->format('d M Y') : '-' }}
            </td>

            <td class="text-end">
              <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                <a href="{{ route('project.show', $r->id) }}" class="btn btn-sm btn-outline-success btn-pill">
                  <i class="bi bi-eye"></i> Detail
                </a>

                {{-- APPROVE/REJECT: hanya admin --}}
                @if($wf === 'submitted' && $roleNow === 'admin')
                  <form action="{{ route('pengajuan.approve', $r->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Approve pengajuan ini?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-approve btn-pill">
                      <i class="bi bi-check2-circle"></i> Approve
                    </button>
                  </form>

                  <form action="{{ route('pengajuan.reject', $r->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Reject pengajuan ini?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-reject btn-pill">
                      <i class="bi bi-x-circle"></i> Reject
                    </button>
                  </form>
                @endif

                {{-- FINALIZED: admin & bendahara; hanya kalau ketua sudah set ulang RAB --}}
                @if($canFinalize)
                  <form action="{{ route('pengajuan.finalize', $r->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Finalize project ini? Setelah finalize, revisi RAB akan dipakai.');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-approve btn-pill">
                      <i class="bi bi-flag-fill"></i> Finalize
                    </button>
                  </form>
                @endif

              </div>
            </td>
          </tr>
        @empty
          <tr id="emptyRow">
            <td colspan="7" class="text-center text-muted py-4">
              Belum ada pengajuan masuk.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

<script>
  const searchInput = document.getElementById('searchPengajuan');
  const rows = Array.from(document.querySelectorAll('.pengajuan-item'));
  const kpis = Array.from(document.querySelectorAll('.kpi-filter'));
  const activeFilterLabel = document.getElementById('activeFilterLabel');

  let activeFilter = 'all';
  let activeQuery = '';

  function applyFilters(){
    const q = (activeQuery || '').toLowerCase().trim();

    let shown = 0;
    rows.forEach(tr => {
      const wf = (tr.dataset.wf || '').toLowerCase();
      const search = (tr.dataset.search || '');

      const passFilter = (activeFilter === 'all') ? true : (wf === activeFilter);
      const passSearch = !q ? true : search.includes(q);

      const show = passFilter && passSearch;
      tr.style.display = show ? '' : 'none';
      if(show) shown++;
    });

    const emptyRow = document.getElementById('emptyRow');
    if(emptyRow){
      emptyRow.style.display = shown === 0 ? '' : 'none';
    }
  }

  // klik KPI untuk filter
  kpis.forEach(card => {
    card.addEventListener('click', () => {
      activeFilter = card.dataset.filter || 'all';

      kpis.forEach(x => x.classList.remove('active'));
      card.classList.add('active');

      const labelMap = { all:'All', submitted:'Submitted', approved:'Approved', funded:'Dana Cair', finalized:'Finalized' };
      activeFilterLabel.textContent = labelMap[activeFilter] || activeFilter;

      applyFilters();
    });
  });

  // search
  searchInput?.addEventListener('input', function(){
    activeQuery = this.value || '';
    applyFilters();
  });

  // initial
  applyFilters();
</script>

@endsection
