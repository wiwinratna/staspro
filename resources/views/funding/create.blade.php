@extends('layouts.panel')

@section('title','Input Dana Cair')

@push('styles')
<style>
  .card-soft{
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:18px;
    box-shadow:0 10px 26px rgba(15,23,42,.06);
  }
  .help{ font-size:.86rem; color:rgba(15,23,42,.65); font-weight:600; }
  .req{ color:#ef4444; font-weight:800; }
</style>
@endpush

@section('content')
<div class="card-soft p-4">
  <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
    <div>
      <h4 class="mb-1" style="font-weight:900;">Input Dana Cair</h4>
      <div class="help">Dana cair dari pendanaan → masuk ke <b>project_funding</b> (bukan pencatatan_keuangan).</div>
    </div>
    <span class="badge rounded-pill text-bg-light border">
      Role: {{ strtoupper(Auth::user()->role) }}
    </span>
  </div>

  @if(session('success'))
    <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger mt-3 mb-0">
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif
</div>

<div class="card-soft p-4 mt-3">
  <form method="POST" action="{{ route('funding.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <label class="form-label fw-bold">Tim Project <span class="req">*</span></label>
        <select name="project_id" class="form-select" required>
          <option value="">Pilih project aktif</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
              {{ $p->nama_project }} ({{ $p->tahun }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label fw-bold">Tanggal Cair <span class="req">*</span></label>
        <input type="date" name="tanggal" class="form-control"
               value="{{ old('tanggal', now()->toDateString()) }}" required>
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label fw-bold">Nominal (Rp) <span class="req">*</span></label>
        <input type="text" name="nominal" id="nominal"
               class="form-control" placeholder="cth: 10.000.000"
               value="{{ old('nominal') }}" required>
        <div class="help mt-1">Boleh ketik pakai titik/koma/“Rp”.</div>
      </div>

      <div class="col-12 col-lg-4">
        <label class="form-label fw-bold">Metode Penerimaan</label>
        <select name="metode_penerimaan" class="form-select">
          <option value="">Pilih metode</option>
          <option value="TRANSFER BANK" {{ old('metode_penerimaan')=='TRANSFER BANK'?'selected':'' }}>TRANSFER BANK</option>
          <option value="TUNAI" {{ old('metode_penerimaan')=='TUNAI'?'selected':'' }}>TUNAI</option>
          <option value="LAINNYA" {{ old('metode_penerimaan')=='LAINNYA'?'selected':'' }}>LAINNYA</option>
        </select>
      </div>

      <div class="col-12 col-lg-8">
        <label class="form-label fw-bold">Sumber Dana (opsional)</label>
        <input type="text" name="sumber_dana" class="form-control"
               placeholder="cth: DRPTM / Kedaireka / Internal Lab"
               value="{{ old('sumber_dana') }}">
      </div>

      <div class="col-12">
        <label class="form-label fw-bold">Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="3"
                  placeholder="cth: Dana tahap 1 cair untuk Project Crowded Detection">{{ old('keterangan') }}</textarea>
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label fw-bold">Bukti (JPG/PNG/PDF, max 5MB)</label>
        <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
      </div>
    </div>

    <div class="d-flex gap-2 mt-4">
      <button class="btn btn-success fw-bold" type="submit">
        <i class="bi bi-save2"></i> Simpan
      </button>
      <a href="{{ route('dashboard') }}" class="btn btn-light border fw-bold">Batal</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
  // Format rupiah sederhana saat ngetik
  const el = document.getElementById('nominal');
  if(el){
    el.addEventListener('input', () => {
      const digits = el.value.replace(/[^\d]/g,'');
      if(!digits) { el.value=''; return; }
      el.value = new Intl.NumberFormat('id-ID').format(parseInt(digits,10));
    });
  }
</script>
@endpush
@endsection
