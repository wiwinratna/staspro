<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileComplete
{
    /**
     * Peneliti harus melengkapi profil sebelum mengakses fitur utama.
     * Field minimum: jurusan, fakultas, nim_nip, no_telp
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Hanya berlaku untuk role peneliti
        if (($user->role ?? '') !== 'peneliti') {
            return $next($request);
        }

        // Cek apakah profil lengkap (field minimum terpenuhi)
        $isComplete = !empty($user->jurusan)
                   && !empty($user->fakultas)
                   && !empty($user->nim_nip)
                   && !empty($user->no_telp);

        if (!$isComplete) {
            // Izinkan akses ke halaman profil, logout, dan update profil
            $allowed = [
                'profile.index',
                'profile.edit',
                'profile.update',
                'password.update',
                'logout',
            ];

            if (in_array($request->route()?->getName(), $allowed)) {
                return $next($request);
            }

            return redirect()->route('profile.index')
                ->with('warning', 'Selamat datang di STASPRO! Sebelum menggunakan aplikasi, silakan lengkapi profil pengguna terlebih dahulu.');
        }

        return $next($request);
    }
}
