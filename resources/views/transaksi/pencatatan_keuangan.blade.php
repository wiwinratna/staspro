{{-- resources/views/transaksi/pencatatan_keuangan.blade.php --}}
@extends('layouts.panel')

@section('title', 'Pencatatan Keuangan')

@push('styles')
<style>
  /* ─── shared design tokens ─── */
  .hero{
    border-radius:22px; padding:22px 24px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow: var(--shadow);
    position:relative; overflow:hidden; margin-bottom:14px;
  }
  .hero::after{
    content:""; position:absolute; inset:-1px;
    background:
      radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
      radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
    pointer-events:none; opacity:.65;
  }
  .hero-inner{ position:relative; z-index:2; }
  .hero .title{ font-size:1.55rem; font-weight:800; margin:0; letter-spacing:-.2px; }
  .hero .sub{ margin:6px 0 0; color:var(--ink-600); font-weight:500; font-size:.92rem; }

  /* buttons */
  .btn-brand{
    height:36px; display:inline-flex; align-items:center; gap:7px;
    border-radius:999px; font-weight:800; padding:0 14px; font-size:.84rem;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    border:0; color:#fff; text-decoration:none; white-space:nowrap;
    box-shadow:0 12px 24px rgba(22,163,74,.16);
    transition:transform .12s, box-shadow .12s;
  }
  .btn-brand:hover{ transform:translateY(-1px); box-shadow:0 16px 30px rgba(22,163,74,.22); color:#fff; }
  .btn-soft{
    height:36px; display:inline-flex; align-items:center; gap:7px;
    border-radius:999px; font-weight:800; padding:0 14px; font-size:.84rem;
    background:#fff; color:var(--ink); text-decoration:none; white-space:nowrap;
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 8px 20px rgba(15,23,42,.04);
    transition:transform .12s, box-shadow .12s;
  }
  .btn-soft:hover{ transform:translateY(-1px); box-shadow:0 12px 28px rgba(15,23,42,.08); color:var(--ink); }

  .tools-row{ margin-top:14px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
  .tools-right{ margin-left:auto; display:flex; gap:8px; flex-wrap:wrap; }

  /* summary */
  .stats-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-top:12px; }
  .stat-card{
    background:var(--card); border:1px solid rgba(226,232,240,.95);
    border-radius:18px; padding:16px 18px; box-shadow:var(--shadow);
  }
  .stat-label{ font-size:.70rem; letter-spacing:.08em; text-transform:uppercase; font-weight:900; color:var(--ink-600); }
  .stat-value{ margin-top:4px; font-weight:900; font-size:1.3rem; }
  .tnum{ font-variant-numeric:tabular-nums; }

  /* tabs */
  .tabs-wrap{ margin-top:12px; display:flex; justify-content:center; }
  .tabs{
    display:inline-flex; gap:6px; background:#fff;
    border:1px solid rgba(226,232,240,.95); border-radius:999px;
    padding:5px; box-shadow:0 8px 22px rgba(15,23,42,.04); flex-wrap:wrap; justify-content:center;
  }
  .tab-pill{
    height:30px; display:inline-flex; align-items:center;
    padding:0 12px; border-radius:999px;
    font-weight:900; font-size:.74rem; text-transform:uppercase; letter-spacing:.06em;
    color:var(--ink-600); text-decoration:none;
  }
  .tab-pill.active{
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    color:#fff; box-shadow:0 12px 24px rgba(22,163,74,.16);
  }

  /* filter */
  .filter-card{
    background:var(--card); border:1px solid rgba(226,232,240,.95);
    border-radius:18px; padding:12px 16px; box-shadow:var(--shadow); margin-top:14px;
  }
  .filter-row{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .filter-label{ font-weight:800; font-size:.84rem; color:var(--ink); white-space:nowrap; }
  .form-date{
    height:36px; border-radius:999px; border:1px solid rgba(226,232,240,.95);
    padding:0 12px; font-weight:700; font-size:.85rem; background:#fff;
  }
  .form-date:focus{ border-color:rgba(22,163,74,.45); box-shadow:0 0 0 .2rem rgba(22,163,74,.1); outline:none; }
  .icon-btn{
    height:36px; width:36px; border-radius:999px;
    border:1px solid rgba(226,232,240,.95); background:#fff;
    display:inline-flex; align-items:center; justify-content:center;
    box-shadow:0 8px 20px rgba(15,23,42,.04); text-decoration:none; color:var(--ink);
    transition:transform .12s;
  }
  .icon-btn:hover{ background:#ecfdf5; color:#15803d; transform:translateY(-1px); }

  /* table */
  .table-wrap{
    background:var(--card); border:1px solid rgba(226,232,240,.95);
    border-radius:18px; overflow:hidden; margin-top:14px; box-shadow:var(--shadow);
  }
  .table-responsive{ max-height:68vh; overflow-y:auto; overflow-x:auto; }
  .table-modern{
    margin:0; font-size:.88rem; border-collapse:separate; border-spacing:0;
    table-layout:fixed; width:100%; min-width:1100px;
  }
  .table-modern thead th{
    background:#f8fafc; color:var(--ink-600);
    font-weight:900; text-transform:uppercase; font-size:.70rem; letter-spacing:.08em;
    padding:12px 14px; border-bottom:1px solid rgba(226,232,240,.95);
    position:sticky; top:0; z-index:5; white-space:nowrap;
  }
  .table-modern tbody td{
    padding:12px 14px; vertical-align:middle;
    border-top:1px solid #f1f5f9; overflow:hidden;
  }
  .table-modern tbody tr:hover{ background:#f8fdfb; transition:.12s; }

  /* cell helpers */
  .cell-ellipsis{
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    max-width:100%; display:block;
  }
  .cell-clamp{
    display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:1;
    overflow:hidden; word-break:break-word; line-height:1.4;
  }
  .cell-muted{ color:#94a3b8; font-size:.78rem; font-weight:700; }
  .cell-main{ font-weight:700; color:var(--ink); }

  .tag-auto{
    display:inline-flex; align-items:center; gap:3px;
    font-size:.62rem; font-weight:900; letter-spacing:.04em;
    padding:2px 7px; border-radius:999px; white-space:nowrap;
    background:rgba(22,163,74,.08); color:#166534;
    border:1px solid rgba(22,163,74,.15);
  }

  .link-detail{
    display:inline-flex; align-items:center; gap:4px;
    font-weight:900; font-size:.78rem; text-decoration:none;
    color:var(--brand-700); white-space:nowrap;
  }
  .link-detail:hover{ color:var(--brand); }

  .btn-act{
    width:30px; height:30px; border-radius:8px;
    display:inline-flex; align-items:center; justify-content:center;
    border:1px solid rgba(226,232,240,.95); background:#fff; font-size:.82rem;
  }
  .btn-act.edit{ border-color:rgba(245,158,11,.35); color:#92400e; background:#fff7ed; }
  .btn-act.del{ border-color:rgba(239,68,68,.30); color:#991b1b; background:#fef2f2; }

  @media(max-width:991.98px){
    .stats-grid{ grid-template-columns:1fr; }
    .hero{ padding:16px 18px; }
  }
</style>
@endpush

@section('content')

  {{-- HERO --}}
  <section class="hero">
    <div class="hero-inner">
      <h1 class="title">Pencatatan Keuangan</h1>
      <p class="sub">
        @if(request()->has('start_date') && request()->has('end_date'))
          @if(request('start_date') === request('end_date'))
            Total transaksi pada {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}.
          @else
            Total transaksi dari {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}
            sampai {{ \Carbon\Carbon::parse(request('end_date'))->locale('id')->translatedFormat('d F Y') }}.
          @endif
        @else
          Total nominal transaksi keseluruhan.
        @endif
      </p>

      <div class="tools-row">
        <a href="{{ route('form_input_pencatatan_keuangan') }}" class="btn-brand">
          <i class="bi bi-plus-lg"></i> Tambah Pencatatan
        </a>
        <div class="tools-right">
          <a href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
             target="_blank" rel="noopener" class="btn-soft">
            <i class="bi bi-journal-bookmark"></i> Manual Book
          </a>
        </div>
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success mt-2" style="border-radius:14px;">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger mt-2" style="border-radius:14px;">{{ session('error') }}</div>
  @endif

  {{-- SUMMARY --}}
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label">Total Pemasukan</div>
      <div class="stat-value tnum" id="total-pemasukan">Rp 0</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Pengeluaran</div>
      <div class="stat-value tnum" id="total-pengeluaran">Rp 0</div>
    </div>
  </div>

  {{-- TABS --}}
  <div class="tabs-wrap">
    <div class="tabs" id="filterTabs">
      <a href="#" class="tab-pill active" data-filter="semua">Semua</a>
      <a href="#" class="tab-pill" data-filter="pemasukan">Pemasukan</a>
      <a href="#" class="tab-pill" data-filter="pengeluaran">Pengeluaran</a>
    </div>
  </div>

  {{-- FILTER DATE --}}
  <div class="filter-card">
    <div class="filter-row">
      <div class="filter-label">Filter Tanggal</div>
      <input type="date" id="startDate" name="start_date" class="form-date" form="filterForm" value="{{ request('start_date') }}">
      <span class="text-muted fw-bold" style="font-size:.84rem;">sampai</span>
      <input type="date" id="endDate" name="end_date" class="form-date" form="filterForm" value="{{ request('end_date') }}">
      <a href="{{ route('pencatatan_keuangan') }}" class="icon-btn" title="Reset Filter">
        <i class="bi bi-arrow-counterclockwise"></i>
      </a>
      <div class="ms-auto">
        <button type="submit" form="filterForm" class="btn-brand">
          <i class="bi bi-funnel"></i> Terapkan
        </button>
      </div>
    </div>
    <form id="filterForm" method="GET" action="{{ route('filter_pencatatan_keuangan') }}"></form>
  </div>

  {{-- TABLE --}}
  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table table-modern align-middle">
        <thead>
          <tr>
            <th style="width:50px" class="text-center">No</th>
            <th style="width:120px">Tanggal</th>
            <th style="width:170px">Tim</th>
            <th style="width:280px">Deskripsi</th>
            <th style="width:130px" class="text-end">Jumlah</th>
            <th style="width:120px">Metode</th>
            <th style="width:70px" class="text-center">Bukti</th>
            <th style="width:120px" class="text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          @forelse($pencatatanKeuangans as $index => $transaksi)
            @php
              $tim  = $transaksi->project->nama_project ?? 'Tidak Ada';
              $sub  = $transaksi->subKategoriPendanaan->nama
                    ?? (($transaksi->jenis_transaksi === 'pemasukan') ? 'Dana Cair' : 'Tidak Ada');
              $desk = $transaksi->deskripsi_transaksi ?? '-';

              // Deteksi sumber transaksi (AUTO vs MANUAL)
              $isFunding = is_string($desk) && str_contains($desk, '[FUNDING#');
              $isReqBuy  = is_string($desk) && str_contains($desk, '[REQBUY#');
              $isReqTrx  = is_string($desk) && str_contains($desk, '[REQTRX#');
              $isAuto = $isFunding || $isReqBuy || $isReqTrx;

              $autoLabel = $isFunding ? 'DANA CAIR'
                        : ($isReqBuy ? 'REQUEST BELI'
                        : ($isReqTrx ? 'PENGAJUAN TRX' : null));

              // Clean deskripsi: pisahkan kode marker + hapus '(Auto Finalized) - '
              $deskClean = $desk;
              $deskCode  = '';
              if (preg_match('/\[(FUNDING|REQBUY|REQTRX)#[^\]]*\]\s*/', $desk, $m)) {
                $deskCode  = trim($m[0]);
                $deskClean = trim(str_replace($deskCode, '', $desk));
              }
              $deskClean = preg_replace('/\(Auto Finalized\)\s*-?\s*/', '', $deskClean);
              $deskClean = trim($deskClean) ?: '-';
            @endphp

            <tr data-jenis="{{ strtolower($transaksi->jenis_transaksi ?? 'pengeluaran') }}">
              <td class="text-center">{{ $index + 1 }}</td>

              <td>
                <div class="cell-main" style="font-size:.88rem;">
                  {{ optional($transaksi->tanggal)->format('d-m-Y') ?? $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y') }}
                </div>
                <div class="cell-muted">{{ $transaksi->created_at->timezone('Asia/Jakarta')->format('H:i') }}</div>
              </td>

              <td>
                <div class="cell-ellipsis cell-main" title="{{ $tim }}">{{ $tim }}</div>
              </td>

              <td>
                <div class="cell-clamp cell-main" title="{{ $desk }}">
                  {{ $deskClean ?: '-' }}
                </div>
                @if($isAuto)
                  <span class="tag-auto mt-1">{{ $autoLabel ?? 'AUTO' }}</span>
                @endif
              </td>

              <td class="text-end tnum fw-bold" style="white-space:nowrap;">
                Rp {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}
              </td>

              <td>
                <div class="cell-ellipsis" style="font-weight:700; font-size:.84rem;">{{ strtoupper($transaksi->metode_pembayaran ?? '-') }}</div>
              </td>

              <td class="text-center">
                @if($transaksi->bukti_transaksi)
                  <a href="#" class="link-detail" style="font-size:.78rem;" data-bs-toggle="modal" data-bs-target="#modalBukti{{ $transaksi->id }}">
                    <i class="bi bi-image"></i>
                  </a>

                  <div class="modal fade" id="modalBukti{{ $transaksi->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header" style="background:linear-gradient(135deg, rgba(139,92,246,.06), rgba(139,92,246,.02));">
                          <h5 class="modal-title"><i class="bi bi-image me-2" style="color:#7c3aed;"></i>Bukti Transaksi</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center" style="padding:24px;">
                          <img src="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" alt="Bukti" class="img-fluid rounded shadow-sm mb-3" style="border-radius:14px !important; max-height:400px; object-fit:contain;">
                          <div class="mt-2">
                            <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" class="btn-brand" download>
                              <i class="bi bi-download"></i> Unduh
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @else
                  <span class="cell-muted">—</span>
                @endif
              </td>

              <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                  <a href="#" class="btn-act" style="border-color:rgba(22,163,74,.25); color:var(--brand-700); background:#f0fdf4;" title="Detail" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $transaksi->id }}">
                    <i class="bi bi-eye"></i>
                  </a>
                  @if(!$isAuto)
                    <a href="{{ route('pencatatan_keuangan.edit', $transaksi->id) }}" class="btn-act edit" title="Edit">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('pencatatan_keuangan.destroy', $transaksi->id) }}" method="POST" class="m-0">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn-act del" title="Hapus" onclick="confirmDelete({{ $transaksi->id }})">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>

            {{-- Modal Detail --}}
            <div class="modal fade" id="modalDetail{{ $transaksi->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                  <div class="modal-header" style="background:linear-gradient(135deg, rgba(22,163,74,.06), rgba(22,163,74,.02));">
                    <h5 class="modal-title"><i class="bi bi-receipt-cutoff me-2 text-success"></i>Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1px solid #f1f5f9;">
                          <div class="text-muted" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:4px;">Tim</div>
                          <div class="fw-bold" style="font-size:.90rem;">{{ $tim }}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1px solid #f1f5f9;">
                          <div class="text-muted" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:4px;">Tanggal</div>
                          <div class="fw-bold" style="font-size:.90rem;">{{ $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1px solid #f1f5f9;">
                          <div class="text-muted" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:4px;">Sub Kategori</div>
                          <div class="fw-bold" style="font-size:.90rem;">{{ $sub }}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1px solid #f1f5f9;">
                          <div class="text-muted" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:4px;">Metode</div>
                          <div class="fw-bold" style="font-size:.90rem;">{{ strtoupper($transaksi->metode_pembayaran ?? '-') }}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1px solid #f1f5f9;">
                          <div class="text-muted" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:4px;">Jumlah</div>
                          <div class="fw-bold" style="font-size:.90rem; color:var(--brand-700);">Rp {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1px solid #f1f5f9;">
                          <div class="text-muted" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:4px;">Jenis</div>
                          <div class="fw-bold" style="font-size:.90rem;">{{ ucfirst($transaksi->jenis_transaksi ?? '-') }}</div>
                        </div>
                      </div>
                      <div class="col-12">
                        <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;border:1px solid #f1f5f9;">
                          <div class="text-muted" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:4px;">Deskripsi</div>
                          <div class="fw-semibold" style="font-size:.90rem;line-height:1.55;">{{ $desk }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill fw-bold px-4" data-bs-dismiss="modal">
                      <i class="bi bi-x-lg me-1"></i> Tutup
                    </button>
                  </div>
                </div>
              </div>
            </div>

          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-5" style="font-weight:700;">Belum ada transaksi.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // SweetAlert Delete
  function confirmDelete(transaksiId) {
    Swal.fire({
      title: "Apakah Anda yakin?",
      text: "Data pencatatan keuangan akan dihapus permanen!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Hapus!",
      cancelButtonText: "Batal",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
    }).then((result) => {
      if (result.isConfirmed) {
        fetch(`/pencatatan_keuangan/${transaksiId}`, {
          method: "DELETE",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Content-Type": "application/json",
            "Accept": "application/json"
          }
        })
        .then(r => { if(!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(data => {
          if (data.success) {
            Swal.fire({ title:"Berhasil!", text:data.message, icon:"success", timer:1500, showConfirmButton:false });
            setTimeout(() => location.reload(), 1500);
          } else {
            Swal.fire({ title:"Gagal!", text:data.message || "Terjadi kesalahan", icon:"error" });
          }
        })
        .catch(err => Swal.fire({ title:"Gagal!", text:"Server error: " + err.message, icon:"error" }));
      }
    });
  }

  // Tabs filter + recompute KPI
  (function(){
    const rows = document.querySelectorAll('table tbody tr');
    const tabs = document.querySelectorAll('#filterTabs [data-filter]');
    const elIn  = document.getElementById('total-pemasukan');
    const elOut = document.getElementById('total-pengeluaran');

    const num = s => Number((s||'').replace(/[^0-9]/g,''));
    const rup = n => new Intl.NumberFormat('id-ID').format(n||0);

    function recompute(){
      let tIn=0, tOut=0;
      rows.forEach(tr=>{
        if (tr.style.display==='none') return;
        const jenis = (tr.dataset.jenis||'').toLowerCase();
        const cell = tr.querySelector('td:nth-child(5)');
        const val = num(cell ? cell.textContent : '');
        if (jenis==='pemasukan') tIn += val;
        else if (jenis==='pengeluaran') tOut += val;
      });
      elIn.textContent  = 'Rp ' + rup(tIn);
      elOut.textContent = 'Rp ' + rup(tOut);
    }

    function applyFilter(filter){
      tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.filter===filter));
      rows.forEach(tr=>{
        const j = (tr.dataset.jenis||'').toLowerCase();
        tr.style.display = (filter==='semua' || j===filter) ? '' : 'none';
      });
      recompute();
    }

    tabs.forEach(tab => tab.addEventListener('click', e=>{
      e.preventDefault();
      applyFilter(tab.dataset.filter);
    }));

    applyFilter('semua');
  })();

  // Filter tanggal: set max & min
  document.addEventListener("DOMContentLoaded", function () {
    const startDateInput = document.getElementById("startDate");
    const endDateInput = document.getElementById("endDate");

    const today = new Date().toISOString().split('T')[0];
    startDateInput?.setAttribute('max', today);
    endDateInput?.setAttribute('max', today);

    startDateInput?.addEventListener("change", function () {
      if (startDateInput.value) endDateInput.setAttribute('min', startDateInput.value);
    });
  });
</script>
@endpush
