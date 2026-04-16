<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pengajuan Komponen</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#fff;
    }
    *{box-sizing:border-box}
    body{ background:var(--bg); font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; color:var(--ink); margin:0; }

    /* Topbar */
    .topbar{
      position:sticky; top:0; z-index:1030;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      height:56px;
      border-bottom:1px solid rgba(255,255,255,.18);
    }
    .brand{ display:flex; align-items:center; gap:10px; font-weight:800; letter-spacing:.2px; }
    .brand-badge{
      font-size:.72rem; font-weight:800;
      padding:.22rem .55rem; border-radius:999px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.22);
      white-space:nowrap;
    }

    /* Layout */
    .app{ display:flex; min-height:calc(100vh - 56px); }

    .sidebar{
      width:260px; background:var(--card); border-right:1px solid var(--line);
      padding:14px; position:sticky; top:56px; height:calc(100vh - 56px); overflow:auto;
    }
    .menu-title{
      font-size:.72rem; letter-spacing:.08em; color:var(--ink-600);
      text-transform:uppercase; margin:8px 0; font-weight:700;
    }
    .nav-link-custom{
      display:flex; align-items:center; gap:10px;
      padding:9px 10px; border-radius:14px;
      text-decoration:none; color:var(--ink);
      font-weight:600; font-size:.92rem; line-height:1;
      transition:.18s; white-space:nowrap;
    }
    .nav-link-custom i{ font-size:1.05rem; }
    .nav-link-custom:hover{ background:var(--brand-50); color:var(--brand-700); transform:translateX(2px); }
    .nav-link-custom.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff; box-shadow:0 16px 28px rgba(2,6,23,.12);
      font-weight:700;
    }

    .content{ flex:1; padding:18px 18px 22px; }

    .page-title{ font-size:1.5rem; font-weight:800; margin:0; }
    .page-sub{ color:var(--ink-600); margin:6px 0 0; }

    .card-soft{
      background:var(--card);
      border:1px solid var(--line);
      border-radius:18px;
      box-shadow:0 8px 22px rgba(15,23,42,.06);
    }
    td.actions{ white-space:nowrap; width:1%; }

    .btn-icon{
      padding:.35rem .55rem;
      line-height:1;
      display:inline-flex;
      align-items:center;
      gap:.35rem;
      border-radius:10px;
    }
    .btn-icon i{ font-size:1rem }

    .hint-pill{
      display:inline-flex; align-items:center; gap:8px;
      padding:.45rem .7rem; border-radius:999px;
      background:#f8fafc; border:1px solid var(--line);
      color:var(--ink-600); font-size:.85rem; font-weight:600;
    }
    .hint-pill b{ color:var(--ink); }

    .status-badge{
      font-size:.78rem; font-weight:800; border-radius:999px;
      padding:.35rem .7rem; border:1px solid transparent; white-space:nowrap;
    }
    .st-draft{ background:#f1f5f9; color:#475569; border-color:#cbd5e1; }
    .st-submit_request{ background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
    .st-approve_request{ background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
    .st-reject_request{ background:#fef2f2; color:#991b1b; border-color:#fecaca; }
    .st-cancel{ background:#fef2f2; color:#6b7280; border-color:#d1d5db; }
    .st-done{ background:#eefdfb; color:#115e59; border-color:#99f6e4; }
    /* legacy compat */
    .st-submit_payment{ background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
    .st-approve_payment{ background:#eefdfb; color:#115e59; border-color:#99f6e4; }
    .st-reject_payment{ background:#fef2f2; color:#991b1b; border-color:#fecaca; }

    /* Header actions */
    .head-actions{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      justify-content:flex-end;
    }

    /* TOTAL CARD */
    .total-card{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      padding:12px 14px;
      border-radius:14px;
      border:1px solid var(--line);
      background:linear-gradient(135deg, rgba(22,163,74,.10), rgba(255,255,255,.85));
    }
    .total-card .label{ font-weight:800; color:var(--ink-600); font-size:.85rem; }
    .total-card .value{ font-weight:900; font-size:1.15rem; color:var(--ink); }

    /* tombol tambah sejajar */
    .btn-add-item{
      display:inline-flex !important;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:.55rem .95rem;
      line-height:1;
      white-space:nowrap;
      border-radius:12px;
      font-weight:800;
    }
    .btn-add-item i{ display:inline-block !important; line-height:1; font-size:1.05rem; }
    .summary-row td{
      border-top:0 !important;
      background:#fafafa;
    }
    .summary-main td{
      border-top:1px solid var(--line) !important;
    }
    .invoice-mini{
      width:130px;
      min-width:130px;
    }
    .arrived-badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border-radius:999px;
      padding:.28rem .62rem;
      font-size:.78rem;
      font-weight:800;
      white-space:nowrap;
    }
    .arrived-yes{ background:#eefdfb; color:#115e59; border:1px solid #99f6e4; }
    .arrived-no{ background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; }
    .table-compact{
      font-size:.92rem;
    }
    .table-compact > :not(caption) > * > *{
      padding:.5rem .55rem;
    }

    /* Tambahan Redesign */
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
    .summary-item { padding: 14px 18px; border-radius: 16px; background: #fff; border: 1px solid var(--line); box-shadow: 0 4px 12px rgba(0,0,0,.02); }
    .summary-item.highlight { background: linear-gradient(135deg, rgba(22,163,74,.05), rgba(22,163,74,.01)); border-color: rgba(22,163,74,.2); }
    .summary-label { font-size: 0.72rem; font-weight: 800; color: var(--ink-600); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
    .summary-value { font-size: 1.15rem; font-weight: 900; color: var(--ink); line-height: 1.2; }
    
    .action-section { background: var(--card); border: 1px solid var(--line); border-radius: 20px; padding: 22px; margin-bottom: 20px; box-shadow: 0 8px 24px rgba(15,23,42,.04); }
    .action-section.admin-mode { border: 2px solid rgba(22,163,74,.4); background: #fdfcf9; }
    .section-title { font-size: 1.15rem; font-weight: 800; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; color: var(--ink); }

    @media (max-width: 991.98px){
      .sidebar{ position:fixed; left:-280px; z-index:1040; transition:left .2s; top:56px; height:calc(100vh - 56px); }
      .sidebar.open{ left:0; }
      .content{ padding:14px; }
      .backdrop{ display:none; position:fixed; inset:0; background:rgba(15,23,42,.38); z-index:1035; }
      .backdrop.show{ display:block; }
    }
  </style>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
  <!-- Topbar -->
  <nav class="navbar topbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>

      <div class="brand">
        <span>STAS-RG • Pengajuan Komponen</span>
        </div>

      <div class="ms-auto">@include('navbar')</div>
    </div>
  </nav>

  @php
    $rawStatus = $request_pembelian->status_request ?? '';
    $status = strtolower(trim($rawStatus));
    $status = str_replace(' ', '_', $status);

    $statusLabel = [
      'draft'           => 'Draft / Belum Diajukan',
      'submit_request'  => 'Dalam Pemesanan',
      'approve_request' => 'Dalam Pembelian',
      'reject_request'  => 'Ditolak',
      'cancel'          => 'Dibatalkan',
      'done'            => 'Selesai (Sudah Sampai)',
      // legacy compat
      'submit_payment'  => 'Dalam Pembelian',
      'approve_payment' => 'Selesai (Sudah Sampai)',
      'reject_payment'  => 'Ditolak',
    ][$status] ?? ucwords(str_replace('_',' ', $status));

    $statusClass = 'st-'.$status;

    $isApprover = in_array(Auth::user()->role, ['admin','bendahara']);
    $canEditItems = !in_array($status, ['done','cancel']) && (($isApprover) || in_array($status, ['draft','submit_request','reject_request']));
    $canProcessByApprover = $isApprover && !in_array($status, ['draft','done','cancel']);
    $isTalangan = (bool)($request_pembelian->is_talangan ?? false);
    $statusAlokasi = (string)($request_pembelian->status_alokasi ?? 'belum');



    // ✅ TOTAL
    $grandTotal = 0;
    foreach($detail as $it){
      $q = (int)($it->kuantitas ?? 0);
      $h = (int)($it->harga ?? 0);
      if($q < 0) $q = 0;
      if($h < 0) $h = 0;
      $grandTotal += ($q * $h);
    }
    $biayaAdmin = (float)($request_pembelian->biaya_admin_transfer ?? 0);
    $totalInvoice = 0;
    foreach($detail as $it){
      $totalInvoice += (float)($it->total_invoice ?? 0);
    }
    $totalDibayar = $totalInvoice + $biayaAdmin;
  @endphp

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      @include('layouts.sidebar-menu')
    </aside>

    <div class="backdrop" id="backdrop"></div>

    <!-- Main -->
    <main class="content">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <div class="page-title">Pengajuan Komponen</div>
          <div class="page-sub">Tambah / tinjau item serta update status proses pembelian komponen.</div>
        </div>

        <div class="head-actions">
          <a href="https://drive.google.com/file/d/1HKaZH2I-Ohq7S-SBb8ADMHMd3htU0nio/view?usp=sharing"
             target="_blank" rel="noopener"
             class="btn btn-outline-light"
             style="border-color: rgba(15,23,42,.16); color: var(--ink); background:#fff;">
            <i class="bi bi-journal-bookmark me-1"></i> Manual Book
          </a>

          <a href="{{ route('requestpembelian.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-short me-1"></i> Kembali ke Daftar
          </a>
        </div>
      </div>

      {{-- ALERTS --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          <div class="fw-bold mb-1">Gagal menyimpan:</div>
          <ul class="mb-0">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

            <!-- 1. Ringkasan Header -->
      <div class="card card-soft mb-4 border-0" style="box-shadow: 0 6px 20px rgba(15,23,42,.06);">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
               <i class="bi bi-card-checklist text-primary"></i> Ringkasan Pengajuan
            </h5>
            @if($isTalangan)
               <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-2 px-3 fw-bold rounded-pill shadow-sm">
                 <i class="bi bi-arrow-left-right me-1"></i> Talangan ({{ $statusAlokasi === 'sudah' ? 'Telah Dialokasi' : 'Belum Dialokasi' }})
               </span>
            @endif
          </div>

          <div class="summary-grid">
            <div class="summary-item">
              <div class="summary-label">Status Saat Ini</div>
              <div class="summary-value"><span class="status-badge {{ $statusClass }} shadow-sm">{{ $statusLabel }}</span></div>
            </div>
            <div class="summary-item">
              <div class="summary-label">Tim Penelitian</div>
              <div class="summary-value fs-6 fw-bold text-dark">{{ $project->firstWhere('id', $request_pembelian->id_project)->nama_project ?? '-' }}</div>
            </div>
            <div class="summary-item">
              <div class="summary-label">Tanggal Terdaftar</div>
              <div class="summary-value fs-6 fw-bold">{{ \Carbon\Carbon::parse($request_pembelian->tgl_request)->format('d M Y') }}</div>
            </div>
            <div class="summary-item highlight">
              <div class="summary-label text-success">Total Estimasi Awal ({{ count($detail) }} Item)</div>
              <div class="summary-value text-success">Rp {{ number_format($grandTotal,0,',','.') }}</div>
            </div>
          </div>

          {{-- Aksi Peneliti (Kirim Draft) --}}
          @if(!$isApprover && $status === 'draft')
             <div class="mt-4 pt-4 border-top text-end">
                @if($grandTotal > 0 && count($detail) > 0)
                  <form action="{{ route('requestpembelian.submit', $request_pembelian->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-brand px-4 py-2 fs-6 fw-bold shadow-sm">
                      <i class="bi bi-send-fill me-2"></i> Kirim Pengajuan Sekarang
                    </button>
                  </form>
                @else
                  <button type="button" class="btn btn-secondary px-4 py-2 opacity-75 fw-bold" disabled>
                    <i class="bi bi-send-exclamation me-1"></i> Kirim Pengajuan
                  </button>
                  <div class="text-danger mt-2 fw-medium fs-6"><i class="bi bi-info-circle me-1"></i> Tambahkan minimal 1 barang dengan total nilai di atas Rp 0 untuk mengirim pengajuan.</div>
                @endif
             </div>
          @elseif(!$isApprover)
             <div class="mt-4 pt-3 border-top">
                @if($status === 'done')
                  <span class="hint-pill bg-success-subtle border-success-subtle text-success-emphasis fw-bold px-3 py-2">
                     <i class="bi bi-check2-circle mb-1 fs-5"></i> Proses Selesai. Seluruh tahapan telah tuntas.
                  </span>
                @else
                  <span class="hint-pill bg-primary-subtle border-primary-subtle text-primary-emphasis fw-bold px-3 py-2">
                     <i class="bi bi-clock-history fs-5"></i> Pengajuan Anda saat ini sedang dalam proses tinjauan tim Keuangan.
                  </span>
                @endif
             </div>
          @endif
        </div>
      </div>

      <!-- 2. Area Tindakan Admin/Bendahara -->
      @if ($isApprover)
        <div class="action-section admin-mode shadow-sm">
          <div class="section-title text-success mb-4 fs-5">
            <i class="bi bi-ui-checks-grid me-1"></i> Panel Review Khusus Admin & Bendahara
          </div>
          
          <div class="row g-4 align-items-start">
            <div class="col-lg-4">
              <label class="form-label mb-2 fw-bold text-dark fs-6">Keputusan Status</label>
              <select class="form-select border-success border-opacity-50 py-2 fw-bold text-dark shadow-sm bg-white" id="status_request" name="status_request" form="bulkInvoiceForm" @if(count($detail)==0) disabled @endif>
                <option value="draft" {{ $status=='draft' ? 'selected' : '' }} disabled>Draft (Peneliti)</option>
                <option value="submit_request" {{ in_array($status,['submit_request']) ? 'selected' : '' }}>Dalam Pemesanan</option>
                <option value="approve_request" {{ in_array($status,['approve_request','submit_payment']) ? 'selected' : '' }}>Dalam Pembelian</option>
                <option value="done" {{ in_array($status,['done','approve_payment']) ? 'selected' : '' }}>Selesai (Sudah Sampai)</option>
                <optgroup label="Tolak / Batal">
                  <option value="reject_request" {{ in_array($status,['reject_request','reject_payment']) ? 'selected' : '' }}>Ditolak</option>
                  <option value="cancel" {{ $status=='cancel' ? 'selected' : '' }}>Dibatalkan</option>
                </optgroup>
              </select>
            </div>

            <div class="col-lg-4">
              <label class="form-label mb-2 fw-bold text-dark fs-6">Attachment / Bukti Transfer <small class="text-muted fw-normal" style="font-size:0.75rem;">(Ops.)</small></label>
              <input type="file" class="form-control py-2 shadow-sm bg-white" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" form="bulkInvoiceForm">
              @if(!empty($request_pembelian->bukti_transfer))
                <div class="mt-2 d-flex align-items-center gap-2">
                  <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-2 fw-bold" style="font-size:.75rem;">
                    <i class="bi bi-check-circle me-1"></i> Bukti tersimpan
                  </span>
                  <a href="#" class="text-primary fw-bold" style="font-size:.78rem; text-decoration:none;" data-bs-toggle="modal" data-bs-target="#modalBuktiTransferHeader">
                    <i class="bi bi-eye me-1"></i>Lihat
                  </a>
                  <a href="{{ asset('storage/' . $request_pembelian->bukti_transfer) }}" class="text-muted fw-bold" style="font-size:.78rem; text-decoration:none;" download>
                    <i class="bi bi-download me-1"></i>Unduh
                  </a>
                </div>

                <div class="modal fade" id="modalBuktiTransferHeader" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header" style="background:linear-gradient(135deg, rgba(139,92,246,.06), rgba(139,92,246,.02));">
                        <h5 class="modal-title"><i class="bi bi-image me-2" style="color:#7c3aed;"></i>Bukti Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body text-center" style="padding:24px;">
                        <img src="{{ asset('storage/' . $request_pembelian->bukti_transfer) }}" alt="Bukti Transfer" class="img-fluid rounded shadow-sm mb-3" style="border-radius:14px !important; max-height:400px; object-fit:contain;">
                        <div class="mt-2">
                          <a href="{{ asset('storage/' . $request_pembelian->bukti_transfer) }}" class="btn btn-sm btn-success rounded-pill fw-bold px-4" download>
                            <i class="bi bi-download me-1"></i> Unduh
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            </div>

            <div class="col-lg-4 pt-1">
              <input type="hidden" name="is_talangan" value="0" form="bulkInvoiceForm">
              <label class="form-label mb-2 fw-bold text-dark fs-6 d-block">Opsi Tambahan</label>
              <div class="form-check form-switch fs-5 d-inline-block">
                <input class="form-check-input mt-1 shadow-sm" type="checkbox" role="switch" value="1" id="is_talangan" name="is_talangan" form="bulkInvoiceForm" {{ $isTalangan ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark user-select-none fs-6 ms-2" for="is_talangan" style="cursor:pointer; vertical-align: middle;"> Tandai sbg Pembelian Talangan </label>
              </div>
            </div>

            <div class="col-lg-12" id="keterangan_reject_wrap">
              <label class="form-label mb-2 fw-bold text-danger fs-6">Keterangan Penolakan / Pembatalan</label>
              <input type="text" class="form-control border-danger border-opacity-50 py-2 shadow-sm bg-white" id="keterangan_reject" name="keterangan_reject"
                    value="{{ $request_pembelian->keterangan_reject }}" placeholder="Tuliskan alasan penolakan / pembatalan..." form="bulkInvoiceForm">
            </div>
            
            @if(count($detail) > 0)
            <div class="col-12 mt-4 text-end border-top pt-4 border-success border-opacity-25">
               <button type="submit" class="btn btn-success px-5 py-2 fw-bold fs-6 shadow" form="bulkInvoiceForm">
                 <i class="bi bi-save2-fill me-2"></i> Simpan Status & Nominal Admin
               </button>
            </div>
            @endif
          </div>
        </div>
      @endif

      <!-- Tabel Detail -->
      <div class="card card-soft">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-compact align-middle mb-0">
                            <thead>
                <tr class="bg-light text-nowrap">
                  <th style="min-width: 200px;">Nama Barang</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end" style="min-width: 120px;">Harga Est.</th>
                  <th class="text-end" style="min-width: 120px;">Total Est.</th>
                  <th style="min-width: 170px;">Total Invoice (Real)</th>
                  <th class="text-center">Link</th>
                  <th style="min-width: 140px;">Kategori / Sumber</th>
                  @if($status !== 'draft')
                  <th class="text-center">Kedatangan</th>
                  @endif
                  <th class="text-center" style="min-width: 140px;">Aksi</th>
                </tr>
              </thead>

              <tbody>
                @foreach ($detail as $d)
                  @php
                    $qty = (int)($d->kuantitas ?? 0);
                    $hargaItem = (int)($d->harga ?? 0);
                    if($qty < 0) $qty = 0;
                    if($hargaItem < 0) $hargaItem = 0;
                    $rowTotal = $qty * $hargaItem;
                  @endphp
                  <tr>
                    <td>{{ $d->nama_barang }}</td>
                    <td class="text-center">{{ $qty }}</td>
                    <td class="text-end">Rp {{ number_format($hargaItem,0,',','.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($rowTotal,0,',','.') }}</td>
                    <td style="min-width:280px;">
                      @if($canProcessByApprover)
                        <div class="d-flex align-items-center">
                          <input type="number" min="0" step="0.01" name="total_invoice[{{ $d->id }}]" value="{{ $d->total_invoice }}"
                                 class="form-control form-control-sm invoice-mini" placeholder="Total invoice"
                                 form="bulkInvoiceForm">
                        </div>
                      @else
                        <span class="fw-bold text-primary">
                          @if(!is_null($d->total_invoice))
                            Rp {{ number_format((float)$d->total_invoice,0,',','.') }}
                          @else
                            -
                          @endif
                        </span>
                      @endif
                    </td>
                    
                    <td><a href="{{ $d->link_pembelian }}" target="_blank" rel="noreferrer">Lihat Link</a></td>
                    <td>
                      @if ($d->id_subkategori_sumberdana)
                        @php
                          $subName = null;
                          foreach($subkategori as $sub){ if($sub->id == $d->id_subkategori_sumberdana){ $subName = $sub->nama; break; } }
                        @endphp
                        {{ $subName ?? '-' }}
                      @else
                        -
                      @endif
                    </td>
                    @if($status !== 'draft')
                    <td>
                      <span class="arrived-badge {{ !empty($d->is_sampai) ? 'arrived-yes' : 'arrived-no' }}">
                        {{ !empty($d->is_sampai) ? 'Sudah Sampai' : 'Belum Sampai' }}
                      </span>
                    </td>
                    @endif

                                        <td class="actions text-center">
                      <div class="d-flex justify-content-center gap-2">
                        @if(!empty($d->invoice_pembelian))
                          <a href="#" class="btn btn-success btn-sm px-2 py-1 shadow-sm" title="Lihat Invoice Item"
                             data-bs-toggle="modal" data-bs-target="#modalInvoice{{ $d->id }}">
                            <i class="bi bi-receipt"></i>
                          </a>

                          <div class="modal fade" id="modalInvoice{{ $d->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header" style="background:linear-gradient(135deg, rgba(139,92,246,.06), rgba(139,92,246,.02));">
                                  <h5 class="modal-title"><i class="bi bi-receipt me-2" style="color:#7c3aed;"></i>Invoice - {{ $d->nama_barang }}</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center" style="padding:24px;">
                                  <img src="{{ asset('storage/'.$d->invoice_pembelian) }}" alt="Invoice" class="img-fluid rounded shadow-sm mb-3" style="border-radius:14px !important; max-height:400px; object-fit:contain;">
                                  <div class="mt-2">
                                    <a href="{{ asset('storage/'.$d->invoice_pembelian) }}" class="btn btn-sm btn-success rounded-pill fw-bold px-4" download>
                                      <i class="bi bi-download me-1"></i> Unduh
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif

                        @if($isApprover && $status !== 'reject_request')
                          <a href="{{ route('requestpembelian.addbukti',$d->id) }}" class="btn btn-outline-primary btn-sm px-2 py-1" title="Upload Bukti Invoice">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                          </a>
                        @endif

                        {{-- EDIT/DELETE --}}
                        @if($canEditItems)
                          <a href="{{ route('requestpembelian.editdetail',$d->id) }}" class="btn btn-warning btn-sm px-2 py-1 shadow-sm border-0 text-dark" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                          </a>

                          <form action="{{ route('requestpembelian.destroydetail',$d->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm px-2 py-1 shadow-sm border-0" title="Hapus">
                              <i class="bi bi-trash-fill"></i>
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach

                {{-- FORM TAMBAH ITEM (PENELITI + ADMIN, saat editable) --}}
                @if(Auth::user()->role !== 'bendahara')
                  @if($canEditItems)
                    <tr>
                      <td colspan="{{ $status !== 'draft' ? 9 : 8 }}" class="pt-4 pb-3">
                        <form action="{{ route('requestpembelian.storedetail') }}" method="POST" class="row g-2 align-items-end" id="formAddItem">
                          @csrf
                          <input type="hidden" name="id_request_pembelian_header" value="{{ $request_pembelian->id }}">

                          <div class="col-lg-3">
                            <label class="form-label mb-1">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                          </div>

                          <div class="col-lg-1">
                            <label class="form-label mb-1">Qty</label>
                            <input type="number" name="kuantitas" id="qty" class="form-control" min="1" step="1" required>
                          </div>

                          <div class="col-lg-2">
                            <label class="form-label mb-1">Harga</label>
                            <input type="text" name="harga" id="harga" class="form-control" inputmode="numeric" placeholder="Rp 0" required>
                          </div>

                          <div class="col-lg-2">
                            <label class="form-label mb-1">Total</label>
                            <input type="text" id="totalPreview" class="form-control" value="Rp 0" disabled>
                          </div>

                          <div class="col-lg-2">
                            <label class="form-label mb-1">Link Pembelian</label>
                            <input type="url" name="link_pembelian" class="form-control" required>
                          </div>

                          <div class="col-lg-1">
                            <label class="form-label mb-1">Subkategori</label>
                            <select name="id_subkategori_sumberdana" class="form-select">
                              <option value="">-- Pilih --</option>
                              @foreach ($subkategori as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->nama }}</option>
                              @endforeach
                            </select>
                          </div>

                          <div class="col-lg-1 text-end">
                            <button type="submit" class="btn btn-success w-100 btn-add-item" id="btnTambah">
                              <i class="bi bi-plus-lg"></i>
                              <span>Tambah</span>
                            </button>
                          </div>
                        </form>
                      </td>
                    </tr>
                  @else
                    <tr>
                      <td colspan="{{ $status !== 'draft' ? 9 : 8 }}" class="text-center py-4">
                        <span class="hint-pill">
                          <i class="bi bi-lock"></i>
                          Item tidak bisa ditambah/diedit karena status sudah <b>{{ $statusLabel }}</b>.
                        </span>
                      </td>
                    </tr>
                  @endif
                @endif

              </tbody>

              {{-- FOOTER TOTAL --}}
                            {{-- FORM INDUK HIDDEN --}}
              <form id="bulkInvoiceForm" action="{{ route('requestpembelian.storeinvoicebulk', $request_pembelian->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
              </form>

              {{-- FOOTER TOTAL --}}
              <tfoot>
                <tr class="summary-main bg-light">
                  <td colspan="4" class="p-0 border-0"></td>
                  <th colspan="3" class="text-end py-3 text-muted">Total Estimasi Awal (Peneliti)</th>
                  <th class="text-end py-3 text-muted">Rp {{ number_format($grandTotal,0,',','.') }}</th>
                  @if($status !== 'draft') <td class="p-0 border-0 bg-white"></td> @endif
                </tr>
                <tr class="summary-row">
                  <td colspan="4" class="p-0 border-0"></td>
                  <th colspan="3" class="text-end py-3">Total Invoice (Sesuai Nota Barang)</th>
                  <th class="text-end text-primary py-3 fs-6">Rp {{ number_format($totalInvoice,0,',','.') }}</th>
                  @if($status !== 'draft') <td class="p-0 border-0"></td> @endif
                </tr>
                <tr class="summary-row">
                  <td colspan="4" class="p-0 border-0"></td>
                  <th colspan="3" class="text-end align-middle py-3">Biaya Tambahan / Transaksi Bank</th>
                  <th class="py-3">
                    @if($canProcessByApprover)
                      <div class="input-group input-group-sm justify-content-end shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">Rp</span>
                        <input type="number" min="0" step="0.01" name="biaya_admin_transfer"
                               value="{{ $request_pembelian->biaya_admin_transfer ?? 0 }}"
                               class="form-control form-control-sm text-end border-start-0 fw-bold" style="max-width: 140px;" placeholder="0"
                               form="bulkInvoiceForm">
                      </div>
                    @else
                      <div class="text-end text-warning fw-bold">Rp {{ number_format($biayaAdmin,0,',','.') }}</div>
                    @endif
                  </th>
                  @if($status !== 'draft') <td class="p-0 border-0"></td> @endif
                </tr>
                <tr class="summary-row bg-success bg-opacity-10 border-top border-success border-opacity-25">
                  <td colspan="4" class="p-0 border-0 bg-white"></td>
                  <th colspan="3" class="text-end fs-6 py-4 text-success"><i class="bi bi-wallet2 me-2"></i>TOTAL KESELURUHAN DIBAYAR</th>
                  <th class="text-end fw-bold text-success fs-5 py-4">Rp {{ number_format($totalDibayar,0,',','.') }}</th>
                  @if($status !== 'draft') <td class="p-0 border-0 bg-white"></td> @endif
                </tr>
              </tfoot>

            </table>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Sidebar toggle (mobile)
    const sidebar=document.getElementById('appSidebar');
    const toggleBtn=document.getElementById('sidebarToggle');
    const backdrop=document.getElementById('backdrop');
    const openSidebar=()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar=()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    toggleBtn?.addEventListener('click',()=> sidebar.classList.contains('open')?closeSidebar():openSidebar());
    backdrop?.addEventListener('click',closeSidebar);

    // Alasan reject only when needed (admin)
    (function(){
      const sel=document.getElementById('status_request');
      const wrap=document.getElementById('keterangan_reject_wrap');
      function toggle(){
        if(!sel || !wrap) return;
        wrap.style.display=(sel.value==='reject_request'||sel.value==='cancel')?'':'none';
      }
      sel?.addEventListener('change',toggle); toggle();
    })();

    // ====== VALIDASI + FORMAT + PREVIEW TOTAL ======
    (function(){
      const harga = document.getElementById('harga');
      const qty   = document.getElementById('qty');
      const form  = document.getElementById('formAddItem');
      const totalPreview = document.getElementById('totalPreview');
      const btnTambah = document.getElementById('btnTambah');
      if(!form) return;

      const toNumber = (s) => (s || '').toString().replace(/[^0-9]/g,'');

      function updateTotal(){
        const q = parseInt(qty?.value || '0', 10) || 0;
        const h = parseInt(toNumber(harga?.value || ''), 10) || 0;
        const t = Math.max(0,q) * Math.max(0,h);
        if(totalPreview){
          totalPreview.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(t);
        }
      }

      // Qty: cegah minus (extra guard)
      qty?.addEventListener('input', ()=>{
        if(qty.value === '') { updateTotal(); return; }
        const v = parseInt(qty.value,10);
        if(isNaN(v) || v < 1) qty.value = 1;
        updateTotal();
      });

      // Harga: hanya angka + format rupiah
      harga?.addEventListener('input', () => {
        const raw = toNumber(harga.value);
        if(!raw){ harga.value = ''; updateTotal(); return; }
        harga.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(raw);
        updateTotal();
      });

      // Submit: pastikan angka murni & tidak 0 / minus
      form.addEventListener('submit', (e) => {
        const q = parseInt(qty?.value || '0',10) || 0;
        const h = parseInt(toNumber(harga?.value || ''),10) || 0;

        if(q < 1){
          e.preventDefault();
          alert('Qty minimal 1 yaa.');
          qty?.focus();
          return;
        }
        if(h < 1){
          e.preventDefault();
          alert('Harga harus lebih dari 0 yaa.');
          harga?.focus();
          return;
        }

        // kirim angka murni
        harga.value = toNumber(harga.value);
      });

      updateTotal();
    })();
  </script>
</body>
</html>
