@extends('layouts.panel')
@section('title', 'Edit Jenis Pendanaan')
@section('content')
<div class="card shadow-sm border-0 rounded-4 mt-3">
    <div class="card-body p-4">
        <h4 class="mb-4">Edit Jenis Pendanaan</h4>
        <form action="{{ route('jenis-pendanaan.update', $data->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ $data->nama }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode</label>
                    <input type="text" name="kode" class="form-control" value="{{ $data->kode ?? $data->singkatan }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ $data->deskripsi }}</textarea>
            </div>
            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $data->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Aktif</label>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save"></i> Simpan Perubahan</button>
            <a href="{{ route('jenis-pendanaan.index') }}" class="btn btn-light px-4">Batal</a>
        </form>
    </div>
</div>
@endsection