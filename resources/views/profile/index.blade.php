@extends('layouts.panel')

@section('title', 'Profile Pengguna')

@push('styles')
<style>
  .profile-card{border:1px solid rgba(226,232,240,.95);border-radius:18px;box-shadow:0 10px 24px rgba(15,23,42,.06)}
  .avatar-box{width:112px;height:112px;border-radius:50%;overflow:hidden;border:3px solid #e2e8f0;background:#f8fafc;display:flex;align-items:center;justify-content:center}
  .avatar-box img{width:100%;height:100%;object-fit:cover}
  .avatar-fallback{font-size:2rem;font-weight:800;color:#334155}
  .label-soft{font-size:.78rem;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.06em}

  /* Welcome / Profile Incomplete banner */
  .onboarding-banner{
    border-radius:18px;
    padding:24px 28px;
    margin-bottom:18px;
    border:1px solid rgba(22,163,74,.18);
    position:relative;
    overflow:hidden;
  }
  .onboarding-banner.welcome{
    background:linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 50%, #fff 100%);
  }
  .onboarding-banner.warning{
    background:linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #fff 100%);
    border-color:rgba(245,158,11,.22);
  }
  .onboarding-banner::before{
    content:"";
    position:absolute;
    top:-40px;right:-40px;
    width:160px;height:160px;
    border-radius:50%;
    background:rgba(22,163,74,.06);
    pointer-events:none;
  }
  .onboarding-banner .banner-icon{
    width:48px;height:48px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;
    font-size:22px;flex-shrink:0;
  }
  .onboarding-banner.welcome .banner-icon{
    background:rgba(22,163,74,.12);color:#16a34a;
  }
  .onboarding-banner.warning .banner-icon{
    background:rgba(245,158,11,.15);color:#d97706;
  }
  .onboarding-banner .banner-title{
    font-weight:800;font-size:1.05rem;margin:0 0 4px;color:#0f172a;
  }
  .onboarding-banner .banner-text{
    margin:0;font-size:.88rem;color:#475569;font-weight:500;line-height:1.5;
  }
  .onboarding-banner .banner-checklist{
    margin:10px 0 0;padding:0;list-style:none;display:flex;flex-wrap:wrap;gap:8px;
  }
  .onboarding-banner .banner-checklist li{
    display:inline-flex;align-items:center;gap:5px;
    font-size:.78rem;font-weight:700;
    padding:4px 10px;border-radius:999px;
    background:#fff;border:1px solid #e2e8f0;
  }
  .onboarding-banner .banner-checklist li.filled{
    background:#ecfdf5;color:#166534;border-color:rgba(22,163,74,.2);
  }
  .onboarding-banner .banner-checklist li.missing{
    background:#fef2f2;color:#991b1b;border-color:rgba(239,68,68,.2);
  }
</style>
@endpush

@section('content')
@php
  $isPeneliti = (($user->role ?? '') === 'peneliti');
  $photoPath = !empty($user->profile_photo ?? null) ? asset('storage/'.$user->profile_photo) : null;

  // Profil incomplete check
  $profileFields = [
    'Jurusan'   => !empty($user->jurusan),
    'Fakultas'  => !empty($user->fakultas),
    'NIM/NIP'   => !empty($user->nim_nip),
    'No. Telp'  => !empty($user->no_telp),
  ];
  $profileComplete = collect($profileFields)->every(fn($v) => $v);
@endphp

{{-- Welcome Banner (setelah register baru) --}}
@if(session('welcome'))
  <div class="onboarding-banner welcome">
    <div class="d-flex align-items-start gap-3">
      <div class="banner-icon">🎉</div>
      <div>
        <h4 class="banner-title">Selamat Datang di STASPRO!</h4>
        <p class="banner-text">{{ session('welcome') }}</p>
        @if($isPeneliti && !$profileComplete)
          <ul class="banner-checklist">
            @foreach($profileFields as $label => $filled)
              <li class="{{ $filled ? 'filled' : 'missing' }}">
                <i class="bi {{ $filled ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                {{ $label }}
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
@endif

{{-- Warning Banner (login dengan profil belum lengkap) --}}
@if(session('warning') && !session('welcome'))
  <div class="onboarding-banner warning">
    <div class="d-flex align-items-start gap-3">
      <div class="banner-icon">⚠️</div>
      <div>
        <h4 class="banner-title">Profil Belum Lengkap</h4>
        <p class="banner-text">{{ session('warning') }}</p>
        @if($isPeneliti && !$profileComplete)
          <ul class="banner-checklist">
            @foreach($profileFields as $label => $filled)
              <li class="{{ $filled ? 'filled' : 'missing' }}">
                <i class="bi {{ $filled ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                {{ $label }}
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
@endif

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
                <label class="form-label">Jurusan @if(!$profileComplete)<span class="text-danger">*</span>@endif</label>
                <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $user->jurusan ?? '') }}" placeholder="Contoh: Teknik Informatika">
              </div>
              <div class="col-md-6">
                <label class="form-label">Fakultas @if(!$profileComplete)<span class="text-danger">*</span>@endif</label>
                <input type="text" name="fakultas" class="form-control" value="{{ old('fakultas', $user->fakultas ?? '') }}" placeholder="Contoh: FTI">
              </div>
              <div class="col-md-6">
                <label class="form-label">NIM/NIP @if(!$profileComplete)<span class="text-danger">*</span>@endif</label>
                <input type="text" name="nim_nip" class="form-control" value="{{ old('nim_nip', $user->nim_nip ?? '') }}" placeholder="Isi NIM atau NIP">
              </div>
              <div class="col-md-6">
                <label class="form-label">No. Telepon @if(!$profileComplete)<span class="text-danger">*</span>@endif</label>
                <div class="input-group">
                  <span class="input-group-text" style="font-size:0.85rem; font-weight:600; background:#f8fafc; border-right:0;">+62</span>
                  <input type="text" name="no_telp" id="noTelpInput" class="form-control" 
                         value="{{ old('no_telp', $user->no_telp ?? '') }}" 
                         placeholder="81234567890" style="border-left:0;">
                </div>
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
              @foreach($projects->take(2) as $p)
                <li class="list-group-item px-0">
                  <div class="fw-semibold">{{ $p->nama_project }}</div>
                  <div class="small text-muted">Tahun {{ $p->tahun ?? '-' }} &bull; {{ ucfirst($p->status ?? '-') }}</div>
                </li>
              @endforeach
            </ul>
            @if($projects->count() > 2)
              <div class="text-center mt-2">
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#projectListModal" style="font-size:0.8rem;">
                  <i class="bi bi-list-ul me-1"></i> Lihat Selengkapnya ({{ $projects->count() }})
                </button>
              </div>
            @endif
          @else
            <div class="text-muted">Belum tergabung di project manapun.</div>
          @endif
        </div>
      </div>
    @endif
  </div>
