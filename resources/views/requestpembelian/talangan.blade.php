@extends('layouts.panel')

@section('title', 'Rekonsiliasi Talangan')

@section('content')
<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <h4 class="mb-1">Rekonsiliasi Talangan</h4>
    <p class="text-muted mb-0">Alokasikan pembelian talangan yang sudah selesai ke project tujuan final.</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>No Request</th>
            <th>Project Awal</th>
            <th>Total Invoice</th>
            <th>Biaya Admin</th>
            <th>Total Talangan</th>
            <th>Status Alokasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            @php
              $totalInvoice = (float)($r->total_invoice ?? 0);
              $biayaAdmin = (float)($r->biaya_admin_transfer ?? 0);
              $totalTalangan = $totalInvoice + $biayaAdmin;
              $isDoneAlloc = (($r->status_alokasi ?? 'belum') === 'sudah');
            @endphp
            <tr>
              <td>
                <div class="fw-bold">{{ $r->no_request }}</div>
                <div class="small text-muted">{{ \Carbon\Carbon::parse($r->tgl_request)->format('d/m/Y') }}</div>
              </td>
              <td>{{ $r->nama_project_awal ?? '-' }}</td>
              <td>Rp {{ number_format($totalInvoice, 0, ',', '.') }}</td>
              <td>Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</td>
              <td class="fw-bold">Rp {{ number_format($totalTalangan, 0, ',', '.') }}</td>
              <td>
                @if($isDoneAlloc)
                  <span class="badge text-bg-success">Sudah</span>
                  <div class="small text-muted mt-1">
                    Ke: {{ $r->nama_project_alokasi ?? '-' }}<br>
                    Tgl: {{ $r->tanggal_alokasi_final ? \Carbon\Carbon::parse($r->tanggal_alokasi_final)->format('d/m/Y') : '-' }}
                  </div>
                @else
                  <span class="badge text-bg-warning">Belum</span>
                @endif
              </td>
              <td style="min-width:340px">
                @if(!$isDoneAlloc)
                  <form method="POST" action="{{ route('requestpembelian.talangan.allocate', $r->id) }}" class="row g-2">
                    @csrf
                    <div class="col-12">
                      <select name="project_id_alokasi_final" class="form-select form-select-sm" required>
                        <option value="">Pilih project tujuan</option>
                        @foreach($projects as $p)
                          <option value="{{ $p->id }}">{{ $p->nama_project }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-5">
                      <input type="date" name="tanggal_alokasi_final" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-7">
                      <input type="text" name="catatan_alokasi" class="form-control form-control-sm" placeholder="Catatan (opsional)">
                    </div>
                    <div class="col-12">
                      <button type="submit" class="btn btn-sm btn-primary">
                        Alokasikan Final
                      </button>
                    </div>
                  </form>
                @else
                  <div class="small text-muted">{{ $r->catatan_alokasi ?: '-' }}</div>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi talangan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

