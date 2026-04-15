<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\WelcomeResearcherMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // =========================
    // LOGIN PENELITI (PUBLIC)
    // =========================
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ⛔ kalau admin/bendahara nyasar login dari sini, lempar ke panel admin
            if (in_array($user->role, ['admin', 'bendahara'])) {
                Auth::logout();
                return redirect()->route('admin.login')
                    ->with('error', 'Akun Admin/Bendahara harus login lewat panel Admin.');
            }

            // ✅ Cek apakah profil peneliti sudah lengkap
            $profileIncomplete = empty($user->jurusan)
                              || empty($user->fakultas)
                              || empty($user->nim_nip)
                              || empty($user->no_telp);

            if ($profileIncomplete) {
                return redirect()->route('profile.index')
                    ->with('warning', 'Selamat datang kembali! Silakan lengkapi profil pengguna Anda terlebih dahulu sebelum menggunakan fitur utama.');
            }

            return redirect()->route('peneliti.dashboard')
                ->with('success', 'Login berhasil sebagai Peneliti!');
        }

        return back()
            ->withErrors(['email' => 'Oops! Email atau password salah.'])
            ->withInput();
    }

    // =========================
    // REGISTER PENELITI
    // =========================
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'peneliti', // DIPAKSA
        ]);

        // Kirim email sambutan ke peneliti baru
        try {
            Mail::to($user->email)->send(new WelcomeResearcherMail($user));
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan registrasi
            \Log::warning('Gagal mengirim email welcome: ' . $e->getMessage());
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Arahkan ke profil untuk melengkapi data
        return redirect()->route('profile.index')
            ->with('welcome', 'Registrasi berhasil! Selamat datang di STASPRO. Silakan lengkapi profil pengguna Anda terlebih dahulu sebelum menggunakan fitur utama.');
    }

    // =========================
    // LOGOUT (SEMUA ROLE)
    // =========================
    public function logout(Request $request)
    {
        $role = $request->input('redirect'); // dari hidden input navbar

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // admin & bendahara satu panel login
        if (in_array($role, ['admin', 'bendahara'])) {
            return redirect()->route('admin.login')
                ->with('success', 'Anda telah logout.');
        }

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }

    // kalau kamu di routes ada /admin/logout -> logoutAdmin, amanin biar ga 404 method
    public function logoutAdmin(Request $request)
    {
        // cukup panggil logout biasa
        return $this->logout($request);
    }

    // =========================
    // PANEL ADMIN (ADMIN + BENDAHARA)
    // =========================
    public function showAdminAuth()
    {
        return view('auth.admin-auth');
    }

    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email','password'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ✅ yang boleh masuk panel admin: admin + bendahara
            if (!in_array($user->role, ['admin', 'bendahara'])) {
                Auth::logout();
                return back()->with('error', 'Akun ini bukan Admin/Bendahara.');
            }

            // ✅ redirect sesuai role
            if ($user->role === 'bendahara') {
                return redirect()->route('bendahara.dashboard')
                    ->with('success', 'Login berhasil sebagai Bendahara!');
            }

            return redirect()->route('dashboard')
                ->with('success', 'Login berhasil sebagai Admin!');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function registerAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'admin_secret' => 'required|string',
        ]);

        $secret = env('ADMIN_REGISTER_SECRET', 'STASRG-ADMIN-2025');

        if ($request->admin_secret !== $secret) {
            return back()->with('error', 'Kode rahasia admin salah.')->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Registrasi Admin berhasil!');
    }
}
