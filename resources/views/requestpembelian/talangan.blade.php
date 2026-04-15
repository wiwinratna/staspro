@extends('layouts.panel')

@section('title', 'Rekonsiliasi Talangan')

@section('content')
<style>
  :root {
    --brand: #16a34a; /* Emerald 600 */
    --brand-700: #15803d; /* Emerald 700 */
    --brand-100: #d1fae5;
    --card: #ffffff;
    --ink: #0f172a;
    --ink-600: #475569;
    --bg-main: #f8fafc;
    --shadow: 0 10px 24px -10px rgba(15,23,42,.08);
  }
  
  .hero {
    border-radius: 22px;
    padding: 18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border: 1px solid rgba(226,232,240,.95);
    box-shadow: 0 10px 30px rgba(15,23,42,.08);
    margin-bottom: 14px;
  }
  .hero .title { font-size: 1.65rem; font-weight: 900; margin: 0; letter-spacing: -.2px; color: #0f172a; }
  .hero .sub { margin: 6px 0 0; color: #475569; font-weight: 600; font-size: 1rem; }
  
  .table-wrap {
    background: var(--card);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 22px;
    overflow: hidden;
    margin-top: 14px;
    box-shadow: var(--shadow);
  }
  .table-responsive { max-height: 68vh; overflow-y: auto; overflow-x: auto; }
  .table-modern { margin: 0; font-size: .92rem; table-layout: auto; width: 100%; border-collapse: separate; border-spacing: 0; min-width: 1000px; }
  .table-modern thead th {
    background: #f8fafc; color: var(--ink-600);
    font-weight: 800; text-transform: uppercase; font-size: .7rem; letter-spacing: .05em;
    padding: 10px 14px; border-bottom: 1px solid rgba(226,232,240,.95);
    position: sticky; top: 0; z-index: 5; white-space: nowrap;
  }
  .table-modern tbody td { padding: 9px 14px; vertical-align: middle; border-top: 1px solid #eef2f7; overflow: visible; font-size: .86rem; }
  
  .tnum { font-variant-numeric: tabular-nums; }
  
  .badge-pill {
    display: inline-flex; align-items: center; gap: 4px;
    border-radius: 999px; padding: 4px 10px;
    font-weight: 800; font-size: .7rem;
    border: 1px solid rgba(226,232,240,.95);
    background: #fff;
    white-space: nowrap;
  }
  .badge-sudah { background: #f0fdf4; color: #166534; border-color: rgba(22,163,74,.25); }
  .badge-belum { background: #fff7ed; color: #9a3412; border-color: rgba(245,158,11,.25); }

  .btn-act {
    width: 32px; height: 32px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .btn-alloc {
    background: #f0fdf4; color: #15803d; border-color: rgba(22,163,74,.25);
  }
  .btn-alloc:hover {
    background: #dcfce7;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(22,163,74,.15);
  }
  .btn-detail {
    background: #eff6ff; color: #1d4ed8; border-color: rgba(59,130,246,.25);
  }
  .btn-detail:hover {
    background: #dbeafe;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(59,130,246,.15);
  }
</style>

<div class="px-2">
  <section class="hero">
    <h1 class="title">Rekonsiliasi Talangan</h1>
    <p class="sub">Alokasikan dana talangan transaksi yang sudah selesai ke project final.</p>
  </section>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center mb-3" style="border-radius: 12px; border:none; box-shadow:0 4px 12px rgba(22,163,74,.1);">
      <i class="bi bi-check-circle-fill me-2 fs-5"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger mb-3" style="border-radius: 12px; border:none; box-shadow:0 4px 12px rgba(239,68,68,.1);">
      <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger mb-3" style="border-radius: 12px;">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Item Barang</th>
            <th>Project Awal</th>
            <th class="text-end">Total Invoice</th>
            <th class="text-end">Biaya Admin</th>
            <th class="text-end">Total Talangan</th>
            <th class="text-center">Status Alokasi</th>
            <th class="text-center">Aksi</th>
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
                <div class="text-truncate" style="font-weight:900; color:var(--ink); max-width: 250px;" title="{{ $r->nama_barang ?? '-' }}">
                  {{ $r->nama_barang ? Str::limit($r->nama_barang, 40) : '-' }}
                </div>
              </td>
              <td style="font-weight:800; color:var(--ink-600);">
                {{ $r->nama_project_awal ?? '-' }}
              </td>
              <td class="text-end tnum" style="font-weight:700; color:var(--ink-600);">Rp {{ number_format($totalInvoice, 0, ',', '.') }}</td>
              <td class="text-end tnum" style="font-weight:700; color:var(--ink-600);">Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</td>
              <td class="text-end tnum" style="font-weight:900; color:var(--brand-700);">Rp {{ number_format($totalTalangan, 0, ',', '.') }}</td>
              <td align="center" style="max-width: 180px;">
                @if($isDoneAlloc)
                  <div class="badge-pill badge-sudah"><i class="bi bi-check-circle-fill"></i> Selesai</div>
                  <div style="margin-top: 6px; font-size: 0.8rem; line-height: 1.4; color: var(--ink-600);">
                    ke <strong style="color:var(--ink);">{{ $r->nama_project_alokasi ?? '-' }}</strong>
                  </div>
                @else
                  <div class="badge-pill badge-belum"><i class="bi bi-hourglass-split"></i> Belum Dialokasikan</div>
                @endif
              </td>
              <td align="center" style="width: 100px;">
                <div class="d-flex justify-content-center gap-1">
                  <a href="{{ ($r->source ?? 'request_pembelian') === 'pengajuan_transaksi' ? route('pengajuan_transaksi.show', $r->id) : route('requestpembelian.detail', $r->id) }}" 
                     class="btn-act btn-detail" title="Lihat Detail Transaksi">
                    <i class="bi bi-info-circle fs-6"></i>
                  </a>
                  @if(!$isDoneAlloc)
                    <button type="button" class="btn-act btn-alloc" 
                            data-bs-toggle="modal" 
                            data-bs-target="#allocModal"
                            data-id="{{ $r->id }}"
                            data-source="{{ $r->source ?? 'request_pembelian' }}"
                            data-nomer="{{ $r->no_request }}"
                            data-total="Rp {{ number_format($totalTalangan, 0, ',', '.') }}"
                            title="Alokasikan Mutasi">
                      <i class="bi bi-box-arrow-in-right fs-6"></i>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5" style="color:var(--ink-600);">
                <i class="bi bi-inbox fs-1 mb-2 d-block" style="color:#cbd5e1;"></i>
                <div style="font-weight:800;">Belum ada transaksi talangan yang menunggu alokasi.</div>
                <div style="font-size:0.85rem;">Transaksi talangan akan muncul di sini saat diproses selesai.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Allocation -->
<div class="modal fade" id="allocModal" tabindex="-1" aria-labelledby="allocModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius:24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
      <div class="modal-header border-0 bg-light" style="border-radius:24px 24px 0 0; padding:20px 24px;">
        <h5 class="modal-title fw-bold text-dark" id="allocModalLabel">
          <i class="bi bi-arrow-left-right text-success me-2"></i> Mutasi Talangan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="allocForm" method="POST" action="">
          @csrf
          <input type="hidden" name="source" id="allocSource" value="">

          <div class="mb-4 text-center p-3 rounded-4" style="background:#f0fdf4; border:1px solid #bbf7d0;">
            <div style="font-size:0.85rem; color:#166534; font-weight:700; text-transform:uppercase;">No. Request</div>
            <div id="allocReq" style="font-weight:900; font-size:1.15rem; color:#0f172a; margin-top:2px;">-</div>
            <div class="mt-2" style="font-size:0.85rem; color:#166534; font-weight:700;">Total Talangan: <span id="allocTotal" class="fw-bold fs-6 tnum d-block">-</span></div>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:800; color:var(--ink-600); font-size:0.85rem;">Pilih Project Tujuan Final <span class="text-danger">*</span></label>
            <select name="project_id_alokasi_final" class="form-select form-select-lg" style="border-radius:12px; font-size:0.95rem; font-weight:600;" required>
              <option value="">-- Pilih Project Tujuan --</option>
              @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->nama_project }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:800; color:var(--ink-600); font-size:0.85rem;">Tanggal Mutasi / Alokasi <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_alokasi_final" class="form-control form-control-lg" style="border-radius:12px; font-size:0.95rem;" value="{{ now()->toDateString() }}" required>
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-weight:800; color:var(--ink-600); font-size:0.85rem;">Catatan Alokasi (Opsional)</label>
            <textarea name="catatan_alokasi" class="form-control" rows="2" style="border-radius:12px; font-size:0.95rem; resize:none;" placeholder="Tambahkan keterangan pendukung..."></textarea>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-success btn-lg fw-bold" style="border-radius:14px; background:var(--brand); border:none; box-shadow:0 8px 16px rgba(22,163,74,.2);">
              <i class="bi bi-save me-2"></i> Simpan Mutasi Alokasi
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const allocModal = document.getElementById('allocModal');
    if (allocModal) {
      allocModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const source = button.getAttribute('data-source');
        const reqStr = button.getAttribute('data-nomer');
        const total = button.getAttribute('data-total');

        const form = document.getElementById('allocForm');
        // Set dynamic action URL
        form.action = `/requestpembelian/talangan/${id}/alokasi`;
        
        document.getElementById('allocSource').value = source;
        document.getElementById('allocReq').textContent = reqStr;
        document.getElementById('allocTotal').textContent = total;
      });
    }
  });
</script>
@endsection

