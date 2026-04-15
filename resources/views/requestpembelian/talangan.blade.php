@extends('layouts.panel')

@section('title', 'Rekonsiliasi Talangan')

@section('content')
<div class="card card-soft mb-4 border-0 shadow-sm" style="border-radius:18px;">
  <div class="card-body p-4">
    <h4 class="mb-1 fw-bold text-dark"><i class="bi bi-arrow-left-right text-warning me-2"></i>Rekonsiliasi Talangan</h4>
    <p class="text-muted mb-0 fw-medium" style="font-size:0.9rem;">Alokasikan pembelian talangan yang sudah selesai ke project tujuan final.</p>
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

<div class="card border-0 shadow-sm" style="border-radius:22px; overflow:hidden;">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table mb-0 align-middle table-hover" style="font-size:0.9rem;">
        <thead style="background:#f8fafc; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:#475569; border-bottom:1px solid #e2e8f0;">
          <tr>
            <th class="py-3 px-3">No Request</th>
            <th class="py-3">Project Awal</th>
            <th class="py-3">Total Invoice</th>
            <th class="py-3">Biaya Admin</th>
            <th class="py-3 text-end">Total Talangan</th>
            <th class="py-3 text-center">Status Alokasi</th>
            <th class="py-3 px-3">Aksi</th>
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
              <td class="px-3">
                <div class="fw-bold">{{ $r->no_request }}</div>
                <div class="small text-muted">{{ \Carbon\Carbon::parse($r->tgl_request)->format('d/m/Y') }}</div>
              </td>
              <td><span class="fw-medium text-dark">{{ $r->nama_project_awal ?? '-' }}</span></td>
              <td>Rp {{ number_format($totalInvoice, 0, ',', '.') }}</td>
              <td>Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</td>
              <td class="fw-bold text-end">Rp {{ number_format($totalTalangan, 0, ',', '.') }}</td>
              <td class="text-center">
                @if($isDoneAlloc)
                  <span class="badge rounded-pill" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0; font-size:0.75rem; font-weight:700;"><i class="bi bi-check-circle me-1"></i>Sudah</span>
                  <div class="mt-2 text-start p-2 rounded-2" style="background:#f8fafc; font-size:0.75rem; border:1px solid #e2e8f0;">
                    <div class="fw-bold text-dark"><i class="bi bi-arrow-right-short text-muted"></i> {{ $r->nama_project_alokasi ?? '-' }}</div>
                    <div class="text-muted"><i class="bi bi-calendar3 ms-1 me-1 text-muted"></i> {{ $r->tanggal_alokasi_final ? \Carbon\Carbon::parse($r->tanggal_alokasi_final)->format('dM Y') : '-' }}</div>
                  </div>
                @else
                  <span class="badge rounded-pill" style="background:#ffedd5; color:#9a3412; border:1px solid #fed7aa; font-size:0.75rem; font-weight:700;"><i class="bi bi-clock-history me-1"></i>Belum</span>
                @endif
              </td>
              <td style="min-width:340px" class="px-3">
                @if(!$isDoneAlloc)
                  <form method="POST" action="{{ route('requestpembelian.talangan.allocate', $r->id) }}" class="row g-2 p-2 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    @csrf
                    <div class="col-12">
                      <select name="project_id_alokasi_final" class="form-select form-select-sm border-0 shadow-sm rounded-2" required>
                        <option value="">-- Pilih Project Tujuan --</option>
                        @foreach($projects as $p)
                          <option value="{{ $p->id }}">{{ $p->nama_project }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-5">
                      <input type="date" name="tanggal_alokasi_final" class="form-control form-control-sm border-0 shadow-sm rounded-2" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-7">
                      <input type="text" name="catatan_alokasi" class="form-control form-control-sm border-0 shadow-sm rounded-2" placeholder="Catatan (Opsional)">
                    </div>
                    <div class="col-12 text-end mt-2">
                      <button type="submit" class="btn btn-sm btn-brand shadow-sm rounded-pill fw-bold" style="background:var(--brand); color:#fff; border:none; padding:4px 16px;">
                        <i class="bi bi-save me-1"></i> Alokasikan
                      </button>
                    </div>
                  </form>
                @else
                  <div class="small fw-medium text-muted fst-italic"><i class="bi bi-chat-left-text me-1"></i> {{ $r->catatan_alokasi ?: 'Tidak ada catatan.' }}</div>
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

