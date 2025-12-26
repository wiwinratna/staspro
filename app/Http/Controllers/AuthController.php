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
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        Auth::login($user);

        return redirect()->route('peneliti.dashboard')->with('success', 'Registrasi berhasil!');
    }

    // Proses logout
    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}
