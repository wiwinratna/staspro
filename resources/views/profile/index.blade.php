@extends('layouts.panel')

@section('title', 'Profile Pengguna')

@push('styles')
<style>
  .profile-card{border:1px solid rgba(226,232,240,.95);border-radius:18px;box-shadow:0 10px 24px rgba(15,23,42,.06)}
  .avatar-box{width:112px;height:112px;border-radius:50%;overflow:hidden;border:3px solid #e2e8f0;background:#f8fafc;display:flex;align-items:center;justify-content:center}
  .avatar-box img{width:100%;height:100%;object-fit:cover}
  .avatar-fallback{font-size:2rem;font-weight:800;color:#334155}
  .label-soft{font-size:.78rem;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.06em}
</style>
@endpush

@section('content')
@php
  $isPeneliti = (($user->role ?? '') === 'peneliti');
  $photoPath = !empty($user->profile_photo ?? null) ? asset('storage/'.$user->profile_photo) : null;
@endphp

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card profile-card mb-3">
  <div class="card-body">
    <div class="d-flex flex-wrap gap-3 align-items-center">
      <div class="avatar-box">
        @if($photoPath)
          <img src="{{ $photoPath }}" alt="Foto Profil" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
          <span class="avatar-fallback" style="display:none;">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
        @else
          <span class="avatar-fallback">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
        @endif
      </div>
      <div>
        <h4 class="mb-1">{{ $user->name }}</h4>
        <div class="text-muted mb-1">{{ $user->email }}</div>
        <span class="badge rounded-pill text-bg-success">{{ strtoupper($user->role ?? '-') }}</span>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-8">
    <div class="card profile-card">
      <div class="card-body">
        <h5 class="mb-3">Data Pengguna</h5>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          @if($isPeneliti)
            <div class="mb-3">
              <label class="form-label">Foto Profil</label>
              <input type="file" name="profile_photo" class="form-control" accept=".jpg,.jpeg,.png">
              <div class="form-text">Maks 3 MB (JPG/PNG).</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
          @else
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" value="{{ $user->name }}" readonly>
            </div>
          @endif

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
          </div>

          @if($isPeneliti)
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Jurusan</label>
                <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $user->jurusan ?? '') }}" placeholder="Contoh: Teknik Informatika">
              </div>
              <div class="col-md-6">
                <label class="form-label">Fakultas</label>
                <input type="text" name="fakultas" class="form-control" value="{{ old('fakultas', $user->fakultas ?? '') }}" placeholder="Contoh: FTI">
              </div>
              <div class="col-md-6">
                <label class="form-label">NIM/NIP</label>
                <input type="text" name="nim_nip" class="form-control" value="{{ old('nim_nip', $user->nim_nip ?? '') }}" placeholder="Isi NIM atau NIP">
              </div>
            </div>
          @endif

          <div class="mt-3">
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card profile-card mb-3">
      <div class="card-body">
        <h5 class="mb-3">Ubah Password</h5>
        <form method="POST" action="{{ route('password.update') }}">
          @csrf
          <div class="mb-2">
            <label class="form-label">Password Saat Ini</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-outline-success w-100">Update Password</button>
        </form>
      </div>
    </div>

    @if($isPeneliti)
      <div class="card profile-card">
        <div class="card-body">
          <h5 class="mb-2">Tergabung di Project</h5>
          @if(($projects ?? collect())->count() > 0)
            <ul class="list-group list-group-flush">
              @foreach($projects as $p)
                <li class="list-group-item px-0">
                  <div class="fw-semibold">{{ $p->nama_project }}</div>
                  <div class="small text-muted">Tahun {{ $p->tahun ?? '-' }} • {{ ucfirst($p->status ?? '-') }}</div>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-muted">Belum tergabung di project manapun.</div>
          @endif
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
