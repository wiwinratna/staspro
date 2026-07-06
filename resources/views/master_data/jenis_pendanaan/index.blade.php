@extends('layouts.panel')
@section('title', 'Jenis Pendanaan')
@push('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
  <style>
    :root{ --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5; --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#ffffff; --danger:#ef4444; --shadow:0 10px 30px rgba(15,23,42,.08); --shadow2:0 18px 40px rgba(15,23,42,.10); }
    .hero{ border-radius:22px; padding:18px; background: radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%), radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%), linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76)); border:1px solid rgba(226,232,240,.95); box-shadow:var(--shadow); position:relative; overflow:hidden; margin-bottom:14px; }
    .hero::after{ content:""; position:absolute; inset:-1px; background: radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%), radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%); pointer-events:none; opacity:.65; }
    .hero-inner{ position:relative; z-index:2; width:100%; }
    .hero-left .title{ font-size:1.65rem; font-weight:800; margin:0; letter-spacing:-.2px; }
    .hero-left .sub{ margin:6px 0 0; color:var(--ink-600); font-weight:500; }
    .tools-row{ margin-top:14px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
    .btn-brand{ height:38px; display:inline-flex; align-items:center; gap:8px; border-radius:999px; font-weight:800; padding:0 14px; background:linear-gradient(135deg,var(--brand-700),var(--brand)); border:0; box-shadow:0 16px 28px rgba(22,163,74,.18); color:#fff; text-decoration:none; }
    .table-wrap{ background:var(--card); border:1px solid rgba(226,232,240,.95); border-radius:22px; overflow:hidden; margin-top:12px; box-shadow:var(--shadow); }
    .table-responsive{ max-height:68vh; overflow-y:auto; }
    .table-modern{ margin:0; font-size:.92rem; border-collapse:separate; border-spacing:0; }
    .table-modern thead th{ background:#f8fafc; color:var(--ink-600); font-weight:900; text-transform:uppercase; font-size:.72rem; letter-spacing:.08em; padding:14px 12px; border-bottom:1px solid rgba(226,232,240,.95); position:sticky; top:0; z-index:5; }
    .table-modern tbody td{ padding:14px 12px; vertical-align:middle; border-top:1px solid #eef2f7; font-weight:500; }
    .table-striped > tbody > tr:nth-of-type(odd){ background:#fcfcfd; }
  </style>
@endpush
@section('content')
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-left">
        <h1 class="title">Jenis Pendanaan</h1>
        <p class="sub">Kelola daftar Jenis Pendanaan.</p>
      </div>
      <div class="tools-row">
        <a href="{{ route('jenis-pendanaan.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg"></i> Tambah Data</a>
      </div>
    </div>
  </section>

  @if ($message = Session::get('success')) <div class="alert alert-success mt-3 mb-0">{{ $message }}</div> @endif
  @if ($message = Session::get('error')) <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div> @endif

  <div class="table-wrap">
    <div class="table-responsive">
      <table id="table" class="table table-modern table-striped align-middle">
        <thead>
          <tr>
            <th class="text-center" width="5%">No</th>
            <th class="text-center">Kode</th>
            <th class="text-start">Nama</th>
            <th class="text-center">Status</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data as $r)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td class="text-center">{{ $r->kode ?? $r->singkatan ?? '-' }}</td>
              <td class="text-start">{{ $r->nama }}</td>
              <td class="text-center">
                @if($r->is_active) <span class="badge bg-success">Aktif</span> @else <span class="badge bg-secondary">Nonaktif</span> @endif
              </td>
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  <a href="{{ route('jenis-pendanaan.edit', $r->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                  <form action="{{ route('jenis-pendanaan.destroy', $r->id) }}" method="POST" class="d-inline" data-id="{{ $r->id }}">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $r->id }})"><i class="bi bi-trash-fill"></i></button>
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
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    $(function(){ new DataTable('#table'); });
    function confirmDelete(id){
      Swal.fire({
        title:'Hapus data ini?', text:'Tindakan ini tidak dapat dibatalkan.', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', confirmButtonText:'Ya, hapus'
      }).then((r)=>{ if(r.isConfirmed) document.querySelector(`form[data-id="${id}"]`).submit(); });
    }
  </script>
@endpush