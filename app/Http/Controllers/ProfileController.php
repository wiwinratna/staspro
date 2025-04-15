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
        $user = Auth::user();
        return view('profile.index'); // Halaman utama profil
    }

    public function edit()
    {
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
    
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
    
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui!',
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        // Redirect untuk request non-AJAX
        return redirect()->route('dashboard')->with('success', 'Profile successfully updated!');
    }
}
