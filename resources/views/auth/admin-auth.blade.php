<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login & Register</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- ✅ Lottie Player -->
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

  <style>
    :root{
      --brand:#006400;
      --brand-2:#0b7a2a;
      --muted:#6b7280;
      --border:#e5e7eb;
      --bg:#f5f7fb;
    }

    body{
      margin:0;
      min-height:100vh;
      background: var(--bg);
      font-family: Arial, sans-serif;
      overflow-x:hidden;
    }

    /* ✅ FULL SCREEN GRID */
    .auth-wrap{
      min-height:100vh;
      width:100%;
      display:grid;
      grid-template-columns: 1.1fr .9fr;
    }

    /* ================= LEFT: LIVE LOTTIE BACKGROUND ================= */
    .auth-visual{
      position:relative;
      background:#eef2f7;
      overflow:hidden;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    /* Lottie sebagai background (pas, tidak kebesaran) */
    .lottie-bg{
      position:absolute;
      inset:0;
      pointer-events:none;
      opacity: 1;
    }
    .lottie-bg lottie-player{
    width: 106%;
    height: 106%;
    transform: translate(-3%, -3%) scale(1.00);
    }



    /* overlay biar teks kebaca + vibe soft */
    .visual-overlay{
      position:absolute;
      inset:0;
      background:
        radial-gradient(900px 520px at 35% 45%, rgba(255,255,255,.75), rgba(255,255,255,.35) 55%, rgba(255,255,255,.15) 85%),
        linear-gradient(90deg, rgba(255,255,255,.30), rgba(255,255,255,.08));
      pointer-events:none;
    }

    /* teks atas & bawah */
    .visual-top,
    .visual-bottom{
      position:absolute;
      left:28px;
      right:28px;
      z-index:3;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
    }

    .visual-top{ top:22px; }
    .visual-bottom{ bottom:20px; }

    .visual-brand{
      display:flex;
      align-items:center;
      gap:12px;
    }
    .visual-badge{
      width:40px;height:40px;
      border-radius:12px;
      background: rgba(0,100,0,.12);
      display:flex;
      align-items:center;
      justify-content:center;
      color: var(--brand);
      font-size: 18px;
      border:1px solid rgba(0,100,0,.18);
      backdrop-filter: blur(6px);
    }
    .visual-title{
      margin:0;
      font-weight:900;
      font-size: 22px;
      color:#0f172a;
      line-height:1.05;
    }
    .visual-sub{
      margin:0;
      color:#334155;
      font-size: 13px;
      font-weight:600;
      opacity:.9;
      margin-top:2px;
    }

    .visual-chip{
      padding:8px 12px;
      border-radius:999px;
      background: rgba(255,255,255,.65);
      border:1px solid rgba(15,23,42,.08);
      color:#0f172a;
      font-size:12px;
      font-weight:700;
      backdrop-filter: blur(8px);
      white-space:nowrap;
    }

    .visual-bottom p{
      margin:0;
      color:#334155;
      font-size:12px;
      font-weight:600;
      opacity:.9;
      max-width: 520px;
    }

    /* ================= RIGHT: FORM PANEL ================= */
    .auth-panel{
      background:#fff;
      display:flex;
      flex-direction:column;
      justify-content:center;
      padding: 44px 44px 36px;
      border-left: 1px solid rgba(0,0,0,.06);
    }

    .brand-row{
      display:flex;
      align-items:center;
      gap:12px;
      margin-bottom: 8px;
    }
    .brand-badge{
      width:40px;height:40px;
      border-radius:12px;
      background: rgba(0,100,0,.12);
      display:flex;
      align-items:center;
      justify-content:center;
      color: var(--brand);
      font-size: 18px;
      flex: 0 0 auto;
    }
    .brand-title{
      margin:0;
      font-weight:800;
      font-size: 20px;
      line-height:1.1;
    }
    .brand-sub{
      margin:0;
      color: var(--muted);
      font-size: 13px;
      margin-top: 2px;
    }

    .divider{
      height:1px;
      background: var(--border);
      margin: 16px 0 18px;
    }

    .nav-tabs{
      border-bottom: 1px solid var(--border);
      margin-bottom: 18px;
      gap: 18px;
    }
    .nav-tabs .nav-link{
      border:none !important;
      color:#6b7280;
      font-weight:700;
      padding: 10px 4px;
      background:transparent;
      position:relative;
    }
    .nav-tabs .nav-link.active{
      color: var(--brand);
    }
    .nav-tabs .nav-link.active::after{
      content:"";
      position:absolute;
      left:0; right:0; bottom:-1px;
      height:3px;
      background: var(--brand);
      border-radius: 99px;
    }

    .form-label{
      font-size: 13px;
      font-weight: 700;
      color:#111827;
      margin-bottom:6px;
    }

    .form-control{
      height: 44px;
      border-radius: 10px;
      border: 1px solid #d1d5db;
      padding: 10px 12px;
    }
    .form-control:focus{
      border-color: var(--brand);
      box-shadow: 0 0 0 .2rem rgba(0,100,0,.15);
    }

    .btn-main{
      height: 44px;
      border-radius: 10px;
      font-weight: 800;
      background: var(--brand);
      border: none;
    }
    .btn-main:hover{
      background: var(--brand-2);
    }

    .small-link{
      font-size: 13px;
      color: var(--brand);
      text-decoration:none;
      font-weight:700;
    }
    .small-link:hover{ text-decoration:underline; }

    .alert{
      border-radius: 12px;
      padding: 12px 14px;
      font-size: 14px;
    }

    .password-requirements{
      font-size: .82rem;
      color: var(--muted);
      margin-top: 6px;
      background:#f9fafb;
      border:1px dashed #e5e7eb;
      border-radius: 12px;
      padding: 10px 12px;
    }
    .password-requirements ul{
      margin: 8px 0 0;
      padding-left: 18px;
    }
    .password-requirements li{ margin: 3px 0; }
    .password-requirements li.valid{ color:#198754; font-weight:700; }
    .password-requirements li.invalid{ color:#dc3545; font-weight:700; }

    .hint{
      color: var(--muted);
      font-size: 12px;
      margin-top: 6px;
    }

    /* ✅ MOBILE: animasi tetap muncul, tapi ga makan tempat */
    @media (max-width: 992px){
      .auth-wrap{ grid-template-columns: 1fr; }

      .auth-visual{
        min-height: 34vh;
      }

      .lottie-bg lottie-player{
        width: 125%;
        height: 125%;
        transform: translate(-8%, -5%) scale(1.02);
      }

      .auth-panel{
        padding: 24px 18px 22px;
        border-left:none;
      }

      .visual-top{ top:16px; left:16px; right:16px; }
      .visual-bottom{ bottom:14px; left:16px; right:16px; }
      .visual-title{ font-size: 18px; }
      .visual-chip{ display:none; }
      .visual-bottom p{ max-width: 100%; }
    }
  </style>

  @php
    $isRegister = request()->routeIs('admin.register');
    $isLogin = !$isRegister;
  @endphp
</head>

<body>
  <div class="auth-wrap">

    <!-- LEFT -->
    <div class="auth-visual">
      <div class="lottie-bg">
        <lottie-player
          src="{{ asset('lottie/login-admin.json') }}"
          background="transparent"
          speed="1"
          loop
          autoplay>
        </lottie-player>
      </div>

      <div class="visual-overlay"></div>

      <div class="visual-top">
        <div class="visual-brand">
          <div class="visual-badge"><i class="bi bi-graph-up-arrow"></i></div>
          <div>
            <h2 class="visual-title">STAS PRO</h2>
            <p class="visual-sub">Sistem Keuangan & Administrasi Riset</p>
          </div>
        </div>
        <div class="visual-chip">Admin Access • Secure</div>
      </div>

      <div class="visual-bottom">
        <p>Kelola anggaran, transaksi, dan laporan kegiatan riset secara terintegrasi.</p>
        <p style="opacity:.75;">© {{ date('Y') }} STAS-RG</p>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="auth-panel">

      <div class="brand-row">
        <div class="brand-badge">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
          <h1 class="brand-title">Admin Panel</h1>
          <p class="brand-sub">Akses khusus Admin untuk mengelola sistem</p>
        </div>
      </div>

      <div class="divider"></div>

      <ul class="nav nav-tabs" id="authTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link {{ $isLogin ? 'active' : '' }}"
                  id="login-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#login"
                  type="button" role="tab">
            Login
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link {{ $isRegister ? 'active' : '' }}"
                  id="register-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#register"
                  type="button" role="tab">
            Register
          </button>
        </li>
      </ul>

      <div class="tab-content">

        <!-- LOGIN -->
        <div class="tab-pane fade {{ $isLogin ? 'show active' : '' }}" id="login" role="tabpanel">
          <div id="login-alert-container"></div>

          <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
            @csrf

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control"
                     placeholder="Masukkan email admin" required value="{{ old('email') }}">
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control"
                     placeholder="Masukkan password" required>
            </div>

            <div class="d-grid mt-2">
              <button type="submit" class="btn btn-main text-white">Sign in</button>
            </div>

            <div class="text-center mt-3">
              <a href="{{ route('login') }}" class="small-link">
                <i class="bi bi-arrow-left"></i> Kembali ke Login Peneliti
              </a>
            </div>
          </form>
        </div>

        <!-- REGISTER -->
        <div class="tab-pane fade {{ $isRegister ? 'show active' : '' }}" id="register" role="tabpanel">
          <div id="register-alert-container"></div>

          <form method="POST" action="{{ route('admin.register.post') }}" id="registerForm">
            @csrf

            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="name" class="form-control"
                     placeholder="Masukkan nama lengkap" required value="{{ old('name') }}">
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control"
                     placeholder="Masukkan email admin" required value="{{ old('email') }}">
            </div>

            <div class="row g-3 mb-2">
              <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" id="register-password" name="password" class="form-control"
                       placeholder="Buat password" required autocomplete="new-password" minlength="8">

                <div class="password-requirements" id="password-requirements">
                  <div class="fw-bold mb-1" style="color:#111827;">Password harus memenuhi kriteria berikut:</div>
                  <ul class="m-0">
                    <li id="length-req">Minimal 8 karakter</li>
                    <li id="uppercase-req">Mengandung huruf besar</li>
                    <li id="lowercase-req">Mengandung huruf kecil</li>
                    <li id="number-req">Mengandung angka</li>
                  </ul>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                       placeholder="Konfirmasi password" required minlength="8">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Kode Rahasia Admin</label>
              <input type="password" name="admin_secret" class="form-control"
                     placeholder="Masukkan kode rahasia" required>
              <div class="hint">Kode ini hanya diketahui oleh pengelola sistem.</div>
            </div>

            <div class="d-grid mt-2">
              <button type="submit" class="btn btn-main text-white">Register</button>
            </div>

            <div class="text-center mt-3">
              <a href="{{ route('login') }}" class="small-link">
                <i class="bi bi-arrow-left"></i> Kembali ke Login Peneliti
              </a>
            </div>
          </form>
        </div>

      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function validatePassword(password) {
      return {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password)
      };
    }

    function updatePasswordRequirements(req) {
      const map = {
        length: document.getElementById('length-req'),
        uppercase: document.getElementById('uppercase-req'),
        lowercase: document.getElementById('lowercase-req'),
        number: document.getElementById('number-req'),
      };
      Object.keys(req).forEach(k => {
        if (!map[k]) return;
        map[k].className = req[k] ? 'valid' : 'invalid';
      });
    }

    document.addEventListener('DOMContentLoaded', function () {
      const pass = document.getElementById('register-password');
      if (pass) {
        updatePasswordRequirements({length:false, uppercase:false, lowercase:false, number:false});
        pass.addEventListener('input', function () {
          updatePasswordRequirements(validatePassword(this.value));
        });
      }

      const targetId = {{ $isRegister ? "'register-alert-container'" : "'login-alert-container'" }};
      const box = document.getElementById(targetId);

      @if($errors->any())
        const errs = {!! json_encode($errors->all()) !!};
        box.innerHTML = `<div class="alert alert-danger mb-3">${errs.join('<br>')}</div>`;
      @endif

      @if(session('success'))
        box.innerHTML = `<div class="alert alert-success mb-3">{{ session('success') }}</div>`;
      @endif

      @if(session('error'))
        box.innerHTML = `<div class="alert alert-danger mb-3">{{ session('error') }}</div>`;
      @endif
    });
  </script>
</body>
</html>
