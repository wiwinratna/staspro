<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index() { return view('profile.index'); }
    public function edit() { return view('profile.edit'); }
    public function update(Request $request) { return back()->with('success','Profile updated'); }
    public function updatePassword(Request $request) { return back()->with('success','Password updated'); }
}
