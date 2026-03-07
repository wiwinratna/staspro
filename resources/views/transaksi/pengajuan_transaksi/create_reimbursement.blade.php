{{-- resources/views/transaksi/pengajuan_transaksi/create_reimbursement.blade.php --}}
@extends('layouts.panel')

@section('title', 'Reimbursement')

@push('styles')
<style>
  .hero{ border-radius:22px; padding:18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow: var(--shadow);
    position:relative; overflow:hidden; margin-bottom:14px;
  }
  .hero::after{ content:""; position:absolute; inset:-1px;
    background:
      radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
      radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
    pointer-events:none; opacity:.65;
  }
  .hero-inner{ position:relative; z-index:2; }
  .title{ font-size:1.65rem; font-weight:800; margin:0; letter-spacing:-.2px; }
  .sub{ margin:6px 0 0; color:var(--ink-600); font-weight:500; }

  .btn-brand{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:800; padding:0 14px;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    border:0; color:#fff; text-decoration:none; white-space:nowrap;
    box-shadow:0 16px 28px rgba(22,163,74,.18);
  }
  .btn-soft{
    height:38px; display:inline-flex; align-items:center; gap:8px;
    border-radius:999px; font-weight:800; padding:0 14px;
    background:#fff; color:var(--ink); text-decoration:none; white-space:nowrap;
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 10px 26px rgba(15,23,42,.05);
  }

  .card-form{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    padding:14px;
    box-shadow:var(--shadow);
  }
  .label{
    font-weight:900; font-size:.78rem; letter-spacing:.06em;
    text-transform:uppercase; color:var(--ink-600);
    margin-bottom:6px;
  }
  .field{
    height:38px; border-radius:999px;
    border:1px solid rgba(226,232,240,.95);
    padding:0 12px; font-weight:700; background:#fff;
  }
  textarea.field{ height:auto; border-radius:16px; padding:12px; }
</style>
@endpush

@section('content')

  <section class="hero">
    <div class="hero-inner">
      <h1 class="title">Reimbursement</h1>
      <p class="sub">Ajukan reimbursement setelah pembelian. Bukti wajib diupload dari awal.</p>

      <div class="d-flex gap-2 flex-wrap mt-3">
        <a href="{{ route('pengajuan_transaksi.index') }}" class="btn-soft">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
  </section>

  @if($errors->any())
    <div class="alert alert-danger mt-3">
      <div class="fw-bold mb-1">Periksa input:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="card-form">
    <form method="POST" action="{{ route('pengajuan_transaksi.store_reimbursement') }}" enctype="multipart/form-data">
      @csrf

      <input type="hidden" name="tipe" value="reimbursement">
      <input type="hidden" name="jenis_transaksi" value="pengeluaran">

      <div class="row g-3">
        <div class="col-md-6">
          <div class="label">Project (Tim) *</div>
          <select name="id_project" id="projectSelect" class="form-select field" required>
            <option value="">-- Pilih Project --</option>
            @foreach($projects as $p)
              <option value="{{ $p->id }}" {{ old('id_project')==$p->id ? 'selected' : '' }}>
                {{ $p->nama_project }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <div class="label">Sub Kategori Sumber Dana *</div>
          <select name="id_subkategori_sumberdana" id="subkategoriSelect" class="form-select field" required>
            <option value="">-- Pilih Sub Kategori --</option>
          </select>
        </div>

        <div class="col-12">
          <div class="label">Deskripsi *</div>
          <textarea name="deskripsi" class="form-control field" rows="3" placeholder="Tulis detail transaksi..." required>{{ old('deskripsi') }}</textarea>
        </div>

        <div class="col-md-4">
          <div class="label">Estimasi Nominal</div>
          <input type="number" name="estimasi_nominal" class="form-control field" value="{{ old('estimasi_nominal') }}">
        </div>

        <div class="col-md-4">
          <div class="label">Tanggal Request *</div>
          <input type="date" name="tgl_request" class="form-control field" value="{{ old('tgl_request') }}" required>
        </div>

        <div class="col-md-4">
          <div class="label">Metode Pembayaran (Preferensi)</div>
          <select name="metode_pembayaran" class="form-select field">
            <option value="">-</option>
            <option value="transfer" {{ old('metode_pembayaran')==='transfer' ? 'selected' : '' }}>Transfer</option>
            <option value="cash" {{ old('metode_pembayaran')==='cash' ? 'selected' : '' }}>Cash</option>
          </select>
        </div>

        <div class="col-md-6">
          <div class="label">Nama Bank *</div>
          <input type="text" name="nama_bank" class="form-control field" value="{{ old('nama_bank') }}" required>
        </div>

        <div class="col-md-6">
          <div class="label">No Rekening *</div>
          <input type="text" name="no_rekening" class="form-control field" value="{{ old('no_rekening') }}" required>
        </div>

        <div class="col-md-4">
          <div class="label">Tanggal Bukti *</div>
          <input type="date" name="tgl_bukti" class="form-control field" value="{{ old('tgl_bukti') }}" required>
        </div>

        <div class="col-md-4">
          <div class="label">Nominal Realisasi *</div>
          <input type="number" name="nominal_realisasi" class="form-control field" value="{{ old('nominal_realisasi') }}" required>
        </div>

        <div class="col-md-4">
          <div class="label">Upload Bukti *</div>
          <input type="file" name="bukti_file" class="form-control field" accept="image/*" required>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
          <a href="{{ route('pengajuan_transaksi.index') }}" class="btn-soft">
            <i class="bi bi-x-lg"></i> Batal
          </a>
          <button type="submit" class="btn-brand">
            <i class="bi bi-send"></i> Submit Reimbursement
          </button>
        </div>
      </div>
    </form>
  </div>

@endsection

@push('scripts')
<script>
  (function(){
    const project = document.getElementById('projectSelect');
    const subkat  = document.getElementById('subkategoriSelect');
    const oldSub  = "{{ old('id_subkategori_sumberdana') }}";

    function clear(){
      subkat.innerHTML = `<option value="">-- Pilih Sub Kategori --</option>`;
    }

    async function load(projectId){
      clear();
      if(!projectId) return;

      const url = "{{ route('pengajuan_transaksi.subkategori', ['project' => '__ID__']) }}".replace('__ID__', projectId);
      const res = await fetch(url, { headers:{ 'Accept':'application/json' }});
      const data = await res.json();

      data.forEach(item=>{
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.textContent = item.nama;
        if(oldSub && String(oldSub)===String(item.id)) opt.selected = true;
        subkat.appendChild(opt);
      });
    }

    project?.addEventListener('change', e=> load(e.target.value));
    const initProject = project?.value;
    if(initProject) load(initProject);
  })();
</script>
@endpush
