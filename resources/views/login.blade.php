<!DOCTYPE html>
<html lang="id">

<head>
    @extends('layouts.app')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
        font-family: Arial, sans-serif;
        margin: 0;
        min-height: 100vh;

        /* card di kanan */
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: clamp(200px, 6vw, 150px);

        /* layer 1 = overlay fade (kiri transparan → kanan putih)
            layer 2 = foto di KIRI
            layer 3 = fallback warna */
        background:
        linear-gradient(to left,
            rgba(255,255,255,0.95) 0%,
            rgba(255,255,255,0.92) 35%,
            rgba(255,255,255,0.85) 55%,
            rgba(255,255,255,0.6) 70%,
            rgba(255,255,255,0.3) 100%),
        url('/images/login-bg.png') left center / cover no-repeat,
        #f5f7f5;
        }

        .card {
        width: 500px;           
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(0,0,0,.08);
        overflow: hidden;
        }

        .card-header {
            background-color: #006400;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .nav-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            border-bottom: none;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
            color: #6c757d;
            transition: all 0.3s ease-in-out;
        }

        .nav-tabs .nav-link.active {
            background-color: #006400;
            color: white;
        }

        .form-control {
            height: 45px;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            transition: border 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #006400;
            box-shadow: 0 0 5px rgba(42, 208, 0, 0.5);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-select {
            height: 45px;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            transition: border 0.3s ease-in-out;
        }

        .form-select:focus {
            border-color: #006400;
            box-shadow: 0 0 5px rgba(42, 208, 0, 0.5);
            outline: none;
        }

        .btn-primary {
            background-color: #006400;
            border: none;
            height: 45px;
            border-radius: 15px;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #249C00;
        }

        .btn-primary:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .valid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #198754;
        }

        .password-requirements {
            font-size: 0.8em;
            color: #6c757d;
            margin-top: 5px;
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }

        .password-requirements li {
            margin: 2px 0;
        }

        .password-requirements li.valid {
            color: #198754;
        }

        .password-requirements li.invalid {
            color: #dc3545;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .loading {
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <h3>Selamat Datang</h3>
        </div>
        <div class="card-body">
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
                <!-- Login Form -->
                <div class="tab-pane fade show active" id="login" role="tabpanel">
                    <div id="login-alert-container"></div>
                    <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                        @csrf
                        <div class="mb-3">
                            <label for="login-email" class="form-label">Email</label>
                            <input type="email" id="login-email" name="email" class="form-control"
                                placeholder="Masukkan email Anda" required value="{{ old('email') }}">
                            <div class="invalid-feedback" id="login-email-error" style="display: none;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="form-label">Password</label>
                            <input type="password" id="login-password" name="password" class="form-control"
                                placeholder="Masukkan password Anda" required>
                            <div class="invalid-feedback" id="login-password-error" style="display: none;"></div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="loginBtn">
                                <span class="btn-text">Login</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Register Form -->
                <div class="tab-pane fade" id="register" role="tabpanel">
                    <div id="register-alert-container"></div>
                    <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Masukkan nama lengkap Anda" required value="{{ old('name') }}">
                            <div class="invalid-feedback" id="name-error" style="display: none;"></div>
                            <div class="valid-feedback" id="name-success" style="display: none;">
                                <i class="bi bi-check-circle"></i> Nama lengkap valid
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="register-email" class="form-label">Email</label>
                            <input type="email" id="register-email" name="email" class="form-control"
                                placeholder="Masukkan email Anda (contoh: nama@domain.com)" required value="{{ old('email') }}">
                            <div class="invalid-feedback" id="email-error" style="display: none;"></div>
                            <div class="valid-feedback" id="email-success" style="display: none;">
                                <i class="bi bi-check-circle"></i> Email tersedia
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="register-password" class="form-label">Password</label>
                                <input type="password" id="register-password" name="password" class="form-control"
                                    placeholder="Buat password" required autocomplete="new-password" minlength="8">
                                <div class="invalid-feedback" id="password-error" style="display: none;"></div>
                                <div class="password-requirements" id="password-requirements">
                                    <small>Password harus memenuhi kriteria berikut:</small>
                                    <ul>
                                        <li id="length-req">Minimal 8 karakter</li>
                                        <li id="uppercase-req">Mengandung huruf besar</li>
                                        <li id="lowercase-req">Mengandung huruf kecil</li>
                                        <li id="number-req">Mengandung angka</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control" placeholder="Konfirmasi password" required minlength="8">
                                <div class="invalid-feedback" id="password-confirmation-error" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select id="role" name="role" class="form-select" required>
                                <option value="" disabled selected>Pilih role Anda</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="peneliti" {{ old('role') == 'peneliti' ? 'selected' : '' }}>Peneliti</option>
                            </select>
                            <div class="invalid-feedback" id="role-error" style="display: none;"></div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="registerBtn">
                                <span class="btn-text">Register</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi nama lengkap yang ditingkatkan
        function validateFullName(name) {
            const nameRegex = /^[a-zA-Z\s\-'\.]+$/;
            name = name.trim();
            
            if (name.length < 2) {
                return {
                    isValid: false,
                    message: "Nama lengkap minimal 2 karakter"
                };
            }
            
            if (name.length > 50) {
                return {
                    isValid: false,
                    message: "Nama lengkap maksimal 50 karakter"
                };
            }
            
            if (!nameRegex.test(name)) {
                return {
                    isValid: false,
                    message: "Nama lengkap tidak boleh mengandung angka atau simbol khusus. Hanya huruf, spasi, tanda hubung (-), apostrof ('), dan titik (.) yang diizinkan."
                };
            }
            
            if (name.replace(/\s/g, '').length === 0) {
                return {
                    isValid: false,
                    message: "Nama lengkap tidak boleh hanya berisi spasi"
                };
            }
            
            if (name !== name.trim()) {
                return {
                    isValid: false,
                    message: "Nama lengkap tidak boleh dimulai atau diakhiri dengan spasi"
                };
            }
            
            return {
                isValid: true,
                message: "Nama lengkap valid"
            };
        }

        // Validasi email yang ditingkatkan
        function validateEmail(email) {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            if (!email || email.trim() === '') {
                return {
                    isValid: false,
                    message: "Email tidak boleh kosong"
                };
            }
            
            if (!emailRegex.test(email)) {
                return {
                    isValid: false,
                    message: "Format email tidak valid. Gunakan format yang benar seperti: nama@domain.com"
                };
            }
            
            return {
                isValid: true,
                message: "Format email valid"
            };
        }

        // Validasi password yang ditingkatkan
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

        // Fungsi untuk menampilkan alert
        function showAlert(message, type = 'danger', containerId = 'register-alert-container') {
            const alertContainer = document.getElementById(containerId);
            let icon = 'bi-exclamation-triangle-fill';
            
            if (type === 'success') {
                icon = 'bi-check-circle-fill';
            } else if (type === 'warning') {
                icon = 'bi-exclamation-triangle-fill';
            } else if (type === 'info') {
                icon = 'bi-info-circle-fill';
            }
            
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="bi ${icon}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHtml;
            
            // Auto remove alert after 7 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 7000);
        }

        // Fungsi untuk update password requirements UI
        function updatePasswordRequirements(requirements) {
            const elements = {
                length: document.getElementById('length-req'),
                uppercase: document.getElementById('uppercase-req'),
                lowercase: document.getElementById('lowercase-req'),
                number: document.getElementById('number-req')
            };

            Object.keys(requirements).forEach(key => {
                const element = elements[key];
                if (element) {
                    element.className = requirements[key] ? 'valid' : 'invalid';
                }
            });
        }

        // Fungsi untuk menampilkan loading state
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

        // Event listeners
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

            // Tampilkan error dari Laravel jika ada
            @if($errors->any())
                @foreach($errors->all() as $error)
                    showAlert('{{ $error }}', 'danger', 'login-alert-container');
                @endforeach
            @endif

            @if(session('success'))
                showAlert('{{ session('success') }}', 'success', 'register-alert-container');
                // Pindah ke tab login
                document.getElementById('login-tab').click();
            @endif

            @if(session('error'))
                showAlert('{{ session('error') }}', 'danger', 'login-alert-container');
            @endif

            // Real-time validation untuk nama lengkap
            nameInput.addEventListener('input', function() {
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

            // Real-time validation untuk email
            emailInput.addEventListener('input', function() {
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

            // Real-time validation untuk password
            passwordInput.addEventListener('input', function() {
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

            // Real-time validation untuk konfirmasi password
            confirmPasswordInput.addEventListener('input', function() {
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

            // Validation untuk role
            roleInput.addEventListener('change', function() {
                const errorElement = document.getElementById('role-error');
                
                if (this.value) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    errorElement.textContent = 'Silakan pilih role Anda';
                    errorElement.style.display = 'block';
                }
            });

            // Login form submission - DIHAPUS PREVENTDEFAULT, BIARKAN FORM SUBMIT KE SERVER
            loginForm.addEventListener('submit', function(e) {
                const email = loginEmailInput.value.trim();
                const password = loginPasswordInput.value;

                if (!email || !password) {
                    e.preventDefault();
                    showAlert('Silakan masukkan email dan password.', 'warning', 'login-alert-container');
                    return;
                }

                // Validasi format email
                const emailValidation = validateEmail(email);
                if (!emailValidation.isValid) {
                    e.preventDefault();
                    showAlert(emailValidation.message, 'danger', 'login-alert-container');
                    return;
                }

                // Tampilkan loading state
                setLoadingState(loginBtn, true);
                
                // Biarkan form submit ke server Laravel
                // Laravel akan handle authentication dan redirect
            });

            // Register form submission
            registerForm.addEventListener('submit', function(e) {
                const nameValue = nameInput.value.trim();
                const emailValue = emailInput.value.trim();
                const passwordValue = passwordInput.value;
                const confirmPasswordValue = confirmPasswordInput.value;
                const roleValue = roleInput.value;

                let isValid = true;
                let errorMessages = [];

                // Validasi nama lengkap
                const nameValidation = validateFullName(nameValue);
                if (!nameValidation.isValid) {
                    isValid = false;
                    errorMessages.push(nameValidation.message);
                }

                // Validasi email
                const emailValidation = validateEmail(emailValue);
                if (!emailValidation.isValid) {
                    isValid = false;
                    errorMessages.push(emailValidation.message);
                }

                // Validasi password
                const passwordValidation = validatePassword(passwordValue);
                if (!passwordValidation.isValid) {
                    isValid = false;
                    errorMessages.push('Password tidak memenuhi kriteria yang diperlukan.');
                }

                // Validasi konfirmasi password
                if (passwordValue !== confirmPasswordValue) {
                    isValid = false;
                    errorMessages.push('Konfirmasi password tidak cocok.');
                }

                // Validasi role
                if (!roleValue) {
                    isValid = false;
                    errorMessages.push('Silakan pilih role Anda.');
                }

                if (!isValid) {
                    e.preventDefault();
                    const errorMessage = errorMessages.length > 1 
                        ? `Terdapat ${errorMessages.length} kesalahan: ${errorMessages.join(', ')}`
                        : errorMessages[0];
                    showAlert(errorMessage, 'danger', 'register-alert-container');
                    return false;
                }

                // Tampilkan loading state
                setLoadingState(registerBtn, true);
                
                // Biarkan form submit ke server Laravel
            });

            // Reset validation ketika pindah tab
            document.getElementById('login-tab').addEventListener('click', function() {
                loginForm.reset();
                document.getElementById('login-alert-container').innerHTML = '';
                document.querySelectorAll('#login .is-valid, #login .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                });
                document.querySelectorAll('#login .invalid-feedback').forEach(el => {
                    el.style.display = 'none';
                });
                setLoadingState(loginBtn, false);
            });

            document.getElementById('register-tab').addEventListener('click', function() {
                registerForm.reset();
                document.getElementById('register-alert-container').innerHTML = '';
                document.querySelectorAll('#register .is-valid, #register .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                });
                document.querySelectorAll('#register .invalid-feedback, #register .valid-feedback').forEach(el => {
                    el.style.display = 'none';
                });
                updatePasswordRequirements({
                    length: false,
                    uppercase: false,
                    lowercase: false,
                    number: false
                });
                setLoadingState(registerBtn, false);
            });
        });
    </script>
</body>

</html>