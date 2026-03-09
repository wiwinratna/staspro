@extends('layouts.panel')

@section('title','Track Pengajuan Komponen')

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
    margin-bottom:14px;
  }
  .title{ font-size:1.65rem; font-weight:900; margin:0; letter-spacing:-.2px; }
  .sub{ margin:6px 0 0; color:#475569; font-weight:600; }
  .table-wrap{
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(15,23,42,.08);
  }
  #trackTable{ font-size:.86rem; }
  #trackTable thead th{
    font-weight:800;
    color:#0f172a;
    white-space:nowrap;
    vertical-align:middle;
  }
  #trackTable tbody td{
    vertical-align:middle;
    padding-top:.65rem;
    padding-bottom:.65rem;
  }
  .cell-project{
    font-weight:700;
    color:#1f2937;
    max-width:190px;
  }
  .cell-item{
    min-width:140px;
    font-weight:600;
    color:#111827;
  }
  .cell-subrequest{
    font-size:.72rem;
    color:#64748b;
    font-weight:600;
    margin-top:2px;
    white-space:nowrap;
  }
  .cell-num{ text-align:right; white-space:nowrap; font-variant-numeric: tabular-nums; }
  .cell-qty{ text-align:center; font-weight:700; }
  .badge-status{
    border-radius:999px;
    padding:.35rem .7rem;
    font-size:.78rem;
    font-weight:900;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:126px;
  }
  .s-submit-request{ background:#fff7ed; color:#9a3412; }
  .s-approve-request{ background:#eff6ff; color:#1e40af; }
  .s-reject-request{ background:#fef2f2; color:#991b1b; }
  .s-submit-payment{ background:#f5f3ff; color:#5b21b6; }
  .s-approve-payment{ background:#ecfdf5; color:#166534; }
  .s-reject-payment{ background:#fdf2f8; color:#9d174d; }
  .s-done{ background:#eefdfb; color:#115e59; }
  .search-wrap{
    height:38px;
    display:flex;
    align-items:center;
    gap:10px;
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:999px;
    padding:0 12px;
    width:420px;
    max-width:100%;
  }
  .search-input{
    width:100%;
    border:0;
    outline:0;
    font-weight:700;
    background:transparent;
  }
  .actions-wrap{
    display:flex;
    justify-content:flex-end;
    gap:6px;
    flex-wrap:wrap;
    min-width:220px;
  }
  .actions-wrap .btn{
    border-radius:10px;
    font-weight:700;
    white-space:nowrap;
  }
  .btn-arrived{
    background:#2563eb;
    border-color:#2563eb;
    color:#fff;
  }
  .btn-report{
    background:#f59e0b;
    border-color:#f59e0b;
    color:#111827;
  }
</style>
@endpush

@section('content')
<section class="hero">
  <h1 class="title">Track Pengajuan Komponen</h1>
  <p class="sub">Pantau item yang sudah disetujui: sudah sampai atau belum, dan status pelaporannya.</p>
  <div class="mt-3 search-wrap">
    <i class="bi bi-search"></i>
    <input id="searchTrack" class="search-input" placeholder="Cari project / barang / request / status">
  </div>
</section>

<section class="table-wrap">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="trackTable">
      <thead class="table-light">
        <tr>
          <th>Project</th>
          <th>Nama Barang</th>
          <th>Qty</th>
          <th>Harga Satuan</th>
          <th>Total Perkiraan</th>
          <th>Total Invoice</th>
          <th>Status Sampai</th>
          @if(auth()->user()->role !== 'peneliti')
            <th>Status Pelaporan</th>
          @endif
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tracks as $t)
          @php
            $sampai = (bool)($t->is_sampai ?? false);
            $pelaporan = (bool)($t->is_pelaporan ?? false);
            $statusSampai = $sampai ? 'Sudah Sampai' : 'Belum Sampai';
            $statusPelaporan = $pelaporan ? 'Sudah Pelaporan' : 'Belum Pelaporan';
            $search = strtolower(
              ($t->nama_project ?? '').' '.
              ($t->nama_barang ?? '').' '.
              $statusSampai.' '.$statusPelaporan
            );
          @endphp
          <tr data-search="{{ $search }}">
            <td class="cell-project">{{ $t->nama_project ?? '-' }}</td>
            <td>
              <div class="cell-item">{{ $t->nama_barang ?? '-' }}</div>
              <div class="cell-subrequest">{{ $t->no_request ?? '-' }}</div>
            </td>
            <td class="cell-qty">{{ (int)$t->kuantitas }}</td>
            <td class="cell-num">Rp {{ number_format((float)$t->harga, 0, ',', '.') }}</td>
            <td class="cell-num">Rp {{ number_format((float)$t->total_perkiraan, 0, ',', '.') }}</td>
            <td class="cell-num">Rp {{ number_format((float)$t->total_invoice, 0, ',', '.') }}</td>
            <td>
              <span class="badge-status {{ $sampai ? 's-done' : 's-submit-request' }}">{{ $statusSampai }}</span>
            </td>
            @if(auth()->user()->role !== 'peneliti')
              <td>
                <span class="badge-status {{ $pelaporan ? 's-done' : 's-reject-payment' }}">{{ $statusPelaporan }}</span>
              </td>
            @endif
            <td class="text-end">
              <div class="actions-wrap">
                <a href="{{ route('requestpembelian.detail', $t->id) }}" class="btn btn-sm btn-outline-success">Detail</a>
                @if(in_array(auth()->user()->role, ['admin','bendahara']))
                  @if(!$sampai)
                    <form action="{{ route('requestpembelian.track.sampai', $t->detail_id) }}" method="POST" class="d-inline">
                      @csrf
                      <button class="btn btn-sm btn-arrived" type="submit">Sudah Sampai</button>
                    </form>
                  @endif

                  @if($sampai && !$pelaporan)
                    <form action="{{ route('requestpembelian.track.pelaporan', $t->detail_id) }}" method="POST" class="d-inline">
                      @csrf
                      <button class="btn btn-sm btn-report" type="submit">Sudah Pelaporan</button>
                    </form>
                  @endif
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ auth()->user()->role === 'peneliti' ? 8 : 9 }}" class="text-center py-4 text-muted">Belum ada data track paket komponen.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection

@push('scripts')
<script>
  document.getElementById('searchTrack')?.addEventListener('input', function () {
    const q = (this.value || '').toLowerCase().trim();
    document.querySelectorAll('#trackTable tbody tr[data-search]').forEach((tr) => {
      const text = tr.getAttribute('data-search') || '';
      tr.style.display = text.includes(q) ? '' : 'none';
    });
  });
</script>
@endpush
