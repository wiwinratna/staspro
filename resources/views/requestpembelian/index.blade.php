{{-- resources/views/requestpembelian/index.blade.php --}}
@extends('layouts.panel')

@section('title','Request Pembelian')

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

    .tabs-title{
      margin-top:12px;
      font-weight:900;
      font-size:.84rem;
      color:var(--ink-600);
      text-transform:uppercase;
      letter-spacing:.06em;
    }
    .tabs-divider{
      height:1px;
      background:var(--line);
      margin:12px 0;
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
    .tab-btn[data-status="submit_request"]{ background:#fff7ed; border-color:#fed7aa; color:#9a3412; }
    .tab-btn[data-status="approve_request"]{ background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
    .tab-btn[data-status="reject_request"]{ background:#fef2f2; border-color:#fecaca; color:#991b1b; }

    .tab-btn[data-status="submit_payment"]{ background:#f5f3ff; border-color:#ddd6fe; color:#5b21b6; }
    .tab-btn[data-status="approve_payment"]{ background:#ecfdf5; border-color:#bbf7d0; color:#166534; }
    .tab-btn[data-status="reject_payment"]{ background:#fdf2f8; border-color:#fbcfe8; color:#9d174d; }

    .tab-btn[data-status="done"]{ background:#eefdfb; border-color:#99f6e4; color:#115e59; }

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
    .badge-submit-request { background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
    .badge-approve-request{ background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
    .badge-reject-request { background:#fef2f2; color:#991b1b; border-color:#fecaca; }

    .badge-submit-payment { background:#f5f3ff; color:#5b21b6; border-color:#ddd6fe; }
    .badge-approve-payment{ background:#ecfdf5; color:#166534; border-color:#bbf7d0; }
    .badge-reject-payment { background:#fdf2f8; color:#9d174d; border-color:#fbcfe8; }

    .badge-done{ background:#eefdfb; color:#115e59; border-color:#99f6e4; }

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
        <h1 class="title">Request Pembelian</h1>
        <p class="sub">
          @if(Auth::user()->role == 'admin')
            Daftar seluruh pengajuan pembelian beserta statusnya.
          @else
            Riwayat request pembelian milik kamu.
          @endif
        </p>
      </div>

      <div class="tools-row">
        <div class="tools-left">
          @if(Auth::user()->role != 'admin')
            <a href="{{ route('requestpembelian.create') }}" class="btn-brand">
              <i class="bi bi-plus-lg"></i> Input Request Pembelian
            </a>
          @endif
        </div>

        <div class="tools-right">
          <a
            href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
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
  @if(session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
  @endif

  <!-- WORKFLOW PANEL -->
  <div class="panel">
    <div class="panel-head">
      <div>
        <div class="fw-bold">Workflow Status</div>
        <div class="workflow-hint">
          Tahap <b>Request</b> untuk pengajuan awal, tahap <b>Payment</b> untuk proses bukti bayar.
        </div>
      </div>
    </div>

    <!-- GLOBAL -->
    <div class="status-tabs" id="statusTabsGlobal">
      <button class="tab-btn active" type="button" data-status="">
        Semua <span class="tab-count" data-count="all">0</span>
      </button>
      <button class="tab-btn" type="button" data-status="done">
        Done <span class="tab-count" data-count="done">0</span>
      </button>
    </div>

    <div class="tabs-divider"></div>

    <!-- REQUEST & PAYMENT SEJAJAR -->
    <div class="stage-grid">
      <div class="stage-box">
        <div class="tabs-title mt-0">Tahap Request</div>
        <div class="status-tabs" id="statusTabsRequest">
          <button class="tab-btn" type="button" data-status="submit_request">
            Submit Request <span class="tab-count" data-count="submit_request">0</span>
          </button>
          <button class="tab-btn" type="button" data-status="approve_request">
            Approve Request <span class="tab-count" data-count="approve_request">0</span>
          </button>
          <button class="tab-btn" type="button" data-status="reject_request">
            Reject Request <span class="tab-count" data-count="reject_request">0</span>
          </button>
        </div>
      </div>

      <div class="stage-box stage-box-right">
        <div class="tabs-title mt-0">Tahap Payment</div>
        <div class="status-tabs" id="statusTabsPayment">
          <button class="tab-btn" type="button" data-status="submit_payment">
            Submit Payment <span class="tab-count" data-count="submit_payment">0</span>
          </button>
          <button class="tab-btn" type="button" data-status="approve_payment">
            Approve Payment <span class="tab-count" data-count="approve_payment">0</span>
          </button>
          <button class="tab-btn" type="button" data-status="reject_payment">
            Reject Payment <span class="tab-count" data-count="reject_payment">0</span>
          </button>
        </div>
      </div>
    </div>
  </div>

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
              'submit_request'  => 'Submit Request',
              'approve_request' => 'Approve Request',
              'reject_request'  => 'Reject Request',
              'submit_payment'  => 'Submit Payment',
              'approve_payment' => 'Approve Payment',
              'reject_payment'  => 'Reject Payment',
              'done'            => 'Done',
            ];

            $badge = 'badge-' . str_replace('_','-',$status);
            $label = $labelMap[$status] ?? ucwords(str_replace('_',' ', $status));
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
            </td>

            <td class="text-center">
              <div class="action-btns">
                <a href="{{ route('requestpembelian.detail',$r->id) }}" class="btn btn-success btn-sm">Detail</a>

                {{-- ADMIN: bisa approve/reject REQUEST saat status submit_request --}}
                @if(Auth::user()->role === 'admin' && $status === 'submit_request')
                  <form method="POST" action="{{ route('requestpembelian.changestatus') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="id_request_pembelian_header" value="{{ $r->id }}">
                    <input type="hidden" name="status_request" value="approve_request">
                    <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                  </form>

                  <a href="{{ route('requestpembelian.detail',$r->id) }}" class="btn btn-outline-danger btn-sm">
                    Reject
                  </a>
                @endif

                {{-- BENDAHARA: bisa approve/reject PAYMENT saat status submit_payment --}}
                @if(Auth::user()->role === 'bendahara' && $status === 'submit_payment')
                  <form method="POST" action="{{ route('requestpembelian.changestatus') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="id_request_pembelian_header" value="{{ $r->id }}">
                    <input type="hidden" name="status_request" value="approve_payment">
                    <button type="submit" class="btn btn-primary btn-sm">Approve Payment</button>
                  </form>

                  <a href="{{ route('requestpembelian.detail',$r->id) }}" class="btn btn-outline-danger btn-sm">
                    Reject Payment
                  </a>
                @endif

                {{-- PENELITI: boleh delete hanya kalau masih submit_request / reject_request (opsional) --}}
                @if(Auth::user()->role === 'peneliti' && in_array($status, ['submit_request','reject_request']))
                  <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $r->id }})">Delete</button>
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
        const active = document.querySelector('.tab-btn.active');
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
          approve_request: 0,
          reject_request: 0,
          submit_payment: 0,
          approve_payment: 0,
          reject_payment: 0,
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
      document.querySelectorAll('.tab-btn').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
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