</div>

{{-- Modal Daftar Project --}}
@if($isPeneliti && ($projects ?? collect())->count() > 2)
<div class="modal fade" id="projectListModal" tabindex="-1" aria-labelledby="projectListModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius: 18px; border: none; overflow: hidden; box-shadow: 0 25px 60px rgba(15,23,42,.14);">
      <div class="modal-header border-0 pb-0" style="padding:24px 28px 12px; background: linear-gradient(135deg, rgba(22,163,74,.06), rgba(22,163,74,.02));">
        <h5 class="modal-title fw-bold" id="projectListModalLabel"><i class="bi bi-folder2-open me-2 text-success"></i>Semua Project</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="opacity:.5;"></button>
      </div>
      <div class="modal-body" style="padding:12px 28px 20px;">
        <div class="list-group list-group-flush">
          @foreach($projects as $idx => $p)
            <div class="list-group-item px-0 d-flex align-items-start gap-3 border-0" style="border-bottom: 1px solid #f1f5f9 !important; padding:12px 0;">
              <div class="d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width:32px; height:32px; border-radius:8px; background: {{ ($p->status ?? '') === 'Aktif' ? '#16a34a' : '#94a3b8' }}; font-size:0.8rem;">
                {{ $idx + 1 }}
              </div>
              <div>
                <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $p->nama_project }}</div>
                <div class="d-flex align-items-center gap-2 mt-1">
                  <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i>Tahun {{ $p->tahun ?? '-' }}</span>
                  <span class="badge rounded-pill" style="font-size:0.65rem; padding:3px 8px; {{ ($p->status ?? '') === 'Aktif' ? 'background:#dcfce7; color:#166534;' : 'background:#f1f5f9; color:#64748b;' }}">{{ ucfirst($p->status ?? '-') }}</span>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="modal-footer border-0 pt-0" style="padding:8px 28px 20px;">
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill fw-bold px-4" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
  // Auto-format nomor telepon: strip leading 0 or +62 prefix for clean storage
  document.addEventListener('DOMContentLoaded', function() {
    const telInput = document.getElementById('noTelpInput');
    if (!telInput) return;

    // On page load: strip existing +62 or 62 prefix for display
    let initVal = telInput.value.trim();
    if (initVal.startsWith('+62')) {
      telInput.value = initVal.substring(3);
    } else if (initVal.startsWith('62') && initVal.length > 2) {
      telInput.value = initVal.substring(2);
    } else if (initVal.startsWith('0')) {
      telInput.value = initVal.substring(1);
    }

    // On form submit: prepend +62
    const form = telInput.closest('form');
    if (form) {
      form.addEventListener('submit', function() {
        let val = telInput.value.trim();
        if (val && !val.startsWith('+62') && !val.startsWith('62')) {
          if (val.startsWith('0')) val = val.substring(1);
          telInput.value = '+62' + val;
        }
      });
    }
  });
</script>
@endpush
