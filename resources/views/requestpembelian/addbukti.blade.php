{{-- resources/views/requestpembelian/addbukti.blade.php --}}
@extends('layouts.panel')

@section('title', 'Upload Invoice Item')

@push('styles')
<style>
    .page-title{ font-size:1.55rem; font-weight:800; }
    .page-sub{ color:var(--ink-600); margin-top:4px; }

    /* Card */
    .card-soft{
      background:var(--card);
      border:1px solid var(--line);
      border-radius:18px;
      box-shadow:0 8px 22px rgba(15,23,42,.06);
    }

    /* tombol */
    .btn-brand{
      background:var(--brand) !important;
      border-color:var(--brand) !important;
      color:#fff !important;
      font-weight:800;
      padding:.6rem 1.2rem;
      border-radius:12px;
    }
    .btn-brand:hover{
      background:var(--brand-700) !important;
      border-color:var(--brand-700) !important;
      color:#fff !important;
    }
</style>
@endpush

@section('content')
@php
  // ambil header (kalau controller belum kirim)
  $header = $header ?? \App\Models\RequestpembelianHeader::find($detail->id_request_pembelian_header);

  $isApprover = in_array(Auth::user()->role, ['admin','bendahara'], true);

  // normalisasi status
  $statusHeaderRaw = $header->status_request ?? '';
  $statusHeader = strtolower(trim($statusHeaderRaw));
  $statusHeader = str_replace(' ', '_', $statusHeader);

  // ✅ FIX: izinkan upload juga saat submit_payment (biar bisa nyicil upload semua item)
  $allowUpload = $isApprover && $statusHeader !== 'reject_request';
@endphp

    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
      <div>
        <div class="page-title">Upload Invoice Item</div>
        <div class="page-sub">
          Upload invoice item dilakukan oleh admin/bendahara untuk komponen ini.
        </div>
      </div>

      <a href="{{ route('requestpembelian.detail', $detail->id_request_pembelian_header) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left-short me-1"></i> Kembali
      </a>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
      <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger mt-3">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card card-soft mt-3">
      <div class="card-body">

        <div class="mb-3">
          <div class="fw-bold mb-1">Info</div>
          <div class="text-muted" style="font-size:.92rem">
            Nomor Request: <b>{{ $header->no_request ?? '-' }}</b> •
            Status: <b>{{ $statusHeader ?: '-' }}</b>
          </div>
        </div>

        @if(!$header)
          <div class="alert alert-danger mb-0">
            Data header tidak ditemukan. Coba kembali ke halaman detail.
          </div>

        @elseif(!$allowUpload)
          <div class="alert alert-warning mb-0">
            Upload invoice tidak tersedia untuk status saat ini.
          </div>

        @else
          <form action="{{ route('requestpembelian.storebukti', $detail->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="id_request_pembelian_header" value="{{ $detail->id_request_pembelian_header }}">

            <div class="mb-3">
              <label class="form-label fw-bold" for="no_invoice">Nomor Invoice <span class="text-muted fw-normal">(Opsional)</span></label>
              <input type="text" class="form-control" id="no_invoice" name="no_invoice" placeholder="Cth: INV-2026/04/123" value="{{ $detail->no_invoice ?? '' }}">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold" for="bukti_bayar">File Invoice</label>
              <input type="file" class="form-control" id="bukti_bayar" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required>
              <div class="form-text">Format: JPG/JPEG/PNG/PDF. Maks 5MB.</div>
            </div>

            <button type="submit" class="btn btn-brand">
              <i class="bi bi-upload me-1"></i> Kirim Invoice
            </button>
          </form>
        @endif

      </div>
    </div>

@endsection
