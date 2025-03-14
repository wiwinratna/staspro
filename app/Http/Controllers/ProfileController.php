<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index'); // Halaman utama profil
    }

    public function edit()
    {
        $user = Auth::user(); // Ambil data user yang sedang login
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|min:6',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validasi gambar
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Jika ada gambar baru, simpan
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika ada
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        // Jika request dari AJAX, kirim JSON response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui!',
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
            ]);
        }

        // Jika bukan AJAX, redirect biasa
        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}
