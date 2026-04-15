{{-- resources/views/transaksi/pengajuan_transaksi/detail_pengajuan.blade.php --}}
@extends('layouts.panel')

@section('title', 'Detail Pengajuan Dana')

@push('styles')
<style>
  .detail-hero{
    border-radius:22px; padding:24px 28px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow: var(--shadow);
    position:relative; overflow:hidden; margin-bottom:18px;
  }
  .detail-hero::after{ content:""; position:absolute; inset:-1px;
    background:
      radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
      radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
    pointer-events:none; opacity:.65;
  }
  .detail-hero-inner{ position:relative; z-index:2; }

  .detail-title{ font-size:1.55rem; font-weight:800; margin:0; letter-spacing:-.2px; }
  .detail-meta{
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    margin-top:8px;
  }
  .detail-request-no{
    font-family:'Inter',monospace; font-weight:800; font-size:.88rem;
    color:var(--ink-600); font-variant-numeric:tabular-nums;
    background:#f8fafc; padding:5px 12px; border-radius:8px;
    border:1px solid #e2e8f0;
  }

  .btn-brand{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:800; padding:0 16px;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    border:0; color:#fff; text-decoration:none; white-space:nowrap;
    box-shadow:0 12px 24px rgba(22,163,74,.16);
    transition:transform .12s, box-shadow .12s;
  }
  .btn-brand:hover{ transform:translateY(-1px); box-shadow:0 16px 32px rgba(22,163,74,.22); color:#fff; }
  .btn-soft{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:800; padding:0 16px;
    background:#fff; color:var(--ink); text-decoration:none; white-space:nowrap;
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 8px 20px rgba(15,23,42,.04);
    transition:transform .12s, box-shadow .12s;
  }
  .btn-soft:hover{ transform:translateY(-1px); box-shadow:0 12px 28px rgba(15,23,42,.08); color:var(--ink); }

  /* Status Badges */
  .status-pill{
    display:inline-flex; align-items:center; gap:6px;
    border-radius:999px; padding:5px 12px;
    font-weight:900; font-size:.74rem;
    white-space:nowrap; letter-spacing:.02em;
  }
  .status-submit{ background:#eff6ff; color:#1d4ed8; border:1px solid rgba(59,130,246,.2); }
  .status-approve{ background:#ecfeff; color:#155e75; border:1px solid rgba(6,182,212,.2); }
  .status-bukti{ background:#fff7ed; color:#9a3412; border:1px solid rgba(245,158,11,.2); }
  .status-done{ background:#f0fdf4; color:#166534; border:1px solid rgba(22,163,74,.2); }
  .status-reject{ background:#fef2f2; color:#991b1b; border:1px solid rgba(239,68,68,.2); }

  /* Section Cards */
  .detail-section{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:18px;
    box-shadow:0 8px 24px rgba(15,23,42,.05);
    overflow:hidden;
  }
  .section-header{
    padding:16px 22px 12px;
    border-bottom:1px solid #f1f5f9;
    display:flex; align-items:center; gap:10px;
  }
  .section-header-icon{
    width:34px; height:34px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:.95rem; flex-shrink:0;
  }
  .section-header-icon.primary{ background:rgba(22,163,74,.1); color:#16a34a; }
  .section-header-icon.info{ background:rgba(59,130,246,.1); color:#2563eb; }
  .section-header-icon.amber{ background:rgba(245,158,11,.1); color:#d97706; }
  .section-header-icon.purple{ background:rgba(139,92,246,.1); color:#7c3aed; }
  .section-header h6{
    margin:0; font-weight:800; font-size:.88rem; color:var(--ink);
  }
  .section-body{ padding:18px 22px 22px; }

  /* Data Fields */
  .data-field{
    background:#f8fafc;
    border:1px solid #f1f5f9;
    border-radius:12px;
    padding:14px 16px;
    height:100%;
  }
  .data-field .field-label{
    font-weight:800; font-size:.70rem; letter-spacing:.06em;
    text-transform:uppercase; color:#94a3b8;
    margin-bottom:5px;
  }
  .data-field .field-value{
    font-weight:700; font-size:.92rem; color:var(--ink);
    word-break:break-word;
  }
  .data-field .field-value.mono{
    font-variant-numeric:tabular-nums;
    font-family:'Inter',monospace;
  }
  .data-field .field-value.empty{
    color:#cbd5e1; font-style:italic; font-weight:600;
  }
  .data-field .field-value.highlight{
    color:var(--brand-700); font-size:1.05rem; font-weight:900;
  }
  .data-field .field-value.danger-text{
    color:#dc2626;
  }

  /* Form Fields */
  .field-input{
    height:38px; border-radius:12px;
    border:1px solid rgba(226,232,240,.95);
    padding:0 12px; font-weight:700; background:#fff;
    font-size:.88rem;
  }
  textarea.field-input{ height:auto; border-radius:12px; padding:10px 12px; }

  @media(max-width:991.98px){
    .detail-hero{ padding:18px 20px; }
    .section-body{ padding:14px 16px 18px; }
  }
</style>
@endpush

@section('content')

  @php
    $status = strtolower($trx->status ?? 'submit');
    $statusClass = match($status){
      'submit'  => 'status-submit',
      'approve' => 'status-approve',
      'bukti'   => 'status-bukti',
      'done'    => 'status-done',
      'reject'  => 'status-reject',
      default   => 'status-submit',
    };
    $statusLabel = match($status){
      'submit'  => 'DIAJUKAN',
      'approve' => 'DISETUJUI',
      'bukti'   => 'BUKTI DIUPLOAD',
      'done'    => 'SELESAI',
      'reject'  => 'DITOLAK',
      default   => strtoupper($status),
    };
    $statusIcon = match($status){
      'done'   => 'bi-check-circle-fill',
      'reject' => 'bi-x-circle-fill',
      'bukti'  => 'bi-file-earmark-check-fill',
      default  => 'bi-clock-history',
    };

    $tim = $trx->project->nama_project ?? 'Tidak Ada';
    $sub = $trx->subKategoriSumberDana->nama ?? '-';

    $role = strtolower(auth()->user()->role ?? auth()->user()->level ?? '');
    $isApprover = in_array($role, ['admin','bendahara','superadmin']);
  @endphp

  {{-- HERO HEADER --}}
  <section class="detail-hero">
    <div class="detail-hero-inner">
      <h1 class="detail-title">Detail Pengajuan Dana</h1>
      <div class="detail-meta">
        <span class="detail-request-no">{{ $trx->no_request }}</span>
        <span class="status-pill {{ $statusClass }}">
          <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
        </span>
      </div>

      <div class="d-flex gap-2 flex-wrap mt-3">
        <a href="{{ route('pengajuan_transaksi.index') }}" class="btn-soft">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
        @if($trx->status === 'done' && $trx->pencatatan_keuangan_id)
          <a href="{{ route('pencatatan_keuangan') }}" class="btn-brand">
            <i class="bi bi-journal-check"></i> Lihat di Pencatatan Keuangan
          </a>
        @endif
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success mt-2" style="border-radius:14px;">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger mt-2" style="border-radius:14px;">{{ session('error') }}</div>
  @endif

  <div class="row g-3">
    {{-- LEFT COLUMN --}}
    <div class="col-lg-7">

      {{-- Informasi Utama --}}
      <div class="detail-section mb-3">
        <div class="section-header">
          <div class="section-header-icon primary"><i class="bi bi-file-earmark-text"></i></div>
          <h6>Informasi Pengajuan</h6>
        </div>
        <div class="section-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="data-field">
                <div class="field-label">Project (Tim)</div>
                <div class="field-value">{{ $tim }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="data-field">
                <div class="field-label">Sub Kategori</div>
                <div class="field-value">{{ $sub }}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="data-field">
                <div class="field-label">Tanggal Request</div>
                <div class="field-value mono">{{ $trx->tgl_request ? \Carbon\Carbon::parse($trx->tgl_request)->format('d-m-Y') : '-' }}</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="data-field">
                <div class="field-label">Kuantitas</div>
                <div class="field-value mono">{{ $trx->kuantitas ?? '-' }}</div>
              </div>
            </div>
            <div class="col-md-5">
              <div class="data-field">
                <div class="field-label">Harga Satuan</div>
                <div class="field-value mono">{{ $trx->harga_satuan ? 'Rp '.number_format($trx->harga_satuan, 0, ',', '.') : '-' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="data-field">
                <div class="field-label">Subtotal (Estimasi)</div>
                <div class="field-value mono highlight">Rp {{ number_format($trx->estimasi_nominal ?? 0, 0, ',', '.') }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="data-field">
                <div class="field-label">Nominal Final (Admin)</div>
                <div class="field-value mono {{ is_null($trx->nominal_final) ? 'empty' : 'highlight' }}">{{ is_null($trx->nominal_final) ? 'Menunggu persetujuan' : 'Rp '.number_format($trx->nominal_final, 0, ',', '.') }}</div>
              </div>
            </div>
            <div class="col-12">
              <div class="data-field">
                <div class="field-label">Deskripsi / Keterangan Pembelian</div>
                <div class="field-value {{ empty($trx->deskripsi) ? 'empty' : '' }}">{{ $trx->deskripsi ?? 'Tidak ada deskripsi' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Info Rekening --}}
      <div class="detail-section mb-3">
        <div class="section-header">
          <div class="section-header-icon info"><i class="bi bi-bank"></i></div>
          <h6>Informasi Rekening</h6>
        </div>
        <div class="section-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="data-field">
                <div class="field-label">Nama Bank</div>
                <div class="field-value {{ empty($trx->nama_bank) ? 'empty' : '' }}">{{ $trx->nama_bank ?? 'Belum diisi' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="data-field">
                <div class="field-label">No Rekening</div>
                <div class="field-value mono {{ empty($trx->no_rekening) ? 'empty' : '' }}">{{ $trx->no_rekening ?? 'Belum diisi' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Approval & Pencairan --}}
      <div class="detail-section">
        <div class="section-header">
          <div class="section-header-icon amber"><i class="bi bi-clipboard-check"></i></div>
          <h6>Hasil Persetujuan & Transfer (Admin)</h6>
        </div>
        <div class="section-body">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="data-field">
                <div class="field-label">Tanggal Cair</div>
                <div class="field-value mono {{ empty($trx->tgl_cair) ? 'empty' : '' }}">{{ $trx->tgl_cair ? \Carbon\Carbon::parse($trx->tgl_cair)->format('d-m-Y') : 'Belum dicairkan' }}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="data-field">
                <div class="field-label">Nominal Final</div>
                <div class="field-value mono {{ is_null($trx->nominal_final) ? 'empty' : 'highlight' }}">{{ is_null($trx->nominal_final) ? 'Menunggu persetujuan' : 'Rp '.number_format($trx->nominal_final,0,',','.') }}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="data-field">
                <div class="field-label">Biaya Admin</div>
                <div class="field-value mono">Rp {{ number_format($trx->biaya_admin ?? 0, 0, ',', '.') }}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="data-field">
                <div class="field-label">Metode</div>
                <div class="field-value {{ ($trx->metode_pembayaran ?? '-') === '-' ? 'empty' : '' }}">{{ strtoupper($trx->metode_pembayaran ?? 'Belum ditentukan') }}</div>
              </div>
            </div>
            @if($trx->is_talangan)
            <div class="col-md-4">
              <div class="data-field" style="background:#fff7ed; border-color:rgba(245,158,11,.15);">
                <div class="field-label" style="color:#d97706;">Talangan</div>
                <div class="field-value" style="color:#92400e; font-weight:800;">✓ Ya ({{ ucfirst($trx->status_alokasi ?? 'belum') }})</div>
              </div>
            </div>
            @endif

            @if($status==='reject')
              <div class="col-12">
                <div class="data-field" style="background:#fef2f2; border-color:rgba(239,68,68,.15);">
                  <div class="field-label" style="color:#dc2626;">Keterangan Penolakan</div>
                  <div class="field-value danger-text">{{ $trx->keterangan_reject ?? '-' }}</div>
                </div>
              </div>
            @endif
          </div>

          @if(!empty($trx->bukti_transfer))
            <div class="mt-3 text-end">
              <a href="#" class="btn-soft" style="height:30px;padding:0 12px;font-size:.78rem;border-radius:999px;"
                 data-bs-toggle="modal" data-bs-target="#modalBuktiTransfer">
                <i class="bi bi-image"></i> Lihat Bukti Transfer
              </a>
            </div>

            <div class="modal fade" id="modalBuktiTransfer" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header" style="background:linear-gradient(135deg, rgba(139,92,246,.06), rgba(139,92,246,.02));">
                    <h5 class="modal-title"><i class="bi bi-image me-2" style="color:#7c3aed;"></i>Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body text-center" style="padding:24px;">
                    <img src="{{ asset('storage/' . $trx->bukti_transfer) }}" alt="Bukti Transfer" class="img-fluid rounded shadow-sm mb-3" style="border-radius:14px !important; max-height:400px; object-fit:contain;">
                    <div class="mt-2">
                      <a href="{{ asset('storage/' . $trx->bukti_transfer) }}" class="btn-brand" download>
                        <i class="bi bi-download"></i> Unduh Bukti Transfer
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-5">

      {{-- Bukti Transaksi (Nota) --}}
      <div class="detail-section mb-3">
        <div class="section-header">
          <div class="section-header-icon purple"><i class="bi bi-receipt"></i></div>
          <h6>Bukti Pembayaran / Nota (Pengaju)</h6>
          @if(!empty($trx->bukti_file))
            <a href="#" class="btn-soft ms-auto" style="height:30px;padding:0 12px;font-size:.78rem;border-radius:999px;"
               data-bs-toggle="modal" data-bs-target="#modalBukti">
              <i class="bi bi-image"></i> Lihat
            </a>
          @endif
        </div>
        <div class="section-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="data-field">
                <div class="field-label">Tanggal Bukti</div>
                <div class="field-value mono {{ empty($trx->tgl_bukti) ? 'empty' : '' }}">{{ $trx->tgl_bukti ? \Carbon\Carbon::parse($trx->tgl_bukti)->format('d-m-Y') : 'Belum ada' }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="data-field">
                <div class="field-label">Nominal Realisasi</div>
                <div class="field-value mono {{ is_null($trx->nominal_realisasi) ? 'empty' : 'highlight' }}">{{ is_null($trx->nominal_realisasi) ? 'Belum ada' : 'Rp '.number_format($trx->nominal_realisasi,0,',','.') }}</div>
              </div>
            </div>
            <div class="col-12">
              <div class="data-field">
                <div class="field-label">File Bukti</div>
                <div class="field-value {{ empty($trx->bukti_file) ? 'empty' : '' }}">
                  @if(!empty($trx->bukti_file))
                    <span style="color:#16a34a;"><i class="bi bi-check-circle-fill me-1"></i> File tersedia</span>
                  @else
                    Belum diupload
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Modal Bukti --}}
        @if(!empty($trx->bukti_file))
          <div class="modal fade" id="modalBukti" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(135deg, rgba(139,92,246,.06), rgba(139,92,246,.02));">
                  <h5 class="modal-title"><i class="bi bi-image me-2" style="color:#7c3aed;"></i>Bukti Transaksi</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" style="padding:24px;">
                  <img src="{{ asset('storage/' . $trx->bukti_file) }}" alt="Bukti" class="img-fluid rounded shadow-sm mb-3" style="border-radius:14px !important; max-height:400px; object-fit:contain;">
                  <div class="mt-2">
                    <a href="{{ asset('storage/' . $trx->bukti_file) }}" class="btn-brand" download>
                      <i class="bi bi-download"></i> Unduh Bukti
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif
      </div>

      {{-- ACTIONS --}}

      {{-- DONE --}}
      @if($status === 'done')
        <div class="detail-section">
          <div class="section-header">
            <div class="section-header-icon" style="background:rgba(22,163,74,.1);color:#16a34a;"><i class="bi bi-check-circle-fill"></i></div>
            <h6>Status Selesai</h6>
          </div>
          <div class="section-body">
            <div class="d-flex align-items-center gap-3 p-3" style="background:#f0fdf4;border-radius:12px;border:1px solid rgba(22,163,74,.15);">
              <i class="bi bi-check-circle-fill" style="font-size:1.5rem;color:#16a34a;"></i>
              <div>
                <div class="fw-bold" style="color:#166534;">Pengajuan Selesai</div>
                <div class="small" style="color:#15803d;">Transaksi telah dicatat ke Pencatatan Keuangan.</div>
              </div>
            </div>
          </div>
        </div>

      {{-- SUBMIT: Approve/Reject (Approver only) --}}
      @elseif($status === 'submit')
        @if($isApprover)
          <div class="detail-section mb-3">
            <div class="section-header">
              <div class="section-header-icon amber"><i class="bi bi-shield-check"></i></div>
              <h6>Form Persetujuan (Admin)</h6>
            </div>
            <div class="section-body">
              <form method="POST" action="{{ route('pengajuan_transaksi.approve', $trx->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-2">
                  <div class="col-md-6">
                    <div class="field-label" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:5px;">Tgl Cair *</div>
                    <input type="date" name="tgl_cair" class="form-control field-input" required>
                  </div>
                  <div class="col-md-6">
                    <div class="field-label" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:5px;">Nominal Final *</div>
                    <input type="number" name="nominal_final" class="form-control field-input" required placeholder="Nominal yang diberikan">
                  </div>
                  <div class="col-md-6">
                    <div class="field-label" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:5px;">Biaya Admin</div>
                    <input type="number" name="biaya_admin" class="form-control field-input" value="0" placeholder="0">
                  </div>
                  <div class="col-md-6">
                    <div class="field-label" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:5px;">Metode *</div>
                    <select name="metode_pembayaran" class="form-select field-input" required>
                      <option value="transfer">Transfer</option>
                      <option value="cash">Cash</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <div class="field-label" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:5px;">Bukti Transfer <small>(Ops.)</small></div>
                    <input type="file" name="bukti_transfer" class="form-control field-input" accept=".jpg,.jpeg,.png,.pdf" style="height:auto;padding:8px 12px;">
                  </div>
                  <div class="col-md-6 d-flex align-items-end pb-1">
                    <div class="form-check form-switch">
                      <input type="hidden" name="is_talangan" value="0">
                      <input class="form-check-input" type="checkbox" name="is_talangan" value="1" id="is_talangan_detail">
                      <label class="form-check-label fw-bold" for="is_talangan_detail">Tandai sebagai Talangan</label>
                    </div>
                  </div>
                  <div class="col-12 d-flex gap-2 justify-content-end mt-3">
                    <button type="submit" class="btn-brand"><i class="bi bi-check2"></i> Setujui</button>
                    <button type="button" class="btn-soft" onclick="openReject()" style="border-color:rgba(239,68,68,.3);color:#dc2626;">
                      <i class="bi bi-x-lg"></i> Tolak
                    </button>
                  </div>
                </div>
              </form>

              <form id="rejectForm" method="POST" action="{{ route('pengajuan_transaksi.reject', $trx->id) }}" class="d-none">
                @csrf
                <input type="hidden" name="keterangan_reject" id="rejectReason">
              </form>
            </div>
          </div>
        @endif

      {{-- APPROVE: Upload bukti (semua role bisa) --}}
      @elseif($status === 'approve')
        <div class="detail-section mb-3">
          <div class="section-header">
            <div class="section-header-icon info"><i class="bi bi-cloud-arrow-up"></i></div>
            <h6>Upload Bukti / Nota (Pengaju)</h6>
          </div>
          <div class="section-body">
            <form method="POST" action="{{ route('pengajuan_transaksi.upload_bukti', $trx->id) }}" enctype="multipart/form-data">
              @csrf
              <div class="row g-2">
                <div class="col-md-6">
                  <div class="field-label" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:5px;">Tgl Bukti *</div>
                  <input type="date" name="tgl_bukti" class="form-control field-input" required>
                </div>
                <div class="col-md-6">
                  <div class="field-label" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:5px;">Nominal *</div>
                  <input type="number" name="nominal_realisasi" class="form-control field-input" required>
                </div>
                <div class="col-12">
                  <div class="field-label" style="font-weight:800;font-size:.70rem;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;margin-bottom:5px;">File Bukti *</div>
                  <input type="file" name="bukti_file" class="form-control field-input" accept="image/*" required style="height:auto;padding:8px 12px;">
                </div>
                <div class="col-12 d-flex justify-content-end mt-3">
                  <button type="submit" class="btn-brand"><i class="bi bi-upload"></i> Simpan Bukti</button>
                </div>
              </div>
            </form>
          </div>
        </div>

      {{-- BUKTI: Finalize (Approver only) --}}
      @elseif($status === 'bukti')
        @if($isApprover)
          <div class="detail-section">
            <div class="section-header">
              <div class="section-header-icon primary"><i class="bi bi-flag-fill"></i></div>
              <h6>Selesaikan Pengajuan</h6>
            </div>
            <div class="section-body">
              <p class="mb-3" style="color:var(--ink-600);font-weight:600;font-size:.88rem;line-height:1.5;">
                Menyelesaikan akan mengubah status menjadi <strong>Selesai</strong> dan memasukkan transaksi ke Pencatatan Keuangan.
              </p>
              <form method="POST" action="{{ route('pengajuan_transaksi.finalize', $trx->id) }}">
                @csrf
                <button type="submit" class="btn-brand w-100">
                  <i class="bi bi-flag"></i> Selesaikan
                </button>
              </form>
            </div>
          </div>
        @endif
      @endif

    </div>
  </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function openReject(){
    Swal.fire({
      title: "Tolak Pengajuan?",
      input: "textarea",
      inputLabel: "Keterangan Penolakan",
      inputPlaceholder: "Tulis alasan penolakan...",
      showCancelButton: true,
      confirmButtonText: "Ya, Tolak",
      cancelButtonText: "Batal",
      confirmButtonColor: "#dc2626",
    }).then((res)=>{
      if(!res.isConfirmed) return;
      document.getElementById('rejectReason').value = res.value || '';
      document.getElementById('rejectForm').submit();
    });
  }
</script>
@endpush
