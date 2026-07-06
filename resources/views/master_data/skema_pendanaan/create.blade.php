@extends('layouts.panel')
@section('title', 'Tambah Skema Pendanaan')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mt-3">
    <div class="card-body p-4">
        <h4 class="mb-4">Tambah Skema Pendanaan Baru</h4>
        <div class="alert alert-info">
            <strong>Catatan:</strong> Pada halaman ini Anda mengisi informasi dasar Skema. Konfigurasi Komponen Biaya akan dilakukan di langkah berikutnya.
        </div>
        
        <form action="{{ route('skema-pendanaan.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Kode Skema</label>
                    <input type="text" name="kode" class="form-control" placeholder="Contoh: PI" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Nama Skema</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Penelitian Internal" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Jenis Project</label>
                    <select name="jenis_project_id" class="form-select" required>
                        <option value="">-- Pilih Jenis Project --</option>
                        @foreach($jenisProjects as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Pendanaan</label>
                    <select name="jenis_pendanaan_id" class="form-select" required>
                        <option value="">-- Pilih Jenis Pendanaan --</option>
                        @foreach($jenisPendanaans as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Provider Pendanaan</label>
                    <select name="provider_id" class="form-select" required>
                        <option value="">-- Pilih Provider --</option>
                        @foreach($providers as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->singkatan }})</option>
                        @endforeach
                    </select>
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
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-arrow-right"></i> Lanjutkan ke Konfigurasi Komponen</button>
            <a href="{{ route('skema-pendanaan.index') }}" class="btn btn-light px-4">Batal</a>
        </form>
    </div>
</div>
@endsection
