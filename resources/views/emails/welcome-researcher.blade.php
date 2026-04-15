<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Selamat Datang di STASPRO</title>
</head>
<body style="margin:0; padding:0; background:#f6f7fb; font-family:'Segoe UI',Arial,Helvetica,sans-serif;">
  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f6f7fb; padding:40px 20px;">
    <tr>
      <td align="center">
        <table role="presentation" cellpadding="0" cellspacing="0" width="560" style="background:#ffffff; border-radius:20px; box-shadow:0 10px 40px rgba(15,23,42,.08); overflow:hidden;">

          {{-- Header --}}
          <tr>
            <td style="background:linear-gradient(135deg,#15803d,#16a34a); padding:36px 40px 28px; text-align:center;">
              <div style="display:inline-block; width:56px; height:56px; border-radius:14px; background:rgba(255,255,255,.18); line-height:56px; font-size:24px; color:#fff; font-weight:800; margin-bottom:14px;">
                🎓
              </div>
              <h1 style="margin:0; color:#fff; font-size:24px; font-weight:800; letter-spacing:-.3px;">
                Selamat Datang di STASPRO!
              </h1>
              <p style="margin:8px 0 0; color:rgba(255,255,255,.85); font-size:14px; font-weight:500;">
                Sistem Tata Kelola Keuangan & Administrasi Riset
              </p>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:32px 40px;">
              <p style="margin:0 0 18px; color:#0f172a; font-size:16px; font-weight:700;">
                Halo, {{ $user->name }}! 👋
              </p>
              <p style="margin:0 0 16px; color:#334155; font-size:14px; line-height:1.65;">
                Akun peneliti Anda telah berhasil dibuat. Anda sekarang dapat mengakses STASPRO untuk mengelola pengajuan dana, reimbursement, dan kebutuhan administrasi riset lainnya.
              </p>

              {{-- Info Box --}}
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f0fdf4; border:1px solid rgba(22,163,74,.18); border-radius:14px; margin:20px 0;">
                <tr>
                  <td style="padding:20px 24px;">
                    <p style="margin:0 0 10px; color:#166534; font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;">
                      ⚡ Langkah Selanjutnya
                    </p>
                    <p style="margin:0 0 8px; color:#15803d; font-size:14px; line-height:1.5;">
                      Silakan lengkapi <strong>Profil Pengguna</strong> Anda terlebih dahulu sebelum menggunakan fitur utama aplikasi. Profil yang lengkap membantu tim mengelola administrasi dengan lebih baik.
                    </p>
                    <p style="margin:0; color:#15803d; font-size:13px;">
                      Data yang perlu dilengkapi: <strong>Jurusan, Fakultas, NIM/NIP, dan No. Telepon</strong>.
                    </p>
                  </td>
                </tr>
              </table>

              {{-- CTA Button --}}
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:24px 0;">
                <tr>
                  <td align="center">
                    <a href="{{ url('/profile') }}"
                       style="display:inline-block; padding:14px 36px; background:linear-gradient(135deg,#15803d,#16a34a);
                              color:#fff; font-size:14px; font-weight:800; text-decoration:none;
                              border-radius:999px; box-shadow:0 8px 24px rgba(22,163,74,.22);">
                      Lengkapi Profil Sekarang →
                    </a>
                  </td>
                </tr>
              </table>

              {{-- Details --}}
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; margin:20px 0;">
                <tr>
                  <td style="padding:16px 20px;">
                    <p style="margin:0 0 8px; color:#64748b; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;">
                      Detail Akun Anda
                    </p>
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                        <td style="padding:4px 0; color:#64748b; font-size:13px; width:80px;">Nama</td>
                        <td style="padding:4px 0; color:#0f172a; font-size:13px; font-weight:700;">{{ $user->name }}</td>
                      </tr>
                      <tr>
                        <td style="padding:4px 0; color:#64748b; font-size:13px;">Email</td>
                        <td style="padding:4px 0; color:#0f172a; font-size:13px; font-weight:700;">{{ $user->email }}</td>
                      </tr>
                      <tr>
                        <td style="padding:4px 0; color:#64748b; font-size:13px;">Role</td>
                        <td style="padding:4px 0; color:#0f172a; font-size:13px; font-weight:700;">Peneliti</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <p style="margin:20px 0 0; color:#94a3b8; font-size:13px; line-height:1.5;">
                Jika Anda tidak merasa mendaftarkan akun ini, silakan abaikan email ini.
              </p>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="padding:20px 40px 28px; text-align:center; border-top:1px solid #f1f5f9;">
              <p style="margin:0; color:#94a3b8; font-size:12px; font-weight:600;">
                © {{ date('Y') }} STAS-RG — Sistem Tata Kelola Keuangan & Administrasi Riset
              </p>
              <p style="margin:4px 0 0; color:#cbd5e1; font-size:11px;">
                Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
