@extends('layouts.app')

@section('title', 'Profile Page')

@section('content')
    <h1>Profile Page</h1>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <label for="name">Nama:</label>
        <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" required>

        <button type="submit">Update</button>
    </form>

@endsection
