<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ isset($pencatatanKeuangan) ? 'Edit Pencatatan Keuangan' : 'Tambah Pencatatan Keuangan' }}</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#ffffff;
    }
    *{box-sizing:border-box}
    body{ background:var(--bg); font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; color:var(--ink); }

    /* Topbar */
    .topbar{ background:linear-gradient(135deg,var(--brand-700),var(--brand)); color:#fff; }

    /* Shell */
    .app{ display:flex; min-height:calc(100vh - 56px); }
    .sidebar{
      width:260px; background:var(--card); border-right:1px solid var(--line);
      padding:18px; position:sticky; top:0; height:calc(100vh - 56px);
    }
    .menu-title{
      font-size:.8rem; letter-spacing:.06em; color:var(--ink-600);
      text-transform:uppercase; margin:6px 0 10px; font-weight:600;
    }
    .nav-link-custom{
      display:flex; align-items:center; gap:10px; padding:10px 12px;
      color:var(--ink); border-radius:12px; text-decoration:none; transition:all .18s;
      font-weight:500;
    }
    .nav-link-custom:hover{ background:var(--brand-50); color:var(--brand-700); }
    .nav-link-custom.active{ background:var(--brand); color:#fff; box-shadow:0 6px 16px rgba(22,163,74,.18); }

    .content{ flex:1; padding:24px; }
    .page-title{ font-size:1.5rem; font-weight:700; margin-bottom:4px; }
    .page-sub{ color:var(--ink-600); margin-bottom:18px; }

    /* Card form */
    .form-card{
      background:var(--card); border:1px solid var(--line); border-radius:18px;
      box-shadow:0 8px 22px rgba(15,23,42,.06);
    }
    .form-card .card-header{ background:#fff; border-bottom:1px solid var(--line); }
    .form-card .card-body{ padding:20px; }
    .form-section-title{ font-weight:700; font-size:1rem; margin:14px 0 10px; color:var(--ink); }

    .btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
    .btn-brand:hover{ background:var(--brand-700); border-color:var(--brand-700); }
    .help-text{ color:var(--ink-600); font-size:.85rem; }

    /* Form validation */
    .error-border { border: 2px solid #dc3545 !important; }
    .error-message { color:#dc3545; font-size:.85rem; margin-top:6px; }

    @media (max-width: 991.98px){
      .sidebar{ position:fixed; left:-280px; z-index:1040; transition:left .2s; }
      .sidebar.open{ left:0; }
      .content{ padding:18px; }
      .backdrop{ display:none; position:fixed; inset:0; background:rgba(15,23,42,.38); z-index:1035; }
      .backdrop.show{ display:block; }
    }
  </style>
</head>

<body>
  <!-- Topbar -->
  <nav class="navbar topbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <div class="ms-auto">
        @include('navbar')
      </div>
    </div>
  </nav>

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

    <!-- Main Content -->
    <main class="content">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-2">
        <div>
          <div class="page-title">{{ isset($pencatatanKeuangan) ? 'Edit Pencatatan Keuangan' : 'Tambah Pencatatan Keuangan' }}</div>
          <div class="page-sub">Lengkapi transaksi sesuai tim project dan sumber pendanaan.</div>
        </div>
        <a href="{{ route('pencatatan_keuangan') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-short me-1"></i> Kembali ke Daftar
        </a>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card form-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>{{ isset($pencatatanKeuangan) ? 'Form Edit Pencatatan Keuangan' : 'Form Tambah Pencatatan Keuangan' }}</strong>
        </div>

        <div class="card-body">
          <form id="formTransaksi"
                method="POST"
                action="{{ isset($pencatatanKeuangan) ? route('pencatatan_keuangan.update', $pencatatanKeuangan->id) : route('pencatatan_keuangan.store') }}"
                enctype="multipart/form-data">
            @csrf
            @if(isset($pencatatanKeuangan)) @method('PUT') @endif

            <div class="row g-3">
              <div class="col-md-6">
                <label for="tanggal" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                <input type="text" id="tanggal" name="tanggal" class="form-control" placeholder="Pilih tanggal"
                  value="{{ old('tanggal', isset($pencatatanKeuangan) ? \Carbon\Carbon::parse($pencatatanKeuangan->tanggal)->format('d-m-Y') : '') }}">
              </div>

              <div class="col-md-6">
                <label for="project" class="form-label">Tim Project <span class="text-danger">*</span></label>
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
                <label for="subkategori_sumberdana" class="form-label">Sub Kategori Pendanaan <span class="text-danger">*</span></label>
                <select id="subkategori_sumberdana" name="subkategori_sumberdana" class="form-select" disabled>
                  <option value="" disabled selected>Pilih Sub Kategori Pendanaan</option>
                </select>
                <div class="help-text mt-1">Sub kategori akan muncul setelah memilih Tim Project.</div>
              </div>

              <div class="col-md-6">
                <label for="jenis_transaksi" class="form-label">Jenis Pencatatan Keuangan <span class="text-danger">*</span></label>
                <select id="jenis_transaksi" name="jenis_transaksi" class="form-select">
                  <option value="" disabled selected>Pilih jenis transaksi</option>
                  <option value="pemasukan" {{ old('jenis_transaksi', $pencatatanKeuangan->jenis_transaksi ?? '') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                  <option value="pengeluaran" {{ old('jenis_transaksi', $pencatatanKeuangan->jenis_transaksi ?? '') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
              </div>

              <div class="col-12">
                <label for="deskripsi" class="form-label">Deskripsi Transaksi <span class="text-danger">*</span></label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Contoh: Pembelian ATK, transport, dll.">{{ old('deskripsi', $pencatatanKeuangan->deskripsi_transaksi ?? '') }}</textarea>
              </div>

              <div class="col-md-6">
                <label for="jumlah_transaksi" class="form-label">Nominal <span class="text-danger">*</span></label>
                <input type="text" id="jumlah_transaksi" name="jumlah_transaksi" class="form-control rupiah" inputmode="numeric"
                  placeholder="Cth: Rp. 1.000.000" value="{{ old('jumlah_transaksi', $pencatatanKeuangan->jumlah_transaksi ?? '') }}">
                <div class="help-text mt-1">Nominal diformat otomatis ke Rupiah.</div>
              </div>

              <div class="col-md-6">
                <label for="metode_pembayaran" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                <select id="metode_pembayaran" name="metode_pembayaran" class="form-select">
                  <option value="" disabled selected>Pilih metode pembayaran</option>
                  <option value="cash" {{ old('metode_pembayaran', $pencatatanKeuangan->metode_pembayaran ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                  <option value="transfer bank" {{ old('metode_pembayaran', $pencatatanKeuangan->metode_pembayaran ?? '') == 'transfer bank' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
              </div>

              <div class="col-12">
                <label for="bukti_transaksi" class="form-label">Bukti Transaksi</label>

                @if (isset($pencatatanKeuangan) && $pencatatanKeuangan->bukti_transaksi)
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalBuktiTransaksi">
                      <i class="bi bi-paperclip me-1"></i> Lihat Bukti Sebelumnya
                    </button>
                    <span class="help-text">Kosongkan jika tidak ingin mengganti.</span>
                  </div>
                @endif

                <input type="file" class="form-control" id="bukti_transaksi" name="bukti_transaksi" accept=".jpg,.jpeg,.png,.pdf">
                <div class="help-text mt-1">Format: JPG/PNG/PDF.</div>
              </div>

              <div class="d-flex gap-2 mt-4">
                <button class="btn btn-brand px-4" type="submit">
                  <i class="bi bi-check2-circle me-1"></i> {{ isset($pencatatanKeuangan) ? 'Update' : 'Simpan' }}
                </button>
                <a href="{{ route('pencatatan_keuangan') }}" class="btn btn-outline-secondary">Batal</a>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Modal bukti transaksi --}}
      @if (isset($pencatatanKeuangan) && $pencatatanKeuangan->bukti_transaksi)
      <div class="modal fade" id="modalBuktiTransaksi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Bukti Transaksi Sebelumnya</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <img src="{{ asset('storage/' . $pencatatanKeuangan->bukti_transaksi) }}" alt="Bukti Transaksi" class="img-fluid">
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
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');
    function openSidebar(){ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    function closeSidebar(){ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
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
      rupiahMask(jumlahInput);

      function toNumber(str){ return Number((str||'').toString().replace(/\D/g,'') || 0); }

      // Load subkategori by project
      async function loadSubkategori(projectId, selectedId=null){
        if(!projectId) return;
        subkategoriSelect.innerHTML = '<option value="">Memuat...</option>';
        subkategoriSelect.disabled = true;

        try{
          const res = await fetch(`/get-subkategori?project_id=${projectId}`);
          const data = await res.json();

          subkategoriSelect.disabled = false;
          subkategoriSelect.innerHTML = '<option value="" disabled selected>Pilih Sub Kategori Pendanaan</option>';

          (data || []).forEach(sub=>{
            const opt = document.createElement('option');
            opt.value = sub.id;
            opt.textContent = sub.nama;
            if(selectedId && String(sub.id)===String(selectedId)) opt.selected = true;
            subkategoriSelect.appendChild(opt);
          });
        }catch(e){
          console.error(e);
          subkategoriSelect.disabled = false;
          subkategoriSelect.innerHTML = '<option value="" disabled selected>Gagal memuat data</option>';
          Swal.fire({ title:'Gagal!', text:'Tidak dapat mengambil sub kategori pendanaan.', icon:'error' });
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
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
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
