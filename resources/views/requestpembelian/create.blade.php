{{-- resources/views/requestpembelian/create.blade.php --}}
@extends('layouts.panel')

@section('title', 'Tambah Request Pembelian')

@push('styles')
<style>
    .page-title{ font-size:1.55rem; font-weight:900; margin:0; }
    .page-sub{ color:var(--ink-600); margin:6px 0 0; }

    /* Card */
    .card-soft{
      background:var(--card);
      border:1px solid var(--line);
      border-radius:18px;
    }

    .btn-brand{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0;
      color:#fff;
      font-weight:800;
      border-radius:12px;
      padding:.6rem 1rem;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
    }
    .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }
</style>
@endpush

@section('content')

      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <h1 class="page-title">Tambah Request Pembelian</h1>
          <p class="page-sub">Pilih tim penelitian dan tanggal request.</p>
        </div>

        <a href="{{ route('requestpembelian.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-short me-1"></i> Kembali ke Daftar
        </a>
      </div>

      <div class="card card-soft">
        <div class="card-body p-4">
          <form action="{{ route('requestpembelian.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-8">
              <label for="id_project" class="form-label fw-semibold">Tim Penelitian</label>
              <select class="form-select" id="id_project" name="id_project" required>
                <option value="" selected disabled>-- Pilih Tim Penelitian --</option>
                @foreach ($project as $p)
                  <option value="{{ $p->id }}">{{ $p->nama_project }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label for="tgl_request" class="form-label fw-semibold">Tanggal Request</label>
              <input type="date" id="tgl_request" name="tgl_request" class="form-control" max="{{ date('Y-m-d') }}" required>
            </div>

            <div class="col-12 d-flex gap-2 pt-2">
              <button class="btn btn-brand" type="submit">
                <i class="bi bi-check2-circle me-1"></i> Submit
              </button>
              <a href="{{ route('requestpembelian.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
          </form>
        </div>
      </div>

@endsection
