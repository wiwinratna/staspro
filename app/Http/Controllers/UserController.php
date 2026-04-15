<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'role'     => 'required|string|in:admin,peneliti,bendahara',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'role'     => $request->role,
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Gagal menambahkan user.');
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255',
            'role'     => 'required|string|in:admin,peneliti,bendahara',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);

        // Update nama, email, role
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        // Hanya ubah password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // CEK: user masih dipakai di tabel detail_project?
        $dipakai = \DB::table('detail_project')->where('id_user', $id)->exists();

        if ($dipakai) {
            return redirect()->route('users.index')
                ->with('error', 'User masih dipakai di detail project. Hapus/ubah relasi project dulu sebelum menghapus user.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * AJAX: ubah password user dari halaman daftar user.
     */
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password ' . $user->name . ' berhasil diubah.',
        ]);
    }

}
