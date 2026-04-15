<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = collect();

        if (($user->role ?? '') === 'peneliti') {
            $projects = DB::table('detail_project as dp')
                ->join('project as p', 'dp.id_project', '=', 'p.id')
                ->where('dp.id_user', $user->id)
                ->select('p.id', 'p.nama_project', 'p.status', 'p.tahun')
                ->orderByDesc('p.tahun')
                ->orderBy('p.nama_project')
                ->get();
        }

        return view('profile.index', compact('user', 'projects'));
    }

    public function edit()
    {
        return $this->index();
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $isPeneliti = ($user->role ?? '') === 'peneliti';

        $rules = [
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        if ($isPeneliti) {
            $rules['name'] = 'required|string|max:255';
            if (Schema::hasColumn('users', 'jurusan')) {
                $rules['jurusan'] = 'nullable|string|max:120';
            }
            if (Schema::hasColumn('users', 'fakultas')) {
                $rules['fakultas'] = 'nullable|string|max:120';
            }
            if (Schema::hasColumn('users', 'nim_nip')) {
                $rules['nim_nip'] = 'nullable|string|max:50';
            }
            if (Schema::hasColumn('users', 'no_telp')) {
                $rules['no_telp'] = 'nullable|string|max:20';
            }
            if (Schema::hasColumn('users', 'profile_photo')) {
                $rules['profile_photo'] = 'nullable|image|mimes:jpg,jpeg,png|max:3072';
            }
        }

        $validated = $request->validate($rules);

        $user->email = $validated['email'];

        if ($isPeneliti) {
            $user->name = $validated['name'];
            if (Schema::hasColumn('users', 'jurusan')) {
                $user->jurusan = $validated['jurusan'] ?? null;
            }
            if (Schema::hasColumn('users', 'fakultas')) {
                $user->fakultas = $validated['fakultas'] ?? null;
            }
            if (Schema::hasColumn('users', 'nim_nip')) {
                $user->nim_nip = $validated['nim_nip'] ?? null;
            }
            if (Schema::hasColumn('users', 'no_telp')) {
                $user->no_telp = $validated['no_telp'] ?? null;
            }

            if ($request->hasFile('profile_photo') && Schema::hasColumn('users', 'profile_photo')) {
                if (!empty($user->profile_photo) && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $user->profile_photo = $request->file('profile_photo')->store('profile_photos', 'public');
            }
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.'])->withInput();
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Password berhasil diubah.');
    }
}
