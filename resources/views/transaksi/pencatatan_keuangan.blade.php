<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <title>Pencatatan Keuangan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root{
      --brand:#16a34a;         /* emerald-600 */
      --brand-700:#15803d;     /* emerald-700 */
      --brand-50:#ecfdf5;      /* emerald-50  */
      --ink:#0f172a;           /* slate-900   */
      --ink-600:#475569;       /* slate-600   */
      --line:#e2e8f0;          /* slate-200   */
      --bg:#f6f7fb;            /* soft background */
      --card:#ffffff;
      --danger:#dc3545;
    }

    *{ box-sizing: border-box; }
    html,body{ height:100%; }
    body{
      background: var(--bg);
      font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color: var(--ink);
    }

    /* Topbar */
    .topbar{
      background: linear-gradient(135deg, var(--brand-700), var(--brand));
      color:#fff;
    }
    .topbar .brand-title{
      font-weight: 700;
      letter-spacing:.2px;
    }

    /* Shell */
    .app{
      display:flex;
      min-height: calc(100vh - 56px);
      gap:0;
    }

    /* Sidebar */
    .sidebar{
      width:260px;
      background: var(--card);
      border-right:1px solid var(--line);
      padding:18px;
      position:sticky;
      top:0;
      height: calc(100vh - 56px);
    }
    .sidebar .menu-title{
      font-size:.8rem;
      letter-spacing:.06em;
      color: var(--ink-600);
      text-transform: uppercase;
      margin: 6px 0 10px;
      font-weight:600;
    }
    .nav-link-custom{
      display:flex; align-items:center; gap:10px;
      padding:10px 12px;
      color: var(--ink);
      border-radius:12px;
      text-decoration:none;
      transition: all .18s ease;
      font-weight:500;
    }
    .nav-link-custom:hover{
      background: var(--brand-50);
      color: var(--brand-700);
    }
    .nav-link-custom.active{
      background: var(--brand);
      color:#fff;
      box-shadow: 0 6px 16px rgba(22,163,74,.18);
    }

    /* Content */
    .content{
      flex:1;
      padding:24px;
    }

    /* Section header */
    .page-title{
      font-size:1.5rem;
      font-weight:700;
      margin-bottom:4px;
    }
    .page-sub{
      color: var(--ink-600);
      margin-bottom:18px;
    }

    /* Stat cards */
    .stat-card{
      background: linear-gradient(180deg, var(--brand), var(--brand-700));
      color:#fff;
      border:0;
      border-radius:18px;
      box-shadow: 0 10px 24px rgba(22,163,74,.18);
    }
    .stat-card .label{
      font-weight:600;
      opacity:.9;
    }
    .stat-card .value{
      font-size:1.6rem;
      font-weight:800;
      line-height:1.2;
    }

    /* Action card */
    .action-card{
      background: var(--card);
      border:1px solid var(--line);
      border-radius:18px;
    }

    /* Filters row */
    .filters{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      align-items:center;
    }

    /* Table */
    .table-wrap{
      background: var(--card);
      border:1px solid var(--line);
      border-radius:18px;
      overflow:hidden;
    }
    .table-modern{
      margin:0;
      vertical-align: middle;
    }
    .table-modern thead th{
      background:#f9fafb;
      color: var(--ink-600);
      font-weight:700;
      border-bottom:1px solid var(--line);
      position: sticky;
      top:0;
      z-index:1;
    }
    .table-modern tbody tr:hover{
      background: #fafafa;
    }
    .table-modern td, .table-modern th{
      padding:.9rem .9rem;
    }
    /* Ellipsis for long text */
    .td-ellipsis{
      max-width: 360px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Buttons */
    .btn-brand{
      background: var(--brand);
      border-color: var(--brand);
      color:#fff;
    }
    .btn-brand:hover{ background: var(--brand-700); border-color: var(--brand-700); }

    .btn-outline-brand{
      border-color: var(--brand);
      color: var(--brand-700);
    }
    .btn-outline-brand:hover{
      background: var(--brand);
      color:#fff;
    }

    /* Utilities */
    .shadow-soft{ box-shadow: 0 6px 16px rgba(15,23,42,.06); }

    /* Responsive: collapse sidebar */
    @media (max-width: 991.98px){
      .sidebar{ position:fixed; left:-280px; z-index:1040; transition:left .2s ease; }
      .sidebar.open{ left:0; }
      .content{ padding:18px; }
      .backdrop{
        display:none; position:fixed; inset:0; background:rgba(15,23,42,.38); z-index:1035;
      }
      .backdrop.show{ display:block; }
    }
    .nav-pills .nav-link {
    padding: 0.35rem 0.9rem;
    font-size: 0.85rem;
    }
    /* jaga supaya baris tidak mudah patah */
    .card .flex-nowrap { overflow-x: auto; }
    /* rapikan tinggi kontrol */
    .card .form-control-sm { line-height: 1.2; }

  </style>
</head>

<body>
  <!-- Topbar -->
  <nav class="navbar topbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <div class="brand-title">STAS-RG • Keuangan</div>
      <div class="ms-auto">
        @include('navbar')
      </div>
    </div>
  </nav>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar" aria-label="Sidebar">
      <div class="menu-title">Menu</div>
      <a class="nav-link-custom" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a class="nav-link-custom" href="{{ route('project.index') }}"><i class="bi bi-kanban"></i> Project</a>
      <a class="nav-link-custom" href="{{ route('requestpembelian.index') }}"><i class="bi bi-bag-check"></i> Request Pembelian</a>

      @if (Auth::user()->role == 'admin')
        <div class="menu-title mt-3">Administrasi</div>
        <a class="nav-link-custom" href="{{ route('sumberdana.index') }}"><i class="bi bi-cash-coin"></i> Sumber Dana</a>
        <a class="nav-link-custom active" href="{{ route('pencatatan_keuangan') }}"><i class="bi bi-journal-text"></i> Pencatatan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('laporan_keuangan') }}"><i class="bi bi-graph-up"></i> Laporan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Management User</a>
      @endif
    </aside>
    <div class="backdrop" id="backdrop"></div>

    <!-- Main -->
    <main class="content">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2">
        <div>
          <div class="page-title">Pencatatan Keuangan</div>
          <div class="page-sub">
            @if(request()->has('start_date') && request()->has('end_date'))
              @if(request('start_date') === request('end_date'))
                Total nominal transaksi pada tanggal {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}.
              @else
                Total nominal transaksi dari {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}
                sampai {{ \Carbon\Carbon::parse(request('end_date'))->locale('id')->translatedFormat('d F Y') }}.
              @endif
            @else
              Total nominal transaksi keseluruhan.
            @endif
          </div>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
      @endif

        <div class="row g-3 align-items-start mb-2">
        <!-- Kiri: Total Pemasukan -->
        <div class="col-lg-6">
            <div class="card text-center">
            <div class="card-body">
                <div class="fw-semibold">Total Pemasukan</div>
                <h3 id="total-pemasukan">Rp. 0</h3>
            </div>
            </div>
        </div>

        <!-- Kanan: Total Pengeluaran -->
        <div class="col-lg-6">
            <div class="card text-center">
            <div class="card-body">
                <div class="fw-semibold">Total Pengeluaran</div>
                <h3 id="total-pengeluaran">Rp. 0</h3>
            </div>
            </div>
        </div>
        </div>

        <!-- Tabs Filter (dibawah KPI) -->
        <div class="d-flex justify-content-center mb-4">
        <ul class="nav nav-pills nav-sm" id="filterTabs">
            <li class="nav-item"><a class="nav-link active" href="#" data-filter="semua">Semua</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-filter="pemasukan">Pemasukan</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-filter="pengeluaran">Pengeluaran</a></li>
        </ul>
        </div>

