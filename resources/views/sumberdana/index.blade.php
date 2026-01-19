{{-- resources/views/sumberdana/index.blade.php --}}
@extends('layouts.panel')

@section('title','Sumber Dana')

@push('styles')
  {{-- DataTables CSS --}}
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
    .tools-left, .tools-right{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .tools-right{ margin-left:auto; }

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

    /* Table */
    .table-wrap{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      overflow:hidden;
      margin-top:12px;
      box-shadow:var(--shadow);
    }
    .table-responsive{ max-height:68vh; overflow-y:auto; }

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
    }
    .table-modern tbody td{
      padding:14px 12px;
      vertical-align:middle;
      border-top:1px solid #eef2f7;
      font-weight:500;
    }
    .table-striped > tbody > tr:nth-of-type(odd){ background:#fcfcfd; }
    .table-modern tbody tr:hover{ background:var(--brand-50); transition:.12s; }
  </style>
@endpush

@section('content')

  {{-- meta csrf tetap aman karena panel sudah include csrf? tapi kamu pakai form + @csrf, jadi aman. --}}

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-left">
        <h1 class="title">Sumber Dana</h1>
        <p class="sub">Kelola daftar sumber & jenis pendanaan.</p>
      </div>

      <div class="tools-row">
        <div class="tools-left">
          <a href="{{ route('sumberdana.create') }}" class="btn btn-brand">
            <i class="bi bi-plus-lg"></i> Input Sumber Dana
          </a>
        </div>

        <div class="tools-right">
          <a
            href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
            target="_blank"
            rel="noopener"
            class="btn btn-soft"
            title="Buka Manual Book"
          >
            <i class="bi bi-journal-bookmark"></i> Manual Book
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Notif -->
  @if ($message = Session::get('success'))
    <div class="alert alert-success mt-3 mb-0">{{ $message }}</div>
  @endif
  @if ($message = Session::get('error'))
    <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
  @endif

  <!-- TABLE -->
  <div class="table-wrap">
    <div class="table-responsive">
      <table id="table" class="table table-modern table-striped align-middle">
        <thead>
          <tr>
            <th class="text-start" style="min-width:260px">Nama Sumber Dana</th>
            <th class="text-center" style="min-width:200px">Jenis Pendanaan</th>
            <th class="text-center" style="min-width:140px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($sumberdana as $r)
            <tr>
              <td class="text-start">{{ $r->nama_sumber_dana }}</td>
              <td class="text-center">{{ Str::title($r->jenis_pendanaan) }}</td>
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  <a href="{{ route('sumberdana.edit', $r->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                  </a>

                  <form action="{{ route('sumberdana.destroy', $r->id) }}" method="POST" class="d-inline" data-id="{{ $r->id }}">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $r->id }})" data-bs-toggle="tooltip" title="Hapus">
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

@endsection

@push('scripts')
  {{-- jQuery + DataTables + SweetAlert (Bootstrap JS sudah ada di panel) --}}
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(function(){
      new DataTable('#table', {
        paging:true, searching:true, ordering:true, info:true,
        language:{
          search:"Cari:",
          lengthMenu:"Tampil _MENU_ data",
          info:"Menampilkan _START_–_END_ dari _TOTAL_ data",
          paginate:{previous:"‹",next:"›"}
        },
        columnDefs:[
          { targets:[1,2], className:'text-center' },
          { targets:[0], className:'text-start' },
        ]
      });
    });

    // SweetAlert Delete
    function confirmDelete(id){
      Swal.fire({
        title:'Hapus data ini?',
        text:'Tindakan ini tidak dapat dibatalkan.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        cancelButtonText:'Batal',
        confirmButtonText:'Ya, hapus'
      }).then((r)=>{
        if(r.isConfirmed){
          const form = document.querySelector(`form[data-id="${id}"]`);
          form?.submit();
        }
      });
    }
  </script>
@endpush
