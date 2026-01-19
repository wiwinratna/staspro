```blade
@extends('layouts.panel')
@section('title','Management User')

@push('styles')
<style>
  /* ✅ HERO biar nuansanya sama kaya halaman sebelumnya */
  .hero{
    border-radius:22px; padding:18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow:var(--shadow);
    position:relative; overflow:hidden; margin-bottom:14px;
  }
  .hero::after{
    content:""; position:absolute; inset:-1px;
    background:
      radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
      radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
    pointer-events:none; opacity:.65;
  }
  .hero-inner{ position:relative; z-index:2; }
  .hero-left .title{ font-size:1.65rem; font-weight:900; margin:0; letter-spacing:-.2px; }
  .hero-left .sub{ margin:6px 0 0; color:var(--ink-600); font-weight:500; }

  .tools-row{
    margin-top:14px;
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; flex-wrap:wrap;
  }
  .tools-right{
    margin-left:auto;
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
  }

  /* Buttons */
  .btn-brand{
    height:38px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    border-radius:999px;
    font-weight:900;
    padding:0 14px;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    border:0;
    box-shadow:0 16px 28px rgba(22,163,74,.18);
    color:#fff;
    white-space:nowrap;
    text-decoration:none;
  }
  .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }

  /* Manual Book (putih) */
  .btn-manual{
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
  .btn-manual:hover{
    background:var(--brand-50);
    color:var(--brand-700);
    transform:translateY(-1px);
  }

  /* =========================
     TABLE CARD + SPACING
     ========================= */
  .table-wrap{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    overflow:hidden;
    margin-top:12px;
    box-shadow:var(--shadow);
    padding: 8px 10px; /* ✅ biar napas */
  }

  .table-wrap .table{
    margin:0;
    width:100%;
  }

  .table-modern{
    font-size:.92rem;
    border-collapse:separate;
    border-spacing:0;
  }

  .table-modern thead th{
    background:#f8fafc;
    color:var(--ink-600);
    font-weight:900;
    text-transform:uppercase;
    font-size:.72rem;
    letter-spacing:.08em;
    padding:16px 18px !important;  /* ✅ lebih lega */
    border-bottom:1px solid rgba(226,232,240,.95);
    white-space:nowrap;
  }

  .table-modern tbody td{
    padding:16px 18px !important;  /* ✅ lebih lega */
    vertical-align:middle;
    border-top:1px solid #eef2f7;
    font-weight:600;
    line-height:1.35;
  }

  .table-modern tbody tr:hover{ background:var(--brand-50); transition:.12s; }

  /* kolom role biar gak sempit */
  .table-modern th:nth-child(3),
  .table-modern td:nth-child(3){
    min-width: 140px;
  }

  /* kolom aksi biar rapih */
  .table-modern th:last-child,
  .table-modern td:last-child{
    width:160px !important;
    min-width:160px;
    white-space:nowrap;
  }
  .table-modern td:last-child .d-flex{
    justify-content:center;
  }

  /* responsive scroll aman */
  .table-wrap .table-responsive{
    overflow-x:auto;
  }

  /* =========================
     DATATABLES CONTROLS (entries/search)
     ========================= */
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter{
    margin: 10px 8px 12px;
  }

  div.dataTables_length label,
  div.dataTables_filter label{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:800;
    color:var(--ink-600);
  }

  div.dataTables_filter input{
    border-radius:999px !important;
    padding:8px 14px !important;
    border:1px solid rgba(226,232,240,.95) !important;
    box-shadow:0 10px 26px rgba(15,23,42,.05);
  }

  div.dataTables_length select{
    border-radius:999px !important;
    padding:6px 12px !important;
    border:1px solid rgba(226,232,240,.95) !important;
    box-shadow:0 10px 26px rgba(15,23,42,.05);
  }

  .dataTables_wrapper .dataTables_paginate{
    margin: 12px 8px 6px;
  }
</style>
@endpush

@section('content')

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-left">
        <h1 class="title">Management User</h1>
        <p class="sub">Kelola akun user (tambah, ubah, dan hapus) untuk akses sistem STAS-RG.</p>
      </div>

      <div class="tools-row">
        <div class="tools-right">
          <a class="btn-manual"
             href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
             target="_blank" rel="noopener">
            <i class="bi bi-book"></i> Manual Book
          </a>

          <a href="{{ route('users.create') }}" class="btn-brand">
            <i class="bi bi-plus-lg"></i> Input User
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- TABLE -->
  <div class="table-wrap">
    <div class="table-responsive">
      <table id="table" class="table table-modern table-striped align-middle">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th style="width:160px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>
              <div class="d-flex gap-2">
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Edit">
                  <i class="bi bi-pencil-square"></i>
                </a>

                <form action="{{ route('users.destroy', $user->id) }}"
                      method="POST"
                      class="d-inline m-0"
                      onsubmit="return confirmDelete(event,this)">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger btn-sm" title="Hapus">
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
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  new DataTable('#table');

  @if(session('success'))
    Swal.fire({ icon:'success', title:'Sukses', text:'{{ session('success') }}', timer:2000, showConfirmButton:false });
  @endif

  @if(session('error'))
    Swal.fire({ icon:'error', title:'Gagal', text:'{{ session('error') }}' });
  @endif

  function confirmDelete(e,form){
    e.preventDefault();
    Swal.fire({
      title:'Apakah Anda yakin?',
      text:'Data user akan dihapus permanen!',
      icon:'warning',
      showCancelButton:true,
      confirmButtonColor:'#d33',
      cancelButtonColor:'#3085d6',
      confirmButtonText:'Ya, hapus!',
      cancelButtonText:'Batal'
    }).then(res=>{ if(res.isConfirmed) form.submit(); });
  }
</script>
@endpush
```
