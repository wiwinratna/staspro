<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function index()
    {
        return view('login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Redirect sesuai role
            return $user->role === 'admin'
            ? redirect()->route('dashboard')->with('success', 'Login berhasil sebagai Admin!')
            : redirect()->route('peneliti.dashboard')->with('success', 'Login berhasil sebagai Peneliti!');
        }

        return back()->withErrors(['email' => 'Oops! Email atau password salah.'])->withInput();
    }

    // Tampilkan halaman register
    public function showRegisterForm()
    {
        return view('auth.register'); 
    }

    // Proses registrasi
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
        'role'     => 'peneliti', // 🔒 DIPAKSA
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->route('dashboard')->with('success', 'Registrasi berhasil sebagai Peneliti.');
}


public function logout(Request $request)
{
    $role = $request->input('redirect');

    Session::flush();
    Auth::logout();

    if ($role === 'admin') {
        return redirect('/admin/login')
            ->with('success', 'Anda telah logout.');
    }

    return redirect()->route('login')
        ->with('success', 'Anda telah logout.');
}

    public function showAdminLogin()
{
    return view('auth.admin-login');
}

public function showAdminAuth()
{
    return view('auth.admin-auth');
}

public function loginAdmin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email','password'))) {
        $user = Auth::user();

        // pastikan yang login beneran admin
        if ($user->role !== 'admin') {
            Auth::logout();
            return back()->with('error', 'Akun ini bukan Admin.');
        }

        return redirect()->route('dashboard')->with('success', 'Login berhasil sebagai Admin!');
    }

    return back()->with('error', 'Email atau password salah.');
}

public function registerAdmin(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'admin_secret' => 'required|string',
    ]);

    // ✅ Kode rahasia admin (taruh di .env biar aman)
    $secret = env('ADMIN_REGISTER_SECRET', 'STASRG-ADMIN-2025');

    if ($request->admin_secret !== $secret) {
        return back()->with('error', 'Kode rahasia admin salah.')->withInput();
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'admin', // ✅ paksa admin, tidak bisa dipilih user
    ]);

    Auth::login($user);

    return redirect()->route('dashboard')->with('success', 'Registrasi Admin berhasil!');
}

}
