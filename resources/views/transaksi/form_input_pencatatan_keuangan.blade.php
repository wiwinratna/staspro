<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ isset($pencatatanKeuangan) ? 'Edit Pencatatan Keuangan' : 'Tambah Pencatatan Keuangan' }}</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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

      --shadow:0 10px 30px rgba(15,23,42,.08);
      --shadow2:0 18px 40px rgba(15,23,42,.10);

      --danger:#ef4444;
    }

    *{ box-sizing:border-box }
    body{
      margin:0;
      background:var(--bg);
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--ink);
    }

    /* Topbar */
    .topbar{
      position:sticky; top:0; z-index:1030;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      border-bottom:1px solid rgba(255,255,255,.18);
      height:56px;
    }
    .brand{
      display:flex;align-items:center;gap:10px;
      font-weight:800;
      letter-spacing:.2px;
    }
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
      width:260px;
      background:var(--card);
      border-right:1px solid var(--line);
      padding:14px;
      position:sticky; top:56px;
      height:calc(100vh - 56px);
      overflow:auto;
    }
    .menu-title{
      font-size:.72rem;
      letter-spacing:.08em;
      color:var(--ink-600);
      text-transform:uppercase;
      margin:8px 0;
      font-weight:700;
    }
    .nav-link-custom{
      display:flex; align-items:center; gap:10px;
      padding:9px 10px;
      border-radius:14px;
      text-decoration:none;
      color:var(--ink);
      font-weight:600;
      font-size:.92rem;
      line-height:1;
      transition:.18s;
      white-space:nowrap;
    }
    .nav-link-custom i{ font-size:1.05rem; }
    .nav-link-custom:hover{
      background:var(--brand-50);
      color:var(--brand-700);
      transform:translateX(2px);
    }
    .nav-link-custom.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      box-shadow:0 16px 28px rgba(2,6,23,.12);
      font-weight:700;
    }

    .content{ flex:1; padding:18px 18px 22px; }

    /* HERO */
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

    /* Form card */
    .form-wrap{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    .form-head{
      padding:14px 16px;
      border-bottom:1px solid rgba(226,232,240,.95);
      background:#fff;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      flex-wrap:wrap;
    }
    .form-head .head-title{
      font-weight:900;
      letter-spacing:.2px;
    }
    .form-body{ padding:16px; }

    .label{
      font-weight:900;
      font-size:.78rem;
      letter-spacing:.08em;
      text-transform:uppercase;
      color:var(--ink-600);
      margin-bottom:6px;
    }
    .req{ color:var(--danger); font-weight:900; }

    .form-control, .form-select{
      border-radius:14px;
      border:1px solid rgba(226,232,240,.95);
      padding:.7rem .9rem;
      font-weight:600;
    }
    .form-control:focus, .form-select:focus{
      border-color: rgba(22,163,74,.45);
      box-shadow: 0 0 0 .25rem rgba(22,163,74,.12);
    }

    .help{
      color:var(--ink-600);
      font-weight:600;
      font-size:.84rem;
      margin-top:6px;
    }

    .actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      margin-top:10px;
    }

    /* Validation */
    .error-border { border: 2px solid #dc3545 !important; }
    .error-message { color:#dc3545; font-size:.85rem; margin-top:6px; font-weight:700; }

    /* Mobile */
    .backdrop{
      display:none;
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.38);
      z-index:1035;
    }
    .backdrop.show{ display:block; }

    @media(max-width:991px){
      .sidebar{
        position:fixed;
        left:-290px;
        top:56px;
        height:calc(100vh - 56px);
        z-index:1040;
        transition:left .2s;
      }
      .sidebar.open{ left:0; }
      .content{ padding:14px; }
    }
  </style>
</head>

<body>

<!-- TOPBAR -->
<nav class="navbar topbar">
  <div class="container-fluid">
    <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle">
      <i class="bi bi-list"></i>
    </button>

    <div class="brand">
      <span>STAS-RG</span>
      <span class="brand-badge">{{ Auth::user()->role === 'admin' ? 'ADMIN' : 'PENELITI' }}</span>
    </div>

    <div class="ms-auto">@include('navbar')</div>
  </div>
</nav>

<div class="app">

  <!-- SIDEBAR -->
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

    @if(Auth::user()->role == 'admin')
      <div class="menu-title mt-3">Administrasi</div>

      <a class="nav-link-custom {{ request()->routeIs('sumberdana.*') ? 'active' : '' }}" href="{{ route('sumberdana.index') }}">
        <i class="bi bi-cash-coin"></i> Sumber Dana
      </a>

      <a class="nav-link-custom {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
        <i class="bi bi-wallet2"></i> Kas
      </a>

      <a class="nav-link-custom active" href="{{ route('pencatatan_keuangan') }}">
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

  <!-- CONTENT -->
  <main class="content">

    <!-- HERO -->
    <section class="hero">
      <div class="hero-inner">
        <div class="hero-left">
          <h1 class="title">
            {{ isset($pencatatanKeuangan) ? 'Edit Pencatatan Keuangan' : 'Tambah Pencatatan Keuangan' }}
          </h1>
          <p class="sub">Lengkapi transaksi sesuai tim project dan sub kategori pendanaan.</p>
        </div>

        <div class="tools-row">
          <a href="{{ route('pencatatan_keuangan') }}" class="btn btn-soft">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
          </a>
        </div>
      </div>
    </section>

    @if ($errors->any())
      <div class="alert alert-danger mt-3">
        <div class="fw-bold mb-2">Ada input yang belum valid:</div>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- FORM -->
    <div class="form-wrap">
      <div class="form-head">
        <div class="head-title">
          <i class="bi bi-ui-checks-grid me-1"></i>
          {{ isset($pencatatanKeuangan) ? 'Form Edit' : 'Form Tambah' }}
        </div>

        @if (isset($pencatatanKeuangan) && $pencatatanKeuangan->bukti_transaksi)
          <button type="button" class="btn btn-soft" data-bs-toggle="modal" data-bs-target="#modalBuktiTransaksi">
            <i class="bi bi-paperclip"></i> Lihat Bukti Sebelumnya
          </button>
        @endif
      </div>

      <div class="form-body">
        <form id="formTransaksi"
              method="POST"
              action="{{ isset($pencatatanKeuangan) ? route('pencatatan_keuangan.update', $pencatatanKeuangan->id) : route('pencatatan_keuangan.store') }}"
              enctype="multipart/form-data">
          @csrf
          @if(isset($pencatatanKeuangan)) @method('PUT') @endif

          <div class="row g-3">

            <div class="col-md-6">
              <div class="label">Tanggal Transaksi <span class="req">*</span></div>
              <input type="text" id="tanggal" name="tanggal" class="form-control"
                placeholder="Pilih tanggal"
                value="{{ old('tanggal', isset($pencatatanKeuangan) ? \Carbon\Carbon::parse($pencatatanKeuangan->tanggal)->format('d-m-Y') : '') }}">
            </div>

            <div class="col-md-6">
              <div class="label">Tim Project <span class="req">*</span></div>
              <select id="project" name="project" class="form-select">
                <option value="" disabled {{ !isset($pencatatanKeuangan) ? 'selected' : '' }}>Pilih Tim Project</option>
                @foreach ($projects as $project)
                  <option value="{{ $project->id }}"
                    {{ old('project', $pencatatanKeuangan->project_id ?? '') == $project->id ? 'selected' : '' }}>
                    {{ $project->nama_project }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <div class="label">Sub Kategori Pendanaan <span class="req">*</span></div>
              <select id="subkategori_sumberdana" name="subkategori_sumberdana" class="form-select" disabled>
                <option value="" disabled selected>Pilih Sub Kategori Pendanaan</option>
              </select>
              <div class="help">Sub kategori akan muncul setelah memilih Tim Project.</div>
            </div>

            <div class="col-md-6">
              <div class="label">Jenis Transaksi <span class="req">*</span></div>
              <select id="jenis_transaksi" name="jenis_transaksi" class="form-select">
                <option value="" disabled {{ old('jenis_transaksi', $pencatatanKeuangan->jenis_transaksi ?? '')=='' ? 'selected':'' }}>Pilih jenis transaksi</option>
                <option value="pemasukan"   {{ old('jenis_transaksi', $pencatatanKeuangan->jenis_transaksi ?? '') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ old('jenis_transaksi', $pencatatanKeuangan->jenis_transaksi ?? '') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
              </select>
            </div>

            <div class="col-12">
              <div class="label">Deskripsi Transaksi <span class="req">*</span></div>
              <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                placeholder="Contoh: Pembelian ATK, transport, honor, dll.">{{ old('deskripsi', $pencatatanKeuangan->deskripsi_transaksi ?? '') }}</textarea>
            </div>

            <div class="col-md-6">
              <div class="label">Nominal <span class="req">*</span></div>
              <input type="text" id="jumlah_transaksi" name="jumlah_transaksi" class="form-control rupiah" inputmode="numeric"
                placeholder="Cth: Rp. 1.000.000" value="{{ old('jumlah_transaksi', $pencatatanKeuangan->jumlah_transaksi ?? '') }}">
              <div class="help">Nominal diformat otomatis ke Rupiah.</div>
            </div>

            <div class="col-md-6">
              <div class="label">Metode Pembayaran <span class="req">*</span></div>
              <select id="metode_pembayaran" name="metode_pembayaran" class="form-select">
                <option value="" disabled {{ old('metode_pembayaran', $pencatatanKeuangan->metode_pembayaran ?? '')=='' ? 'selected':'' }}>Pilih metode pembayaran</option>
                <option value="cash"          {{ old('metode_pembayaran', $pencatatanKeuangan->metode_pembayaran ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="transfer bank" {{ old('metode_pembayaran', $pencatatanKeuangan->metode_pembayaran ?? '') == 'transfer bank' ? 'selected' : '' }}>Transfer Bank</option>
              </select>
            </div>

            <div class="col-12">
              <div class="label">Bukti Transaksi</div>
              <input type="file" class="form-control" id="bukti_transaksi" name="bukti_transaksi" accept=".jpg,.jpeg,.png,.pdf">
              <div class="help">Format: JPG/PNG/PDF. @if(isset($pencatatanKeuangan) && $pencatatanKeuangan->bukti_transaksi) Kosongkan jika tidak ingin mengganti. @endif</div>
            </div>

            <div class="col-12">
              <div class="actions">
                <button class="btn btn-brand" type="submit">
                  <i class="bi bi-check2-circle"></i>
                  {{ isset($pencatatanKeuangan) ? 'Update' : 'Simpan' }}
                </button>

                <a href="{{ route('pencatatan_keuangan') }}" class="btn btn-soft">
                  <i class="bi bi-x-lg"></i> Batal
                </a>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>

    {{-- Modal bukti transaksi --}}
    @if (isset($pencatatanKeuangan) && $pencatatanKeuangan->bukti_transaksi)
      @php
        $path = $pencatatanKeuangan->bukti_transaksi;
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $url  = asset('storage/' . $path);
      @endphp

      <div class="modal fade" id="modalBuktiTransaksi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Bukti Transaksi Sebelumnya</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
              @if(in_array($ext, ['jpg','jpeg','png','webp']))
                <img src="{{ $url }}" alt="Bukti Transaksi" class="img-fluid rounded shadow-sm mb-3">
                <a href="{{ $url }}" class="btn btn-brand" download>
                  <i class="bi bi-download"></i> Unduh
                </a>
              @else
                <div class="text-muted fw-semibold mb-2">File bukti berupa PDF.</div>
                <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-brand">
                  <i class="bi bi-box-arrow-up-right"></i> Buka PDF
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif

  </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // sidebar mobile toggle
  const sidebar = document.getElementById('appSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const backdrop = document.getElementById('backdrop');

  const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
  const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

  toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
  backdrop?.addEventListener('click', closeSidebar);
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Flatpickr tanggal
    flatpickr("#tanggal", {
      enableTime: false,
      dateFormat: "d-m-Y",
      defaultDate: @json($tanggalFormatted ?? null),
      maxDate: "today",
      allowInput: true
    });

    const projectSelect     = document.getElementById("project");
    const subkategoriSelect = document.getElementById("subkategori_sumberdana");
    const jumlahInput       = document.getElementById("jumlah_transaksi");
    const formTransaksi     = document.getElementById("formTransaksi");

    const selectedSubkategoriId = @json(old('subkategori_sumberdana', $pencatatanKeuangan->subkategori_sumberdana_id ?? ''));

    // Rupiah mask
    function rupiahMask(el){
      el.addEventListener('input', function(){
        const digits = this.value.replace(/\D/g,'');
        if(!digits){ this.value=''; return; }
        this.value = 'Rp. ' + new Intl.NumberFormat('id-ID').format(Number(digits));
      });
    }
    if(jumlahInput) rupiahMask(jumlahInput);

    function toNumber(str){ return Number((str||'').toString().replace(/\D/g,'') || 0); }

    // Load subkategori by project
async function loadSubkategori(projectId, selectedId = null) {
  if (!projectId) return;

  subkategoriSelect.innerHTML = '<option value="">Memuat...</option>';
  subkategoriSelect.disabled = true;

  try {
    const res = await fetch(`/get-subkategori?project_id=${projectId}`);
    const data = await res.json();

    subkategoriSelect.innerHTML =
      '<option value="" disabled>Pilih Sub Kategori Pendanaan</option>';

    data.forEach(sub => {
      const opt = document.createElement('option');
      opt.value = sub.id;
      opt.textContent = sub.nama;

      // 🔥 AUTO SELECT SAAT EDIT
      if (selectedId && String(sub.id) === String(selectedId)) {
        opt.selected = true;
      }

      subkategoriSelect.appendChild(opt);
    });

    subkategoriSelect.disabled = false;
  } catch (e) {
    console.error(e);
    subkategoriSelect.innerHTML =
      '<option value="" disabled>Gagal memuat data</option>';
    subkategoriSelect.disabled = true;
  }
}


    // init edit mode
    if(projectSelect.value){
      loadSubkategori(projectSelect.value, selectedSubkategoriId);
    }

    projectSelect.addEventListener("change", function(){
      loadSubkategori(this.value);
    });

    // Submit: validasi + kirim via fetch
    formTransaksi.addEventListener("submit", async function(e){
      e.preventDefault();

      const requiredFields = [
        "tanggal","project","subkategori_sumberdana","jenis_transaksi",
        "deskripsi","jumlah_transaksi","metode_pembayaran"
      ];

      let isValid = true;

      // reset error
      requiredFields.forEach(id=>{
        const el = document.getElementById(id);
        el.classList.remove("error-border");
        const msg = el.parentNode.querySelector(".error-message");
        if(msg) msg.remove();
      });

      requiredFields.forEach(id=>{
        const el = document.getElementById(id);
        const val = (el.value || '').trim();
        if(!val){
          isValid = false;
          el.classList.add("error-border");
          const msg = document.createElement("div");
          msg.className = "error-message";
          msg.innerText = "Harap isi field ini";
          el.parentNode.appendChild(msg);
        }
      });

      const fileInput = document.getElementById("bukti_transaksi");
      if(fileInput && fileInput.files.length>0){
        const allowed = ['image/jpeg','image/png','application/pdf'];
        const f = fileInput.files[0];
        if(!allowed.includes(f.type)){
          isValid = false;
          fileInput.classList.add("error-border");
          const msg = document.createElement("div");
          msg.className = "error-message";
          msg.innerText = "Format file harus JPG, PNG, atau PDF";
          fileInput.parentNode.appendChild(msg);
        }
      }

      if(!isValid){
        Swal.fire({ title:'Gagal!', text:'Harap isi semua field yang wajib.', icon:'error' });
        return;
      }

      // ubah rupiah -> angka sebelum submit
      const originalNominal = jumlahInput.value;
      jumlahInput.value = toNumber(jumlahInput.value);

      const formData = new FormData(this);

      Swal.fire({
        title:'Menyimpan...',
        text:'Mohon tunggu sebentar',
        allowOutsideClick:false,
        didOpen:()=> Swal.showLoading()
      });

      try{
        const res = await fetch(this.action, {
          method: this.method,
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        });

        const data = await res.json();
        Swal.close();

        if(data.success){
          Swal.fire({
            title:"Sukses!",
            text: @json(isset($pencatatanKeuangan) ? 'Pencatatan keuangan berhasil diedit!' : 'Pencatatan keuangan berhasil disimpan!'),
            icon:"success",
            timer:1600,
            showConfirmButton:false
          });
          setTimeout(()=> window.location.href = @json(route('pencatatan_keuangan')), 1600);
        }else{
          jumlahInput.value = originalNominal;
          Swal.fire({ title:'Gagal!', text: data.message || 'Terjadi kesalahan saat menyimpan.', icon:'error' });
        }
      }catch(err){
        console.error(err);
        Swal.close();
        jumlahInput.value = originalNominal;
        Swal.fire({ title:'Gagal!', text:'Terjadi kesalahan saat menyimpan data.', icon:'error' });
      }
    });
  });
</script>

</body>
</html>
