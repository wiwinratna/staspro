<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login & Register Peneliti</title>

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

    /* ✅ FULL SCREEN GRID (sama kaya admin) */
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

    .lottie-bg{
      position:absolute;
      inset:0;
      pointer-events:none;
      opacity: 1;
    }
    /* ukuran PAS (nggak kebesaran) */
    .lottie-bg lottie-player{
      width: 108%;
      height: 108%;
      transform: translate(-4%, 2%) scale(1.00);
    }

    .visual-overlay{
      position:absolute;
      inset:0;
      background:
        radial-gradient(900px 520px at 35% 45%, rgba(255,255,255,.75), rgba(255,255,255,.35) 55%, rgba(255,255,255,.15) 85%),
        linear-gradient(90deg, rgba(255,255,255,.30), rgba(255,255,255,.08));
      pointer-events:none;
    }

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

    /* Tabs (sama kaya admin) */
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

    .form-control, .form-select{
      height: 44px;
      border-radius: 10px;
      border: 1px solid #d1d5db;
      padding: 10px 12px;
      transition: border .2s ease, box-shadow .2s ease;
    }
    .form-control:focus, .form-select:focus{
      border-color: var(--brand);
      box-shadow: 0 0 0 .2rem rgba(0,100,0,.15);
      outline: none;
    }

    .btn-primary{
      height: 44px;
      border-radius: 10px;
      font-weight: 800;
      background: var(--brand);
      border: none;
    }
    .btn-primary:hover{
      background: var(--brand-2);
    }
    .btn-primary:disabled{
      background: #6c757d;
      cursor:not-allowed;
    }

    .alert{
      border-radius: 12px;
      padding: 12px 14px;
      font-size: 14px;
      margin-bottom: 15px;
    }

    .invalid-feedback, .valid-feedback{
      display:block;
      margin-top:.25rem;
      font-size:.875em;
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

    .spinner-border-sm{
      width:1rem;height:1rem;
    }
    .loading{ pointer-events:none; }

    /* ✅ MOBILE: animasi tetap muncul */
    @media (max-width: 992px){
      .auth-wrap{ grid-template-columns: 1fr; }

      .auth-visual{ min-height: 34vh; }

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
</head>

<body>
  <div class="auth-wrap">

    <!-- LEFT -->
    <div class="auth-visual">
      <div class="lottie-bg">
        <lottie-player
          src="{{ asset('lottie/login-peneliti.json') }}"
          background="transparent"
          speed="1"
          loop
          autoplay>
        </lottie-player>
      </div>

      <div class="visual-overlay"></div>

      <div class="visual-top">
        <div class="visual-brand">
          <div class="visual-badge"><i class="bi bi-journal-check"></i></div>
          <div>
            <h2 class="visual-title">STAS PRO</h2>
            <p class="visual-sub">Portal Peneliti • Keuangan & Administrasi Riset</p>
          </div>
        </div>
        <div class="visual-chip">Peneliti Access • Secure</div>
      </div>

      <div class="visual-bottom">
        <p>Ajukan kebutuhan, unggah bukti, dan pantau progres pendanaan riset secara real-time.</p>
        <p style="opacity:.75;">© {{ date('Y') }} STAS-RG</p>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="auth-panel">

      <div class="brand-row">
        <div class="brand-badge">
          <i class="bi bi-person-badge-fill"></i>
        </div>
        <div>
          <h1 class="brand-title">Portal Peneliti</h1>
          <p class="brand-sub">Masuk atau daftar untuk mengakses sistem</p>
        </div>
      </div>

      <div class="divider"></div>

      <ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
            type="button" role="tab">Login</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register"
            type="button" role="tab">Register</button>
        </li>
      </ul>

      <div class="tab-content">
        <!-- ================= LOGIN FORM ================= -->
        <div class="tab-pane fade show active" id="login" role="tabpanel">
          <div id="login-alert-container"></div>

          <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            <div class="mb-3">
              <label for="login-email" class="form-label">Email</label>
              <input type="email" id="login-email" name="email" class="form-control"
                placeholder="Masukkan email Anda" required value="{{ old('email') }}">
              <div class="invalid-feedback" id="login-email-error" style="display:none;"></div>
            </div>

            <div class="mb-3">
              <label for="login-password" class="form-label">Password</label>
              <input type="password" id="login-password" name="password" class="form-control"
                placeholder="Masukkan password Anda" required>
              <div class="invalid-feedback" id="login-password-error" style="display:none;"></div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary" id="loginBtn">
                <span class="btn-text">Login</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </div>

            <div class="text-center mt-3">
              <a href="{{ route('admin.login') }}" class="small-link">
                <i class="bi bi-shield-lock"></i> Login Admin
              </a>
            </div>
          </form>
        </div>

        <!-- ================= REGISTER FORM (AUTO ROLE PENELITI) ================= -->
        <div class="tab-pane fade" id="register" role="tabpanel">
          <div id="register-alert-container"></div>

          <form method="POST" action="{{ route('register.post') }}" id="registerForm">
            @csrf

            <div class="mb-3">
              <label for="name" class="form-label">Nama Lengkap</label>
              <input type="text" id="name" name="name" class="form-control"
                placeholder="Masukkan nama lengkap Anda" required value="{{ old('name') }}">
              <div class="invalid-feedback" id="name-error" style="display:none;"></div>
              <div class="valid-feedback" id="name-success" style="display:none;">
                <i class="bi bi-check-circle"></i> Nama lengkap valid
              </div>
            </div>

            <div class="mb-3">
              <label for="register-email" class="form-label">Email</label>
              <input type="email" id="register-email" name="email" class="form-control"
                placeholder="Masukkan email Anda (contoh: nama@domain.com)" required value="{{ old('email') }}">
              <div class="invalid-feedback" id="email-error" style="display:none;"></div>
              <div class="valid-feedback" id="email-success" style="display:none;">
                <i class="bi bi-check-circle"></i> Email tersedia
              </div>
            </div>

            <div class="mb-3 row g-3">
              <div class="col-md-6">
                <label for="register-password" class="form-label">Password</label>
                <input type="password" id="register-password" name="password" class="form-control"
                  placeholder="Buat password" required autocomplete="new-password" minlength="8">
                <div class="invalid-feedback" id="password-error" style="display:none;"></div>

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
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                  placeholder="Konfirmasi password" required minlength="8">
                <div class="invalid-feedback" id="password-confirmation-error" style="display:none;"></div>
              </div>
            </div>

            <!-- ROLE DIKUNCI: PENELITI -->
            <input type="hidden" id="role" name="role" value="peneliti">

            <div class="d-grid">
              <button type="submit" class="btn btn-primary" id="registerBtn">
                <span class="btn-text">Register</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </div>

            <div class="text-center mt-3">
              <a href="{{ route('admin.login') }}" class="small-link">
                <i class="bi bi-shield-lock"></i> Login Admin
              </a>
            </div>
          </form>
        </div>

      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ================== SCRIPT ASLI KAMU (tetap) ==================

    function validateFullName(name) {
      const nameRegex = /^[a-zA-Z\s\-'\.]+$/;
      name = name.trim();

      if (name.length < 2) return { isValid: false, message: "Nama lengkap minimal 2 karakter" };
      if (name.length > 50) return { isValid: false, message: "Nama lengkap maksimal 50 karakter" };
      if (!nameRegex.test(name)) {
        return {
          isValid: false,
          message: "Nama lengkap tidak boleh mengandung angka atau simbol khusus. Hanya huruf, spasi, tanda hubung (-), apostrof ('), dan titik (.) yang diizinkan."
        };
      }
      if (name.replace(/\s/g, '').length === 0) return { isValid: false, message: "Nama lengkap tidak boleh hanya berisi spasi" };
      if (name !== name.trim()) return { isValid: false, message: "Nama lengkap tidak boleh dimulai atau diakhiri dengan spasi" };

      return { isValid: true, message: "Nama lengkap valid" };
    }

    function validateEmail(email) {
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!email || email.trim() === '') return { isValid: false, message: "Email tidak boleh kosong" };
      if (!emailRegex.test(email)) return { isValid: false, message: "Format email tidak valid. Gunakan format yang benar seperti: nama@domain.com" };
      return { isValid: true, message: "Format email valid" };
    }

    function validatePassword(password) {
      const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password)
      };
      const isValid = Object.values(requirements).every(req => req);
      return {
        isValid: isValid,
        requirements: requirements,
        message: isValid ? "Password memenuhi semua kriteria" : "Password tidak memenuhi kriteria yang diperlukan"
      };
    }

    function showAlert(message, type = 'danger', containerId = 'register-alert-container') {
      const alertContainer = document.getElementById(containerId);
      let icon = 'bi-exclamation-triangle-fill';
      if (type === 'success') icon = 'bi-check-circle-fill';
      else if (type === 'warning') icon = 'bi-exclamation-triangle-fill';
      else if (type === 'info') icon = 'bi-info-circle-fill';

      const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
          <i class="bi ${icon}"></i> ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      `;
      alertContainer.innerHTML = alertHtml;

      setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) alert.remove();
      }, 7000);
    }

    function updatePasswordRequirements(requirements) {
      const elements = {
        length: document.getElementById('length-req'),
        uppercase: document.getElementById('uppercase-req'),
        lowercase: document.getElementById('lowercase-req'),
        number: document.getElementById('number-req')
      };
      Object.keys(requirements).forEach(key => {
        const element = elements[key];
        if (element) element.className = requirements[key] ? 'valid' : 'invalid';
      });
    }

    function setLoadingState(button, isLoading) {
      const btnText = button.querySelector('.btn-text');
      const spinner = button.querySelector('.spinner-border');

      if (isLoading) {
        button.disabled = true;
        btnText.textContent = 'Memproses...';
        spinner.classList.remove('d-none');
        button.classList.add('loading');
      } else {
        button.disabled = false;
        btnText.textContent = button.id === 'loginBtn' ? 'Login' : 'Register';
        spinner.classList.add('d-none');
        button.classList.remove('loading');
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const nameInput = document.getElementById('name');
      const emailInput = document.getElementById('register-email');
      const passwordInput = document.getElementById('register-password');
      const confirmPasswordInput = document.getElementById('password_confirmation');
      const roleInput = document.getElementById('role');
      const registerForm = document.getElementById('registerForm');

      const loginForm = document.getElementById('loginForm');
      const loginEmailInput = document.getElementById('login-email');
      const loginPasswordInput = document.getElementById('login-password');

      const loginBtn = document.getElementById('loginBtn');
      const registerBtn = document.getElementById('registerBtn');

      // ===== Laravel flash/errors =====
      @if($errors->any())
        @foreach($errors->all() as $error)
          showAlert('{{ $error }}', 'danger', 'login-alert-container');
        @endforeach
      @endif

      @if(session('success'))
        showAlert('{{ session('success') }}', 'success', 'register-alert-container');
        document.getElementById('login-tab').click();
      @endif

      @if(session('error'))
        showAlert('{{ session('error') }}', 'danger', 'login-alert-container');
      @endif

      // Real-time validation nama
      nameInput?.addEventListener('input', function() {
        const nameValue = this.value;
        const validation = validateFullName(nameValue);
        const errorElement = document.getElementById('name-error');
        const successElement = document.getElementById('name-success');

        if (nameValue.length === 0) {
          this.classList.remove('is-invalid', 'is-valid');
          errorElement.style.display = 'none';
          successElement.style.display = 'none';
        } else if (!validation.isValid) {
          this.classList.add('is-invalid');
          this.classList.remove('is-valid');
          errorElement.textContent = validation.message;
          errorElement.style.display = 'block';
          successElement.style.display = 'none';
        } else {
          this.classList.add('is-valid');
          this.classList.remove('is-invalid');
          errorElement.style.display = 'none';
          successElement.style.display = 'block';
        }
      });

      // Real-time validation email
      emailInput?.addEventListener('input', function() {
        const emailValue = this.value;
        const errorElement = document.getElementById('email-error');
        const successElement = document.getElementById('email-success');
        const emailValidation = validateEmail(emailValue);

        if (emailValue.length === 0) {
          this.classList.remove('is-invalid', 'is-valid');
          errorElement.style.display = 'none';
          successElement.style.display = 'none';
        } else if (!emailValidation.isValid) {
          this.classList.add('is-invalid');
          this.classList.remove('is-valid');
          errorElement.textContent = emailValidation.message;
          errorElement.style.display = 'block';
          successElement.style.display = 'none';
        } else {
          this.classList.add('is-valid');
          this.classList.remove('is-invalid');
          errorElement.style.display = 'none';
          successElement.style.display = 'block';
        }
      });

      // Real-time validation password
      passwordInput?.addEventListener('input', function() {
        const passwordValue = this.value;
        const errorElement = document.getElementById('password-error');
        const validation = validatePassword(passwordValue);

        updatePasswordRequirements(validation.requirements);

        if (passwordValue.length === 0) {
          this.classList.remove('is-invalid', 'is-valid');
          errorElement.style.display = 'none';
        } else if (!validation.isValid) {
          this.classList.add('is-invalid');
          this.classList.remove('is-valid');
          errorElement.textContent = validation.message;
          errorElement.style.display = 'block';
        } else {
          this.classList.add('is-valid');
          this.classList.remove('is-invalid');
          errorElement.style.display = 'none';
        }
      });

      // Real-time validation konfirmasi password
      confirmPasswordInput?.addEventListener('input', function() {
        const confirmPasswordValue = this.value;
        const passwordValue = passwordInput.value;
        const errorElement = document.getElementById('password-confirmation-error');

        if (confirmPasswordValue.length === 0) {
          this.classList.remove('is-invalid', 'is-valid');
          errorElement.style.display = 'none';
        } else if (confirmPasswordValue !== passwordValue) {
          this.classList.add('is-invalid');
          this.classList.remove('is-valid');
          errorElement.textContent = 'Konfirmasi password tidak cocok dengan password';
          errorElement.style.display = 'block';
        } else {
          this.classList.add('is-valid');
          this.classList.remove('is-invalid');
          errorElement.style.display = 'none';
        }
      });

      // Login submit
      loginForm.addEventListener('submit', function(e) {
        const email = loginEmailInput.value.trim();
        const password = loginPasswordInput.value;

        if (!email || !password) {
          e.preventDefault();
          showAlert('Silakan masukkan email dan password.', 'warning', 'login-alert-container');
          return;
        }

        const emailValidation = validateEmail(email);
        if (!emailValidation.isValid) {
          e.preventDefault();
          showAlert(emailValidation.message, 'danger', 'login-alert-container');
          return;
        }

        setLoadingState(loginBtn, true);
      });

      // Register submit (ROLE DIPAKSA PENELITI)
      registerForm.addEventListener('submit', function(e) {
        const nameValue = nameInput.value.trim();
        const emailValue = emailInput.value.trim();
        const passwordValue = passwordInput.value;
        const confirmPasswordValue = confirmPasswordInput.value;

        if (roleInput) roleInput.value = 'peneliti';

        let isValid = true;
        let errorMessages = [];

        const nameValidation = validateFullName(nameValue);
        if (!nameValidation.isValid) { isValid = false; errorMessages.push(nameValidation.message); }

        const emailValidation = validateEmail(emailValue);
        if (!emailValidation.isValid) { isValid = false; errorMessages.push(emailValidation.message); }

        const passwordValidation = validatePassword(passwordValue);
        if (!passwordValidation.isValid) { isValid = false; errorMessages.push('Password tidak memenuhi kriteria yang diperlukan.'); }

        if (passwordValue !== confirmPasswordValue) { isValid = false; errorMessages.push('Konfirmasi password tidak cocok.'); }

        if (!isValid) {
          e.preventDefault();
          const errorMessage = errorMessages.length > 1
            ? `Terdapat ${errorMessages.length} kesalahan: ${errorMessages.join(', ')}`
            : errorMessages[0];
          showAlert(errorMessage, 'danger', 'register-alert-container');
          return false;
        }

        setLoadingState(registerBtn, true);
      });

      // Reset ketika pindah tab
      document.getElementById('login-tab').addEventListener('click', function() {
        loginForm.reset();
        document.getElementById('login-alert-container').innerHTML = '';
        document.querySelectorAll('#login .is-valid, #login .is-invalid').forEach(el => el.classList.remove('is-valid','is-invalid'));
        document.querySelectorAll('#login .invalid-feedback').forEach(el => el.style.display = 'none');
        setLoadingState(loginBtn, false);
      });

      document.getElementById('register-tab').addEventListener('click', function() {
        registerForm.reset();
        document.getElementById('register-alert-container').innerHTML = '';
        document.querySelectorAll('#register .is-valid, #register .is-invalid').forEach(el => el.classList.remove('is-valid','is-invalid'));
        document.querySelectorAll('#register .invalid-feedback, #register .valid-feedback').forEach(el => el.style.display = 'none');

        updatePasswordRequirements({ length:false, uppercase:false, lowercase:false, number:false });
        setLoadingState(registerBtn, false);
        if (roleInput) roleInput.value = 'peneliti';
      });
    });
  </script>
</body>
</html>
