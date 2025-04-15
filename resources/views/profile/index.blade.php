@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 text-center mb-8">Dashboard</h1>
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Selamat datang di aplikasi kami!</h2>
        <p class="text-gray-600">Ini adalah halaman utama dari index.blade.php.</p>
        <div class="text-center mt-6">
            <a href="{{ route('profile.edit') }}" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow">
                Edit Profil
            </a>
        </div>
    </div>
</div>
@endsection