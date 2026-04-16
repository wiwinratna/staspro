{{-- resources/views/laporan_keuangan.blade.php --}}
@extends('layouts.panel')

@section('title','Laporan Keuangan')

@push('styles')
<style>
  /* ─── shared design tokens (sama dgn pencatatan_keuangan) ─── */
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
  .hero-inner{ position:relative; z-index:2; width:100%; }
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
  .btn-excel{
    height:36px; display:inline-flex; align-items:center; gap:7px;
    border-radius:999px; font-weight:800; padding:0 14px; font-size:.84rem;
    background:#16a34a; border:0; color:#fff; text-decoration:none; white-space:nowrap;
    box-shadow:0 12px 24px rgba(22,163,74,.16);
    transition:transform .12s, box-shadow .12s;
  }
  .btn-excel:hover{ filter:brightness(.96); transform:translateY(-1px); color:#fff; }
  .btn-pdf{
    height:36px; display:inline-flex; align-items:center; gap:7px;
    border-radius:999px; font-weight:800; padding:0 14px; font-size:.84rem;
    background:#dc2626; border:0; color:#fff; text-decoration:none; white-space:nowrap;
    box-shadow:0 12px 24px rgba(220,38,38,.14);
    transition:transform .12s, box-shadow .12s;
  }
  .btn-pdf:hover{ filter:brightness(.96); transform:translateY(-1px); color:#fff; }

  .tools-row{ margin-top:14px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
  .tools-right{ margin-left:auto; display:flex; gap:8px; flex-wrap:wrap; }

  /* filter */
  .filter-card{
    background:var(--card); border:1px solid rgba(226,232,240,.95);
    border-radius:18px; padding:12px 16px; box-shadow:var(--shadow); margin-bottom:14px;
  }
  .filter-row{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .filter-label{ font-weight:800; font-size:.84rem; color:var(--ink); white-space:nowrap; }
  .select-sm, .date-sm{
    height:36px; border-radius:999px; padding:0 12px;
    border:1px solid rgba(226,232,240,.95); background:#fff;
    box-shadow:0 8px 20px rgba(15,23,42,.04);
    font-weight:700; font-size:.85rem; color:var(--ink);
  }
  .select-sm:focus, .date-sm:focus{
    border-color:rgba(22,163,74,.45); box-shadow:0 0 0 .2rem rgba(22,163,74,.1); outline:none;
  }
  .icon-btn{
    height:36px; width:36px; border-radius:999px;
    border:1px solid rgba(226,232,240,.95); background:#fff;
    display:inline-flex; align-items:center; justify-content:center;
    box-shadow:0 8px 20px rgba(15,23,42,.04); text-decoration:none; color:var(--ink);
    transition:transform .12s;
  }
  .icon-btn:hover{ background:#ecfdf5; color:#15803d; transform:translateY(-1px); }

  /* summary */
  .stats-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-bottom:14px; }
  .stat-card{
    background:var(--card); border:1px solid rgba(226,232,240,.95);
    border-radius:18px; padding:16px 18px; box-shadow:var(--shadow);
  }
  .stat-label{ font-size:.70rem; letter-spacing:.08em; text-transform:uppercase; font-weight:900; color:var(--ink-600); }
  .stat-value{ margin-top:4px; font-weight:900; font-size:1.3rem; }
  .tnum{ font-variant-numeric:tabular-nums; }

  /* table */
  .table-wrap{
    background:var(--card); border:1px solid rgba(226,232,240,.95);
    border-radius:18px; overflow:hidden; box-shadow:var(--shadow);
  }
  .table-responsive{ max-height:68vh; overflow-y:auto; overflow-x:auto; }
  .table-modern{
    margin:0; font-size:.88rem; border-collapse:separate; border-spacing:0;
    table-layout:fixed; width:100%; min-width:1000px;
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
  .table-modern tfoot th{
    background:#f0fdf4; padding:12px 14px;
    border-top:2px solid rgba(22,163,74,.15);
    font-size:.88rem;
  }

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

  @media(max-width:991.98px){
    .stats-grid{ grid-template-columns:1fr; }
    .hero{ padding:16px 18px; }
  }
</style>
@endpush

@section('content')

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <h1 class="title">Laporan Keuangan</h1>
      <p class="sub">Debit = uang masuk, Kredit = uang keluar (berdasarkan transaksi project).</p>

      <div class="tools-row">
        <div class="tools-right">
          <a class="btn-soft"
             href="https://drive.google.com/file/d/1HKaZH2I-Ohq7S-SBb8ADMHMd3htU0nio/view?usp=sharing"
             target="_blank" rel="noopener">
            <i class="bi bi-journal-bookmark"></i> Manual Book
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- SUMMARY -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label">Total Uang Masuk (Debit)</div>
      <div class="stat-value tnum">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Uang Keluar (Kredit)</div>
      <div class="stat-value tnum">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</div>
    </div>
  </div>

  <!-- FILTER -->
  <div class="filter-card">
    <div class="filter-row">
      <div class="filter-label">Filter</div>

      <form id="filter-form" method="GET" action="{{ route('laporan_keuangan') }}" class="d-flex align-items-center flex-wrap" style="gap:8px;">
        <select name="tim_peneliti" id="tim_peneliti" class="select-sm">
          <option value="">Semua Tim</option>
          @foreach ($projects as $project)
            <option value="{{ $project->id }}" {{ request('tim_peneliti') == $project->id ? 'selected' : '' }}>
              {{ $project->nama_project }}
            </option>
          @endforeach
        </select>

        <select name="metode_pembayaran" id="metode_pembayaran" class="select-sm">
          <option value="">Semua Metode</option>
          <option value="cash" {{ request('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
          <option value="transfer bank" {{ request('metode_pembayaran') == 'transfer bank' ? 'selected' : '' }}>Transfer Bank</option>
        </select>

        <select name="sumber_dana" id="sumber_dana" class="select-sm">
          <option value="">Semua Sumber</option>
          <option value="internal"  {{ request('sumber_dana') == 'internal' ? 'selected' : '' }}>Internal</option>
          <option value="eksternal" {{ request('sumber_dana') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
        </select>

        <input type="date" name="start_date" id="start_date" class="date-sm"
               value="{{ request('start_date') ?? '' }}" title="Tanggal mulai">
        <input type="date" name="end_date" id="end_date" class="date-sm"
               value="{{ request('end_date') ?? '' }}" title="Tanggal sampai">

        <a href="{{ route('laporan_keuangan') }}" class="icon-btn" title="Reset Filter">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      </form>

      <div class="ms-auto d-flex gap-2">
        <a class="btn-excel"
           href="{{ route('laporan.export', 'excel') }}?tim_peneliti={{ request('tim_peneliti') }}&metode_pembayaran={{ request('metode_pembayaran') }}&sumber_dana={{ request('sumber_dana') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}">
          <i class="bi bi-file-earmark-excel"></i> Excel
        </a>
        <a class="btn-pdf"
           href="{{ route('laporan.export', 'pdf') }}?tim_peneliti={{ request('tim_peneliti') }}&metode_pembayaran={{ request('metode_pembayaran') }}&sumber_dana={{ request('sumber_dana') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}">
          <i class="bi bi-file-earmark-pdf"></i> PDF
        </a>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table table-modern align-middle">
        <thead>
          <tr>
            <th style="width:50px" class="text-center">No</th>
            <th style="width:120px">Tanggal</th>
            <th style="width:160px">Tim</th>
            <th style="width:280px">Deskripsi</th>
            <th style="width:120px">Metode</th>
            <th style="width:100px" class="text-center">Sumber</th>
            <th style="width:140px" class="text-end">Debit</th>
            <th style="width:140px" class="text-end">Kredit</th>
          </tr>
        </thead>

        <tbody>
          @forelse($pencatatanKeuangans as $index => $row)
            @php
              $desk = $row->deskripsi_transaksi ?? '-';

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
            <tr>
              <td class="text-center">{{ $index + 1 }}</td>

              <td>
                <div class="cell-main" style="font-size:.88rem;">
                  {{ $row->created_at->timezone('Asia/Jakarta')->format('d-m-Y') }}
                </div>
                <div class="cell-muted">{{ $row->created_at->timezone('Asia/Jakarta')->format('H:i') }}</div>
              </td>

              <td>
                <div class="cell-ellipsis cell-main" title="{{ $row->project->nama_project ?? '-' }}">
                  {{ $row->project->nama_project ?? '-' }}
                </div>
              </td>

              <td>
                <div class="cell-clamp cell-main" title="{{ $desk }}">
                  {{ $deskClean ?: '-' }}
                </div>
              </td>

              <td>
                <div class="cell-ellipsis" style="font-weight:700; font-size:.84rem;">{{ strtoupper($row->metode_pembayaran ?? '-') }}</div>
              </td>

              <td class="text-center">
                <span style="font-weight:800; font-size:.78rem; padding:3px 9px; border-radius:999px;
                  {{ strtolower($row->project->sumberDana->jenis_pendanaan ?? '') === 'internal'
                    ? 'background:#eff6ff; color:#1d4ed8; border:1px solid rgba(59,130,246,.2);'
                    : 'background:#fdf4ff; color:#7c3aed; border:1px solid rgba(139,92,246,.2);' }}">
                  {{ strtoupper($row->project->sumberDana->jenis_pendanaan ?? '-') }}
                </span>
              </td>

              <td class="text-end tnum fw-bold" style="white-space:nowrap;">
                @if(($row->jenis_transaksi ?? '') === 'pemasukan')
                  <span style="color:#166534;">Rp {{ number_format($row->jumlah_transaksi ?? 0, 0, ',', '.') }}</span>
                @else
                  <span class="cell-muted">—</span>
                @endif
              </td>

              <td class="text-end tnum fw-bold" style="white-space:nowrap;">
                @if(($row->jenis_transaksi ?? '') === 'pengeluaran')
                  <span style="color:#991b1b;">Rp {{ number_format($row->jumlah_transaksi ?? 0, 0, ',', '.') }}</span>
                @else
                  <span class="cell-muted">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-5" style="font-weight:700;">
                Belum ada data laporan.
              </td>
            </tr>
          @endforelse
        </tbody>

        <tfoot>
          <tr>
            <th colspan="6" class="text-end" style="font-weight:900;">Total</th>
            <th class="text-end tnum" style="color:#166534;">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</th>
            <th class="text-end tnum" style="color:#991b1b;">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</th>
          </tr>
        </tfoot>

      </table>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  // auto submit filters + validasi range tanggal
  document.addEventListener('DOMContentLoaded', () => {
    const form  = document.getElementById('filter-form');
    const start = document.getElementById('start_date');
    const end   = document.getElementById('end_date');

    const today = new Date().toISOString().split('T')[0];
    start?.setAttribute('max', today);
    end?.setAttribute('max', today);

    ['tim_peneliti','metode_pembayaran','sumber_dana'].forEach(id=>{
      document.getElementById(id)?.addEventListener('change', ()=> form.submit());
    });

    start?.addEventListener('change', ()=>{
      if(start.value) end.setAttribute('min', start.value);
      if(start.value && end.value) form.submit();
    });

    end?.addEventListener('change', ()=>{
      if(start.value && end.value) form.submit();
    });
  });
</script>
@endpush
