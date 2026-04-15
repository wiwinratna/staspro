
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
            <th style="min-width:160px">Nama</th>
            <th style="min-width:180px">Email</th>
            <th style="min-width:100px">Role</th>
            <th style="min-width:120px">NIM/NIP</th>
            <th style="min-width:160px">No. Telp</th>
            <th style="min-width:150px">Jurusan</th>
            <th style="width:160px" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          @php
            $rawTelp = $user->no_telp ?? '';
            $waTelp = $rawTelp;
            if (str_starts_with($waTelp, '+')) $waTelp = substr($waTelp, 1);
            if (str_starts_with($waTelp, '0')) $waTelp = '62' . substr($waTelp, 1);
            if (!str_starts_with($waTelp, '62') && !empty($waTelp)) $waTelp = '62' . $waTelp;
          @endphp
          <tr>
            <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;" title="{{ $user->name }}">
              <span class="fw-semibold">{{ $user->name }}</span>
            </td>
            <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; font-size:0.85rem;" title="{{ $user->email }}">
              {{ $user->email }}
            </td>
            <td>
              @php
                $roleColor = match(strtolower($user->role ?? '')) {
                  'admin' => 'background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe;',
                  'bendahara' => 'background:#fef3c7; color:#92400e; border:1px solid #fde68a;',
                  'peneliti' => 'background:#dcfce7; color:#166534; border:1px solid #bbf7d0;',
                  default => 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;',
                };
              @endphp
              <span class="badge rounded-pill fw-bold" style="{{ $roleColor }} font-size:0.7rem; padding: 4px 10px; white-space:nowrap;">{{ ucfirst($user->role) }}</span>
            </td>
            <td style="white-space:nowrap; font-size:0.85rem; color:#334155;">
              {{ $user->nim_nip ?? '-' }}
            </td>
            <td style="white-space:nowrap; font-size:0.85rem; color:#334155;">
              {{ $rawTelp ?: '-' }}
            </td>
            <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:150px; font-size:0.82rem; color:#475569;" title="{{ $user->jurusan ?? '-' }}">
              {{ $user->jurusan ?? '-' }}
            </td>
            <td class="text-center">
              <div class="d-flex gap-1 justify-content-center flex-nowrap">
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Edit" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                  <i class="bi bi-pencil-square" style="font-size:0.8rem;"></i>
                </a>

                @if(!empty($rawTelp))
                  <a href="https://wa.me/{{ $waTelp }}" target="_blank" rel="noopener"
                     class="btn btn-sm" title="Hubungi via WhatsApp"
                     style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; background:#25D366; border:none; color:#fff; border-radius:6px;">
                    <i class="bi bi-whatsapp" style="font-size:0.85rem;"></i>
                  </a>
                @else
                  <span class="btn btn-sm disabled" title="No. Telp belum diisi"
                        style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; background:#e2e8f0; border:none; color:#94a3b8; border-radius:6px; cursor:not-allowed;">
                    <i class="bi bi-whatsapp" style="font-size:0.85rem;"></i>
                  </span>
                @endif

                <form action="{{ route('users.destroy', $user->id) }}"
                      method="POST"
                      class="d-inline m-0"
                      onsubmit="return confirmDelete(event,this)">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger btn-sm" title="Hapus" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="bi bi-trash-fill" style="font-size:0.8rem;"></i>
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

  function changePassword(userId, userName){
    Swal.fire({
      title: 'Ubah Password',
      html: `
        <p style="margin:0 0 12px;color:#475569;font-size:.92rem;">
          User: <strong>${userName}</strong>
        </p>
        <input type="password" id="swal-pw" class="swal2-input" placeholder="Password baru (min 6 karakter)">
        <input type="password" id="swal-pw-confirm" class="swal2-input" placeholder="Konfirmasi password baru">
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: '<i class="bi bi-check2-circle"></i> Simpan',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#16a34a',
      preConfirm: () => {
        const pw = document.getElementById('swal-pw').value;
        const pwc = document.getElementById('swal-pw-confirm').value;
        if(!pw || pw.length < 6){
          Swal.showValidationMessage('Password minimal 6 karakter');
          return false;
        }
        if(pw !== pwc){
          Swal.showValidationMessage('Konfirmasi password tidak cocok');
          return false;
        }
        return { password: pw, password_confirmation: pwc };
      }
    }).then(result => {
      if(!result.isConfirmed) return;

      fetch(`/users/${userId}/change-password`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(result.value)
      })
      .then(r => r.json())
      .then(data => {
        if(data.success){
          Swal.fire({ icon:'success', title:'Sukses', text: data.message, timer:2000, showConfirmButton:false });
        } else {
          Swal.fire({ icon:'error', title:'Gagal', text: data.message });
        }
      })
      .catch(() => {
        Swal.fire({ icon:'error', title:'Error', text:'Terjadi kesalahan pada server.' });
      });
    });
  }
</script>
@endpush