<div class="card action-card shadow-soft mt-3">
  <div class="card-body">

    <div class="row g-2 align-items-center flex-nowrap">
      <!-- kiri: filter -->
      <div class="col-auto">
        <label for="startDate" class="fw-semibold mb-0">Mulai</label>
      </div>
      <div class="col-auto">
        <input type="date" id="startDate" name="start_date"
               class="form-control form-control-sm" style="width: 150px;" form="filterForm">
      </div>
      <div class="col-auto">
        <span class="mx-1">sampai</span>
      </div>
      <div class="col-auto">
        <input type="date" id="endDate" name="end_date"
               class="form-control form-control-sm" style="width: 150px;" form="filterForm">
      </div>
      <div class="col-auto">
        <a href="{{ route('pencatatan_keuangan') }}"
           class="btn btn-outline-secondary btn-sm d-flex align-items-center"
           data-bs-toggle="tooltip" title="Reset Filter">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>
      </div>

      <!-- kanan: tombol tambah, terdorong ke pojok -->
      <div class="col ms-auto text-end">
        <button type="button" class="btn btn-success btn-sm shadow-soft"
                onclick="window.location.href='/form_input_pencatatan_keuangan'">
          <i class="bi bi-plus-lg me-1"></i> Tambah Pencatatan Keuangan
        </button>
      </div>
    </div>

    <!-- form GET (boleh taruh di mana saja di card ini) -->
    <form id="filterForm" method="GET" action="{{ route('filter_pencatatan_keuangan') }}"></form>

  </div>
