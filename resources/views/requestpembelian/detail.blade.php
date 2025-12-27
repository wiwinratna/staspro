<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Request Pembelian</title>

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
    .st-submit_request{ background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
    .st-approve_request{ background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
    .st-reject_request{ background:#fef2f2; color:#991b1b; border-color:#fecaca; }
    .st-submit_payment{ background:#f5f3ff; color:#5b21b6; border-color:#ddd6fe; }
    .st-reject_payment{ background:#fdf2f8; color:#9d174d; border-color:#fbcfe8; }
    .st-done{ background:#eefdfb; color:#115e59; border-color:#99f6e4; }

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
        <span>STAS-RG • Request Pembelian</span>
        <span class="brand-badge">{{ Auth::user()->role === 'admin' ? 'ADMIN' : 'PENELITI' }}</span>
      </div>

      <div class="ms-auto">@include('navbar')</div>
    </div>
  </nav>

  @php
    $rawStatus = $request_pembelian->status_request ?? '';
    $status = strtolower(trim($rawStatus));
    $status = str_replace(' ', '_', $status);

    $statusLabel = [
      'submit_request'  => 'Submit Request',
      'approve_request' => 'Approve Request',
      'reject_request'  => 'Reject Request',
      'submit_payment'  => 'Submit Payment',
      'reject_payment'  => 'Reject Payment',
      'done'            => 'Done',
    ][$status] ?? ucwords(str_replace('_',' ', $status));

    $statusClass = 'st-'.$status;

    $isAdmin = Auth::user()->role === 'admin';
    $canEditItems = ($isAdmin) || in_array($status, ['submit_request','reject_request']);
    $canUploadBukti = (!$isAdmin) && in_array($status, ['approve_request','reject_payment','submit_payment']);



    // ✅ TOTAL
    $grandTotal = 0;
    foreach($detail as $it){
      $q = (int)($it->kuantitas ?? 0);
      $h = (int)($it->harga ?? 0);
      if($q < 0) $q = 0;
      if($h < 0) $h = 0;
      $grandTotal += ($q * $h);
    }
  @endphp

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      <div class="menu-title">Menu</div>

      <a class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>

      <a class="nav-link-custom {{ request()->routeIs('project.*') ? 'active' : '' }}" href="{{ route('project.index') }}">
        <i class="bi bi-kanban"></i> Project
      </a>

      <a class="nav-link-custom {{ request()->routeIs('requestpembelian.*') ? 'active' : '' }}" href="{{ route('requestpembelian.index') }}">
        <i class="bi bi-bag-check"></i> Request Pembelian
      </a>

      @if (Auth::user()->role == 'admin')
        <div class="menu-title mt-3">Administrasi</div>

        <a class="nav-link-custom {{ request()->routeIs('sumberdana.*') ? 'active' : '' }}" href="{{ route('sumberdana.index') }}">
          <i class="bi bi-cash-coin"></i> Sumber Dana
        </a>

        <a class="nav-link-custom {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
          <i class="bi bi-wallet2"></i> Kas
        </a>

        <a class="nav-link-custom {{ request()->routeIs('pencatatan_keuangan') ? 'active' : '' }}" href="{{ route('pencatatan_keuangan') }}">
          <i class="bi bi-journal-text"></i> Pencatatan Keuangan
        </a>

        <a class="nav-link-custom {{ request()->routeIs('laporan_keuangan') ? 'active' : '' }}" href="{{ route('laporan_keuangan') }}">
          <i class="bi bi-graph-up"></i> Laporan Keuangan
        </a>

        <a class="nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
          <i class="bi bi-people"></i> Management User
        </a>
      @endif
    </aside>

    <div class="backdrop" id="backdrop"></div>

    <!-- Main -->
    <main class="content">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <div class="page-title">Request Pembelian</div>
          <div class="page-sub">Tambah / tinjau item serta ubah status request.</div>
        </div>

        <div class="head-actions">
          <a href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
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

      <!-- Ringkasan -->
      <div class="card card-soft mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <span class="hint-pill">
              Status saat ini:
              <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </span>

            <div class="total-card">
              <div>
                <div class="label">Total Keseluruhan</div>
                <div class="value">Rp {{ number_format($grandTotal,0,',','.') }}</div>
              </div>
              <div class="hint-pill" style="background:#fff;">
                <i class="bi bi-calculator"></i> <b>{{ count($detail) }}</b> item
              </div>
            </div>

            @if(!$isAdmin)
              @if($status === 'approve_request')
                <span class="hint-pill"><i class="bi bi-info-circle"></i> Request sudah disetujui. Silahkan <b>upload bukti pembayaran</b>.</span>
              @elseif($status === 'reject_payment')
                <span class="hint-pill"><i class="bi bi-exclamation-triangle"></i> Bukti pembayaran ditolak. Silahkan <b>upload ulang</b>.</span>
              @elseif($status === 'done')
                <span class="hint-pill"><i class="bi bi-check2-circle"></i> Selesai. Tidak ada aksi lanjutan.</span>
              @else
                <span class="hint-pill"><i class="bi bi-clock-history"></i> Menunggu proses admin.</span>
              @endif
            @endif
          </div>

          <div class="row g-3 align-items-center">
            <div class="col-lg-8">
              <label class="form-label mb-1">Tim Penelitian</label>
              <select class="form-select" disabled>
                @foreach ($project as $p)
                  <option value="{{ $p->id }}" {{ $p->id == $request_pembelian->id_project ? 'selected' : '' }}>
                    {{ $p->nama_project }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-lg-4">
              <label class="form-label mb-1">Tanggal Request</label>
              <input type="date" class="form-control" value="{{ $request_pembelian->tgl_request }}" disabled>
            </div>
          </div>
        </div>
      </div>

      <!-- Ubah Status (Admin) -->
      @if ($isAdmin)
        <div class="card card-soft mb-3">
          <div class="card-body">
            <form action="{{ route('requestpembelian.changestatus') }}" method="POST" class="row g-2 align-items-end">
              @csrf
              <input type="hidden" name="id_request_pembelian_header" value="{{ $request_pembelian->id }}">

              <div class="col-lg-6">
                <label class="form-label mb-1">Ubah Status</label>
                <select class="form-select" id="status_request" name="status_request" @if(count($detail)==0) disabled @endif>
                  <option value="approve_request" {{ $status=='approve_request' ? 'selected' : '' }}>Menyetujui Request</option>
                  <option value="reject_request"  {{ $status=='reject_request'  ? 'selected' : '' }}>Menolak Request</option>
                  <option value="approve_payment" {{ in_array($status,['done']) ? 'selected' : '' }}>Done (Setujui Bukti Pembayaran)</option>
                  <option value="reject_payment"  {{ $status=='reject_payment'  ? 'selected' : '' }}>Menolak Bukti Pembayaran</option>
                </select>
              </div>

              <div class="col-lg-4" id="keterangan_reject_wrap">
                <label class="form-label mb-1">Keterangan (jika menolak)</label>
                <input type="text" class="form-control" id="keterangan_reject" name="keterangan_reject"
                      value="{{ $request_pembelian->keterangan_reject }}" placeholder="Masukkan alasan penolakan">
              </div>

              <div class="col-lg-2 text-lg-end">
                <button class="btn btn-success w-100" @if(count($detail)==0) disabled @endif>
                  <i class="bi bi-check-circle me-1"></i> Submit
                </button>
              </div>
            </form>
          </div>
        </div>
      @endif

      <!-- Tabel Detail -->
      <div class="card card-soft">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th>Nama Barang</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Harga</th>
                  <th class="text-end">Total</th>
                  <th>Link Pembelian</th>
                  <th>Subkategori</th>
                  <th class="text-center">Aksi</th>
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

                    <td class="actions text-center">
                      {{-- BUKTI --}}
                      @if($d->bukti_bayar)
                        <a href="{{ asset('bukti_bayar/'.$d->bukti_bayar) }}" class="btn btn-success btn-icon" title="Lihat Bukti" target="_blank" rel="noreferrer">
                          <i class="bi bi-receipt-cutoff"></i>
                        </a>

                        @if(!$isAdmin && in_array($status, ['reject_payment','submit_payment']))
                          <a href="{{ route('requestpembelian.addbukti',$d->id) }}" class="btn btn-primary btn-icon" title="Upload Ulang Bukti">
                            <i class="bi bi-arrow-repeat"></i>
                          </a>
                        @endif
                      @else
                        @if($canUploadBukti)
                          <a href="{{ route('requestpembelian.addbukti',$d->id) }}" class="btn btn-primary btn-icon" title="{{ $status==='reject_payment' ? 'Upload Ulang Bukti' : 'Upload Bukti' }}">
                            <i class="bi bi-upload"></i>
                          </a>
                        @else
                          <button type="button" class="btn btn-outline-secondary btn-icon" disabled
                                  title="Bukti pembayaran hanya bisa diupload setelah status Approve Request">
                            <i class="bi bi-upload"></i>
                          </button>
                        @endif
                      @endif

                      {{-- EDIT/DELETE --}}
                      @if($canEditItems)
                        <a href="{{ route('requestpembelian.editdetail',$d->id) }}" class="btn btn-warning btn-icon" title="Edit">
                          <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('requestpembelian.destroydetail',$d->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-icon" title="Hapus">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      @else
                        <button type="button" class="btn btn-outline-warning btn-icon" disabled title="Item terkunci setelah disetujui admin">
                          <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-icon" disabled title="Item terkunci setelah disetujui admin">
                          <i class="bi bi-trash"></i>
                        </button>
                      @endif
                    </td>
                  </tr>
                @endforeach

                {{-- FORM TAMBAH ITEM (PENELITI, saat editable) --}}
                @if(!$isAdmin)
                  @if($canEditItems)
                    <tr>
                      <td colspan="7" class="pt-3">
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
                      <td colspan="7" class="text-center py-4">
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
              <tfoot>
                <tr>
                  <th colspan="3" class="text-end">Total Keseluruhan</th>
                  <th class="text-end">Rp {{ number_format($grandTotal,0,',','.') }}</th>
                  <th colspan="3"></th>
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
        wrap.style.display=(sel.value==='reject_request'||sel.value==='reject_payment')?'':'none';
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
