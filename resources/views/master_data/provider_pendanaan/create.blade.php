@extends('layouts.panel')
@section('title', 'Tambah Provider Pendanaan')
@section('content')
<div class="card shadow-sm border-0 rounded-4 mt-3">
    <div class="card-body p-4">
        <h4 class="mb-4">Tambah Provider Pendanaan</h4>
        <form action="{{ route('provider-pendanaan.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Singkatan</label>
                    <input type="text" name="singkatan" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked value="1">
                <label class="form-check-label" for="is_active">Aktif</label>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save"></i> Simpan</button>
            <a href="{{ route('provider-pendanaan.index') }}" class="btn btn-light px-4">Batal</a>
        </form>
    </div>
</div>
@endsection