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
        $request->validate ([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|string|in:admin,peneliti', 
        ]);

        try {
            $defaultPassword = $request->role === 'admin' ? 'Admin@123' : 'User@321';

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($defaultPassword),
            ]);

            return redirect()->route('users.index')->with('success', 'User  berhasil ditambahkan.');
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role' => 'required|string|in:admin,peneliti',
        ]);

        $user = User::findOrFail($id);
        
        // Update nama dan email
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Set password default sesuai role baru
        $defaultPassword = $request->role === 'admin' ? 'Admin@123' : 'User @123';
        $user->password = Hash::make($defaultPassword);

        $user->save();

        return redirect()->route('users.index')->with('success', 'User  berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User  berhasil dihapus.');
    }
}