</div>


      <!-- Table -->
      <div class="table-wrap shadow-soft mt-3">
        <div class="table-responsive">
          <table class="table table-modern table-striped align-middle">
            <thead>
              <tr>
                <th style="width:56px">No.</th>
                <th style="min-width:140px">Tanggal</th>
                <th style="min-width:160px">Tim Peneliti</th>
                <th style="min-width:220px">Sub Kategori Pendanaan</th>
                <th style="min-width:260px">Deskripsi</th>
                <th class="text-end" style="min-width:140px">Jumlah</th>
                <th style="min-width:160px">Metode Pembayaran</th>
                <th style="min-width:110px">Bukti</th>
                <th style="min-width:120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pencatatanKeuangans as $index => $transaksi)
                <tr data-jenis="{{ strtolower($transaksi->jenis_transaksi ?? 'pengeluaran') }}">
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                  <td class="td-ellipsis" title="{{ $transaksi->project->nama_project ?? 'Tidak Ada' }}">
                    {{ $transaksi->project->nama_project ?? 'Tidak Ada' }}
                  </td>
                  <td class="td-ellipsis" title="{{ $transaksi->subKategoriPendanaan->nama ?? 'Tidak Ada' }}">
                    {{ $transaksi->subKategoriPendanaan->nama ?? 'Tidak Ada' }}
                  </td>
                  <td class="td-ellipsis" title="{{ $transaksi->deskripsi_transaksi }}">
                    {{ $transaksi->deskripsi_transaksi }}
                  </td>
                  <td class="text-end">Rp. {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}</td>
                  <td>{{ strtoupper($transaksi->metode_pembayaran) }}</td>
                  <td>
                    @if($transaksi->bukti_transaksi)
                      <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modalBukti{{ $transaksi->id }}">
                        Lihat Bukti
                      </button>
                      <!-- Modal Bukti -->
                      <div class="modal fade" id="modalBukti{{ $transaksi->id }}" tabindex="-1" aria-labelledby="modalBuktiLabel{{ $transaksi->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="modalBuktiLabel{{ $transaksi->id }}">Bukti Transaksi</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                              <img src="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" alt="Bukti Transaksi" class="img-fluid rounded shadow-sm mb-3">
                              <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" class="btn btn-brand" download>
                                <i class="bi bi-download me-1"></i> Unduh Bukti
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="{{ route('pencatatan_keuangan.edit', $transaksi->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                      </a>
                      <form action="{{ route('pencatatan_keuangan.destroy', $transaksi->id) }}" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $transaksi->id }})" data-bs-toggle="tooltip" title="Hapus">
                          <i class="bi bi-trash-fill"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- Toast -->
      <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="notifToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body" id="notifMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');

    function closeSidebar(){ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    function openSidebar(){ sidebar.classList.add('open'); backdrop.classList.add('show'); }

    toggleBtn && toggleBtn.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop && backdrop.addEventListener('click', closeSidebar);

    // Toast notif (tetap sama)
    document.addEventListener("DOMContentLoaded", function () {
      let notif = localStorage.getItem("notif");
      if (notif) {
        document.getElementById("notifMessage").textContent = notif;
        let toast = new bootstrap.Toast(document.getElementById("notifToast"));
        toast.show();
        localStorage.removeItem("notif");
      }
    });

    // SweetAlert Delete (tetap sama)
    function confirmDelete(transaksiId) {
      Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data pencatatan keuangan akan dihapus secara permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/pencatatan_keuangan/${transaksiId}`, {
            method: "DELETE",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
              "Content-Type": "application/json",
              "Accept": "application/json"
            }
          })
          .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
          })
          .then(data => {
            if (data.success) {
              Swal.fire({ title:"Berhasil!", text:data.message, icon:"success", timer:1800, showConfirmButton:false });
              setTimeout(()=> location.reload(), 1800);
            } else {
              Swal.fire({ title:"Gagal!", text:data.message || "Terjadi kesalahan saat menghapus data", icon:"error" });
            }
          })
          .catch(error => {
            Swal.fire({ title:"Gagal!", text:"Terjadi kesalahan pada server: " + error.message, icon:"error" });
          });
        }
      });
    }

    // Filter tanggal (auto submit & batasan)
    document.addEventListener("DOMContentLoaded", function () {
      const startDateInput = document.getElementById("startDate");
      const endDateInput = document.getElementById("endDate");
      const filterForm = document.getElementById("filterForm");

      const today = new Date();
      const todayString = today.toISOString().split('T')[0];
      startDateInput.setAttribute('max', todayString);
      endDateInput.setAttribute('max', todayString);

      startDateInput.addEventListener("change", function () {
        if (startDateInput.value) endDateInput.setAttribute('min', startDateInput.value);
        if (startDateInput.value && endDateInput.value) filterForm.submit();
      });
      endDateInput.addEventListener("change", function () {
        if (startDateInput.value && endDateInput.value) filterForm.submit();
      });
    });

    // (Opsional) Hook tombol cepat – sesuaikan handler server-side kalau sudah ada
    document.getElementById("btn-filter-semua")?.addEventListener("click", ()=> window.location.href = "{{ route('pencatatan_keuangan') }}");
    // Untuk btn-filter-pemasukan & btn-filter-pengeluaran, arahkan ke route yang sudah kamu pakai:
    // document.getElementById("btn-filter-pemasukan")?.addEventListener("click", ()=> window.location.href = "{{ route('pencatatan_keuangan', ['type' => 'income']) }}");
    // document.getElementById("btn-filter-pengeluaran")?.addEventListener("click", ()=> window.location.href = "{{ route('pencatatan_keuangan', ['type' => 'expense']) }}");
  </script>

    <script>
    (function(){
    const rows = document.querySelectorAll('table.table tbody tr');
    const tabs = document.querySelectorAll('#filterTabs [data-filter]');
    const elIn  = document.getElementById('total-pemasukan');
    const elOut = document.getElementById('total-pengeluaran');
    const caption = document.getElementById('caption-keseluruhan');

    const num = s => Number((s||'').replace(/[^0-9]/g,''));
    const rup = n => new Intl.NumberFormat('id-ID').format(n||0);

    function recompute(){
        let tIn=0, tOut=0;
        rows.forEach(tr=>{
        if (tr.style.display==='none') return;
        const jenis = (tr.dataset.jenis||'').toLowerCase();
        const val = num((tr.querySelector('td:nth-child(6)')||{}).textContent);
        if (jenis==='pemasukan') tIn+=val; else tOut+=val;
        });
        elIn.textContent  = 'Rp. ' + rup(tIn);
        elOut.textContent = 'Rp. ' + rup(tOut);
    }

    function applyFilter(filter){
        tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.filter===filter));
        rows.forEach(tr=>{
        const j = (tr.dataset.jenis||'').toLowerCase();
        tr.style.display = (filter==='semua' || j===filter) ? '' : 'none';
        });
        if (caption) {
        caption.textContent = "Total nominal transaksi " + (filter==='semua' ? "keseluruhan" : filter);
        }
        recompute();
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', e=>{
        e.preventDefault();
        applyFilter(tab.dataset.filter);
        });
    });

    applyFilter('semua'); // default
    })();
    </script>

</body>
</html>
