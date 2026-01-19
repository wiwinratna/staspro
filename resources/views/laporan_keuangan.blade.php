{{-- resources/views/laporan_keuangan.blade.php --}}
@extends('layouts.panel')

@section('title','Laporan Keuangan')

@push('styles')
<style>
  /* =========================
     PAGE-ONLY STYLES (Laporan)
     ========================= */

  /* HERO */
  .hero{
    border-radius:22px; padding:18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 10px 30px rgba(15,23,42,.08);
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
  .hero-left .title{ font-size:1.65rem; font-weight:900; margin:0; letter-spacing:-.2px; }
  .hero-left .sub{ margin:6px 0 0; color:#475569; font-weight:600; }

  .tools-row{
    margin-top:14px;
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; flex-wrap:wrap;
  }
  .tools-left{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .tools-right{ margin-left:auto; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }

  /* Buttons */
  .btn-soft{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:900; padding:0 14px;
    background:#fff; color:#0f172a;
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    white-space:nowrap; text-decoration:none;
  }
  .btn-soft:hover{ background:#ecfdf5; transform:translateY(-1px); color:#15803d; }

  .btn-excel{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:900; padding:0 14px;
    background:#16a34a; border:0; color:#fff;
    box-shadow:0 14px 26px rgba(22,163,74,.18);
    text-decoration:none; white-space:nowrap;
  }
  .btn-excel:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }

  .btn-pdf{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:900; padding:0 14px;
    background:#dc2626; border:0; color:#fff;
    box-shadow:0 14px 26px rgba(220,38,38,.14);
    text-decoration:none; white-space:nowrap;
  }
  .btn-pdf:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }

  .btn-manual{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:900; padding:0 14px;
    background:#fff; color:#0f172a;
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    white-space:nowrap; text-decoration:none;
  }
  .btn-manual:hover{ background:#ecfdf5; color:#15803d; transform:translateY(-1px); }

  /* Filter */
  .filter-wrap{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .filter-label{
    font-size:.72rem; letter-spacing:.08em; text-transform:uppercase;
    font-weight:900; color:#475569; margin-right:4px; white-space:nowrap;
  }
  .select-sm, .date-sm{
    height:38px; border-radius:999px; padding:0 12px;
    border:1px solid rgba(226,232,240,.95);
    background:#fff; box-shadow:0 10px 26px rgba(15,23,42,.05);
    font-weight:800; font-size:.9rem; color:#0f172a;
  }
  .select-sm:focus, .date-sm:focus{
    border-color:rgba(22,163,74,.45);
    box-shadow:0 0 0 .2rem rgba(22,163,74,.12);
  }
  .btn-reset{
    height:38px; width:38px; display:inline-flex; align-items:center; justify-content:center;
    border-radius:999px; border:1px solid rgba(226,232,240,.95);
    background:#fff; box-shadow:0 10px 26px rgba(15,23,42,.05);
    color:#475569; text-decoration:none;
  }
  .btn-reset:hover{ background:#ecfdf5; color:#15803d; transform:translateY(-1px); }

  /* Summary */
  .stats-grid{
    display:grid; grid-template-columns: repeat(2, 1fr);
    gap:14px; margin-top:12px;
  }
  .stat-card{
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    padding:14px;
    box-shadow:0 10px 30px rgba(15,23,42,.08);
  }
  .stat-label{
    font-size:.72rem; letter-spacing:.08em; text-transform:uppercase;
    font-weight:900; color:#475569;
  }
  .stat-value{ margin-top:6px; font-weight:900; font-size:1.35rem; }
  .tnum{ font-variant-numeric: tabular-nums; }

  /* Table */
  .table-wrap{
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    overflow:hidden;
    margin-top:14px;
    box-shadow:0 10px 30px rgba(15,23,42,.08);
  }
  .table-responsive{ max-height:68vh; overflow-y:auto; }
  .table-modern{
    margin:0; font-size:.92rem;
    border-collapse:separate; border-spacing:0;
    table-layout:fixed; width:100%;
  }
  .table-modern thead th{
    background:#f8fafc; color:#475569;
    font-weight:900; text-transform:uppercase;
    font-size:.72rem; letter-spacing:.08em;
    padding:14px 12px;
    border-bottom:1px solid rgba(226,232,240,.95);
    position:sticky; top:0; z-index:5;
    white-space:nowrap;
  }
  .table-modern tbody td{
    padding:14px 12px;
    vertical-align:middle;
    border-top:1px solid #eef2f7;
    font-weight:700;
    overflow:hidden;
  }
  .table-striped > tbody > tr:nth-of-type(odd){ background:#fcfcfd; }
  .table-modern tbody tr:hover{ background:#ecfdf5; transition:.12s; }

  .td-left{ text-align:left; }
  .td-center{ text-align:center; }
  .td-right{ text-align:right; }
  .ellipsis{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

  @media(max-width:991px){
    .stats-grid{ grid-template-columns:1fr; }
    .select-sm, .date-sm{ width:100%; }
  }
</style>
@endpush

@section('content')

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-left">
        <h1 class="title">Laporan Keuangan</h1>
        <p class="sub">Debit = uang masuk, Kredit = uang keluar (berdasarkan transaksi project).</p>
      </div>

      <div class="tools-row">
        <form id="filter-form" method="GET" action="{{ route('laporan_keuangan') }}" class="tools-left">
          <div class="filter-wrap">
            <span class="filter-label">Filter</span>

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

            <a href="{{ route('laporan_keuangan') }}" class="btn-reset" title="Reset Filter">
              <i class="bi bi-arrow-counterclockwise"></i>
            </a>
          </div>
        </form>

        <div class="tools-right">
          <a class="btn-manual"
             href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
             target="_blank" rel="noopener">
            <i class="bi bi-book"></i> Manual Book
          </a>

          <a class="btn-excel"
             href="{{ route('laporan.export', 'excel') }}?tim_peneliti={{ request('tim_peneliti') }}&metode_pembayaran={{ request('metode_pembayaran') }}&sumber_dana={{ request('sumber_dana') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}">
            <i class="bi bi-file-earmark-excel"></i> Excel
          </a>

          <a class="btn-pdf"
             href="{{ route('laporan.export', 'pdf') }}?tim_peneliti={{ request('tim_peneliti') }}&metode_pembayaran={{ request('metode_pembayaran') }}&sumber_dana={{ request('sumber_dana') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}">
            <i class="bi bi-file-earmark-pdf"></i> PDF
          </a>

          <a class="btn-soft" href="{{ route('laporan_keuangan') }}">
            <i class="bi bi-arrow-repeat"></i> Refresh
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

  <!-- TABLE -->
  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table table-modern table-striped align-middle">
        <thead>
          <tr>
            <th style="width:64px" class="td-center">No</th>
            <th style="width:150px" class="td-center">Tanggal</th>
            <th style="width:190px" class="td-center">Tim</th>
            <th style="width:340px" class="td-left">Deskripsi</th>
            <th style="width:150px" class="td-center">Metode</th>
            <th style="width:140px" class="td-center">Sumber</th>
            <th style="width:160px" class="td-right">Debit</th>
            <th style="width:160px" class="td-right">Kredit</th>
          </tr>
        </thead>

        <tbody>
          @forelse($pencatatanKeuangans as $index => $row)
            <tr>
              <td class="td-center">{{ $index + 1 }}</td>
              <td class="td-center">{{ $row->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>

              <td class="td-center ellipsis" title="{{ $row->project->nama_project ?? '-' }}">
                {{ $row->project->nama_project ?? '-' }}
              </td>

              <td class="td-left ellipsis" title="{{ $row->deskripsi_transaksi }}">
                {{ $row->deskripsi_transaksi }}
              </td>

              <td class="td-center">{{ strtoupper($row->metode_pembayaran ?? '-') }}</td>
              <td class="td-center">{{ strtoupper($row->project->sumberDana->jenis_pendanaan ?? '-') }}</td>

              <td class="td-right tnum">
                @if(($row->jenis_transaksi ?? '') === 'pemasukan')
                  Rp {{ number_format($row->jumlah_transaksi ?? 0, 0, ',', '.') }}
                @else
                  -
                @endif
              </td>

              <td class="td-right tnum">
                @if(($row->jenis_transaksi ?? '') === 'pengeluaran')
                  Rp {{ number_format($row->jumlah_transaksi ?? 0, 0, ',', '.') }}
                @else
                  -
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="td-center text-secondary py-4" style="font-weight:900;">
                Belum ada data laporan.
              </td>
            </tr>
          @endforelse
        </tbody>

        <tfoot>
          <tr>
            <th colspan="6" class="td-right">Total</th>
            <th class="td-right tnum">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</th>
            <th class="td-right tnum">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</th>
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
