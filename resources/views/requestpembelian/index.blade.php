{{-- resources/views/requestpembelian/index.blade.php --}}
@extends('layouts.panel')

@section('title','Daftar Pengajuan Komponen')

@push('styles')
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">

  <style>
    :root{
      --brand:#16a34a;
      --brand-700:#15803d;
      --brand-50:#ecfdf5;

      --ink:#0f172a;
      --ink-600:#475569;
      --line:#e2e8f0;

      --bg:#f6f7fb;
      --card:#ffffff;

      --danger:#ef4444;

      --shadow:0 10px 30px rgba(15,23,42,.08);
      --shadow2:0 18px 40px rgba(15,23,42,.10);
    }

    /* HERO ala dashboard */
    .hero{
      border-radius:22px;
      padding:18px;
      background:
        radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
        radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
        linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
      border:1px solid rgba(226,232,240,.95);
      box-shadow:var(--shadow);
      position:relative;
      overflow:hidden;
      margin-bottom:14px;
    }
    .hero::after{
      content:"";
      position:absolute; inset:-1px;
      background:
        radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
        radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
      pointer-events:none;
      opacity:.65;
    }
    .hero-inner{ position:relative; z-index:2; width:100%; }

    .hero-left .title{
      font-size:1.65rem;
      font-weight:800;
      margin:0;
      letter-spacing:-.2px;
    }
    .hero-left .sub{
      margin:6px 0 0;
      color:var(--ink-600);
      font-weight:500;
    }

    .tools-row{
      margin-top:14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }
    .tools-left{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .tools-right{
      margin-left:auto;
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }

    .btn-brand{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
      color:#fff;
      white-space:nowrap;
      text-decoration:none;
    }
    .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }
    .btn-brand i{ line-height:1; }

    .btn-soft{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:#fff;
      color:var(--ink);
      border:1px solid rgba(226,232,240,.95);
      box-shadow:0 10px 26px rgba(15,23,42,.05);
      white-space:nowrap;
      text-decoration:none;
    }
    .btn-soft:hover{
      background:var(--brand-50);
      transform:translateY(-1px);
      color:var(--brand-700);
      border-color:rgba(226,232,240,.95);
    }

    /* Panel workflow */
    .panel{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      padding:16px;
      margin-top:14px;
      box-shadow:var(--shadow);
    }
    .panel-head{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }
    .workflow-hint{
      color:var(--ink-600);
      font-size:.88rem;
      font-weight:500;
    }

    /* Tabs */
    .status-tabs{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      margin-top:12px;
    }
    .tab-btn{
      border:1px solid var(--line);
      background:#fff;
      color:var(--ink-600);
      border-radius:999px;
      padding:.48rem .85rem;
      font-weight:800;
      font-size:.84rem;
      display:inline-flex;
      align-items:center;
      gap:10px;
      transition:.15s;
      user-select:none;
      cursor:pointer;
      line-height:1;
    }
    .tab-btn:hover{ transform:translateY(-1px); }

    .tab-count{
      font-size:.75rem;
      padding:.12rem .5rem;
      border-radius:999px;
      border:1px solid rgba(15,23,42,.08);
      background:rgba(15,23,42,.06);
      color:#334155;
      font-weight:800;
      min-width:26px;
      text-align:center;
    }

    /* Status filter pills – clean, readable */
    .status-pill{
      display:inline-flex;
      align-items:center;
      gap:7px;
      padding:7px 14px;
      border-radius:999px;
      border:1px solid #e2e8f0;
      background:#fff;
      color:#334155;
      font-weight:700;
      font-size:.82rem;
      line-height:1;
      cursor:pointer;
      transition:all .15s ease;
      white-space:nowrap;
      --pill-color:#64748b;
    }
    .status-pill:hover{
      border-color:var(--pill-color);
      background:color-mix(in srgb, var(--pill-color) 6%, #fff);
      transform:translateY(-1px);
    }
    .status-pill.active{
      background:var(--pill-color);
      border-color:var(--pill-color);
      color:#fff;
      box-shadow:0 4px 12px color-mix(in srgb, var(--pill-color) 35%, transparent);
    }
    .status-pill.active .pill-dot{ background:#fff; }
    .status-pill.active .pill-count{ background:rgba(255,255,255,.25); color:#fff; }

    .pill-dot{
      width:8px; height:8px;
      border-radius:50%;
      background:var(--pill-color);
      flex-shrink:0;
    }
    .pill-label{ letter-spacing:-.01em; }
    .pill-count{
      font-size:.7rem;
      font-weight:800;
      min-width:22px;
      text-align:center;
      padding:2px 6px;
      border-radius:999px;
      background:#f1f5f9;
      color:#475569;
    }

    .tabs-title{
      margin-top:12px;
      font-weight:900;
      font-size:.84rem;
      color:var(--ink-600);
      text-transform:uppercase;
      letter-spacing:.06em;
    }

    /* Stage: Request & Payment sejajar */
    .stage-grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:14px;
      margin-top:10px;
    }
    .stage-box{
      background:#fff;
      border:1px solid rgba(226,232,240,.95);
      border-radius:18px;
      padding:12px 12px 10px;
      box-shadow:0 10px 26px rgba(15,23,42,.05);
    }
    .stage-box-right{ position:relative; }
    .stage-box-right:before{
      content:"";
      position:absolute;
      left:-7px;
      top:10px;
      bottom:10px;
      width:1px;
      background:var(--line);
    }
    .stage-box .status-tabs{
      margin-top:6px;
      padding-bottom:4px;
    }

    /* Colors per workflow step */
    .tab-btn[data-status=""]{
      background:#ffffff;
      border-color:#e2e8f0;
      color:#0f172a;
    }
    .tab-btn[data-status="draft"]{ background:#f1f5f9; border-color:#cbd5e1; color:#475569; }
    .tab-btn[data-status="submit_request"]{ background:#fff7ed; border-color:#fed7aa; color:#9a3412; }
    .tab-btn[data-status="approve_request"]{ background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
    .tab-btn[data-status="reject_request"]{ background:#fef2f2; border-color:#fecaca; color:#991b1b; }
    .tab-btn[data-status="cancel"]{ background:#fef2f2; border-color:#d1d5db; color:#6b7280; }
    .tab-btn[data-status="done"]{ background:#eefdfb; border-color:#99f6e4; color:#115e59; }
    /* legacy compat */
    .tab-btn[data-status="submit_payment"]{ background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
    .tab-btn[data-status="approve_payment"]{ background:#eefdfb; border-color:#99f6e4; color:#115e59; }
    .tab-btn[data-status="reject_payment"]{ background:#fef2f2; border-color:#fecaca; color:#991b1b; }

    .tab-btn.active{
      box-shadow:0 10px 22px rgba(15,23,42,.10);
      outline:3px solid rgba(22,163,74,.12);
    }

    /* Table */
    .table-wrap{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      overflow:hidden;
      margin-top:12px;
      box-shadow:var(--shadow);
    }
    .table-responsive{
      max-height:68vh;
      overflow-y:auto;
      overflow-x:auto; /* ✅ biar tabel ga dorong layout */
    }

    .table-modern{ margin:0; font-size:.92rem; border-collapse:separate; border-spacing:0; }
    .table-modern thead th{
      background:#f8fafc;
      color:var(--ink-600);
      font-weight:900;
      text-transform:uppercase;
      font-size:.72rem;
      letter-spacing:.08em;
      padding:14px 12px;
      border-bottom:1px solid rgba(226,232,240,.95);
      position:sticky;
      top:0;
      z-index:5;
      white-space:nowrap;
    }
    .table-modern tbody td{
      padding:14px 12px;
      vertical-align:middle;
      border-top:1px solid #eef2f7;
      font-weight:500;
      white-space:nowrap;
    }
    .table-striped > tbody > tr:nth-of-type(odd){ background:#fcfcfd; }
    .table-modern tbody tr:hover{ background:var(--brand-50); transition:.12s; }

    /* Nama Barang clamp 2 lines */
    .col-item{
      max-width:520px;
      overflow:hidden;
      display:-webkit-box;
      -webkit-line-clamp:2;
      -webkit-box-orient:vertical;
      word-break:break-word;
      line-height:1.25rem;
      white-space:normal;
    }

    /* Badge status */
    .badge-status{
      font-size:.72rem;
      font-weight:900;
      padding:.42rem .72rem;
      border-radius:999px;
      white-space:nowrap;
      border:1px solid transparent;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:6px;
    }
    .badge-draft { background:#f1f5f9; color:#475569; border-color:#cbd5e1; }
    .badge-submit-request { background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
    .badge-approve-request{ background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
    .badge-reject-request { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
    .badge-cancel { background:#fef2f2; color:#6b7280; border-color:#d1d5db; }
    .badge-done{ background:#eefdfb; color:#115e59; border-color:#99f6e4; }
    /* legacy compat */
    .badge-submit-payment { background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
    .badge-approve-payment{ background:#eefdfb; color:#115e59; border-color:#99f6e4; }
    .badge-reject-payment { background:#fef2f2; color:#991b1b; border-color:#fecaca; }

    /* Action buttons */
    .action-btns{ display:flex; justify-content:center; gap:6px; flex-wrap:wrap; }
    .action-btns .btn{ padding:.32rem .6rem; font-size:.78rem; border-radius:10px; white-space:nowrap; font-weight:800; }

    @media(max-width:991px){
      .col-item{ max-width:260px; }
      .stage-grid{ grid-template-columns: 1fr; }
      .stage-box-right:before{ display:none; }
    }
  </style>
@endpush

@section('content')

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-left">
        <h1 class="title">Daftar Pengajuan Komponen</h1>
        <p class="sub">
          @if(Auth::user()->role == 'admin')
            Daftar seluruh pengajuan komponen beserta statusnya.
          @else
            Riwayat pengajuan komponen milik kamu.
          @endif
        </p>
      </div>

      <div class="tools-row">
        <div class="tools-left">
          @if(in_array(Auth::user()->role, ['admin','peneliti']))
            <a href="{{ route('requestpembelian.create') }}" class="btn-brand">
              <i class="bi bi-plus-lg"></i> Create Pengajuan Komponen
            </a>
          @endif
        </div>

        <div class="tools-right">
          <a
            href="https://drive.google.com/file/d/1HKaZH2I-Ohq7S-SBb8ADMHMd3htU0nio/view?usp=sharing"
            target="_blank"
            rel="noopener"
            class="btn-soft"
            title="Buka Manual Book"
          >
            <i class="bi bi-journal-bookmark"></i> Manual Book
          </a>
        </div>
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
  @endif
  @if(session('info'))
    <div class="alert alert-info mt-3">{{ session('info') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
  @endif

  @if(Auth::user()->role !== 'peneliti')
    <!-- WORKFLOW PANEL (CLEAN) -->
    <div class="card card-soft mb-4 border-0 shadow-sm" style="border-radius:18px;">
      <div class="card-body p-4">
        <div class="fw-bold fs-6 mb-3 text-dark"><i class="bi bi-funnel-fill me-2 text-primary"></i>Filter Status Pengajuan</div>
        
        <div class="d-flex flex-wrap gap-2" id="statusTabsGlobal">
          <button class="status-pill active" type="button" data-status="">
            <span class="pill-label">Semua</span>
            <span class="pill-count" data-count="all">0</span>
          </button>
          <button class="status-pill" type="button" data-status="submit_request" style="--pill-color:#3b82f6;">
            <span class="pill-dot"></span>
            <span class="pill-label">Dalam Pemesanan</span>
            <span class="pill-count" data-count="submit_request">0</span>
          </button>
          <button class="status-pill" type="button" data-status="approve_request" style="--pill-color:#06b6d4;">
            <span class="pill-dot"></span>
            <span class="pill-label">Dalam Pembelian</span>
            <span class="pill-count" data-count="approve_request">0</span>
          </button>
          <button class="status-pill" type="button" data-status="done" style="--pill-color:#16a34a;">
            <span class="pill-dot"></span>
            <span class="pill-label">Selesai</span>
            <span class="pill-count" data-count="done">0</span>
          </button>
          <button class="status-pill" type="button" data-status="reject_request" style="--pill-color:#ef4444;">
            <span class="pill-dot"></span>
            <span class="pill-label">Ditolak</span>
            <span class="pill-count" data-count="reject_request">0</span>
          </button>
          <button class="status-pill" type="button" data-status="cancel" style="--pill-color:#6b7280;">
            <span class="pill-dot"></span>
            <span class="pill-label">Dibatalkan</span>
            <span class="pill-count" data-count="cancel">0</span>
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- TABLE -->
  <div class="table-wrap">
    <div class="table-responsive">
      <table id="table" class="table table-modern table-striped align-middle w-100">

        <thead>
          <tr>
            <th class="text-center" style="min-width:150px">Nomor Request</th>
            <th class="text-start" style="min-width:190px">Tim Penelitian</th>
            <th class="text-start" style="min-width:360px">Nama Barang</th>
            <th class="text-end" style="min-width:150px">Total Harga</th>
            <th class="text-center" style="min-width:160px">Status</th>
            <th class="text-center" style="min-width:160px">Aksi</th>
          </tr>
        </thead>

        <tbody>
        @foreach($request_pembelian as $r)
          @php
            $rawStatus = $r->status_request ?? '';
            $status = strtolower(trim($rawStatus));
            $status = str_replace(' ', '_', $status);

            $labelMap = [
              'draft'           => 'Draft',
              'submit_request'  => 'Dalam Pemesanan',
              'approve_request' => 'Dalam Pembelian',
              'reject_request'  => 'Ditolak',
              'cancel'          => 'Dibatalkan',
              'done'            => 'Selesai',
              // legacy compat
              'submit_payment'  => 'Dalam Pembelian',
              'approve_payment' => 'Selesai',
              'reject_payment'  => 'Ditolak',
            ];

            $badge = 'badge-' . str_replace('_','-',$status);
            $labelInternal = $labelMap[$status] ?? ucwords(str_replace('_',' ', $status));
            $label = (Auth::user()->role === 'peneliti' && $status !== 'draft')
              ? ($status === 'done' ? 'Selesai (Sudah Sampai)' : ($status === 'reject_request' ? 'Ditolak' : ($status === 'cancel' ? 'Dibatalkan' : 'Dalam Proses')))
              : $labelInternal;
          @endphp

          <tr data-status="{{ $status }}">
            <td class="text-center">{{ $r->no_request }}</td>
            <td class="text-start">{{ $r->nama_project }}</td>

            <td class="text-start">
              <div class="col-item" title="{{ $r->nama_barang }}">
                {{ $r->nama_barang }}
              </div>
            </td>

            <td class="text-end">Rp {{ number_format($r->total_harga,0,',','.') }}</td>

            <td class="text-center">
              <span class="badge badge-status {{ $badge }}">{{ $label }}</span>
              @if(!empty(data_get($r, 'is_talangan')))
                <div class="mt-1" style="font-size: 0.7rem; color: #64748b; font-weight: 600;">
                  <i class="bi bi-arrow-return-right me-1"></i>
                  {{ (data_get($r, 'status_alokasi') ?? 'belum') === 'sudah' ? 'Talangan Dialokasikan' : 'Talangan Belum Alokasi' }}
                </div>
              @endif
            </td>

            <td class="text-center">
              <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('requestpembelian.detail',$r->id) }}" class="btn btn-outline-success btn-sm rounded-pill fw-bold px-4 py-1 shadow-sm" style="font-size:0.8rem; border-color: rgba(22,163,74, 0.4);">
                  Detail
                </a>

                {{-- PENELITI: boleh delete hanya kalau masih submit_request / reject_request (opsional) --}}
                @if(Auth::user()->role === 'peneliti' && in_array($status, ['submit_request','reject_request']))
                  <button class="btn btn-outline-danger btn-sm px-3 shadow-sm rounded-pill fw-bold" onclick="confirmDelete({{ $r->id }})">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>

      </table>
    </div>
  </div>

@endsection

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    let dt;

    $(function(){
      dt = new DataTable('#table',{
        paging:true, searching:true, ordering:true, info:true,
        language:{
          search:"Cari:",
          lengthMenu:"Tampil _MENU_ data",
          info:"Menampilkan _START_–_END_ dari _TOTAL_ data",
          paginate:{previous:"‹",next:"›"}
        },
        columnDefs:[
          { targets:[0,4,5], className:'text-center' },
          { targets:[3], className:'text-end' },
          { targets:[1,2], className:'text-start' },
        ]
      });

      // Filter by active tab (active bisa dari group mana pun)
      $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
        const active = document.querySelector('.status-pill.active');
        const targetStatus = active ? active.getAttribute('data-status') : '';
        if(!targetStatus) return true;

        const rowNode = dt.row(dataIndex).node();
        const rowStatus = rowNode?.getAttribute('data-status') || '';
        return rowStatus === targetStatus;
      });

      function updateCounts(){
        const counts = {
          all: 0,
          submit_request: 0,
          draft: 0,
          approve_request: 0,
          reject_request: 0,
          cancel: 0,
          done: 0,
        };

        // TOTAL asli (tidak terpengaruh search box)
        dt.rows({ search: 'none' }).every(function(){
          const node = this.node();
          const st = node?.getAttribute('data-status') || '';
          counts.all++;
          if (counts[st] !== undefined) counts[st]++;
        });

        Object.keys(counts).forEach(k=>{
          const el = document.querySelector(`[data-count="${k}"]`);
          if(el) el.textContent = counts[k];
        });
      }

      updateCounts();
      dt.on('draw', updateCounts);

      // Click tab => aktifin satu tab global (bukan per group)
      document.querySelectorAll('.status-pill').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          document.querySelectorAll('.status-pill').forEach(b=>b.classList.remove('active'));
          btn.classList.add('active');
          dt.draw();
        });
      });
    });

    function confirmDelete(id){
      Swal.fire({
        title:'Yakin ingin menghapus?',
        text:'Data tidak dapat dikembalikan!',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        cancelButtonText:'Batal',
        confirmButtonText:'Ya, hapus!'
      }).then((r)=>{
        if(r.isConfirmed){
          const f=document.createElement('form');
          f.method='POST';
          f.action='/requestpembelian/destroy/'+id;
          const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          f.innerHTML=`<input type="hidden" name="_token" value="${csrf}">
                       <input type="hidden" name="_method" value="DELETE">`;
          document.body.appendChild(f);
          f.submit();
        }
      });
    }
  </script>
@endpush
