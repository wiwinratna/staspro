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
    display:inline-flex; align-items:center; gap:8px;
    border-radius:8px; font-weight:600; padding:8px 16px;
    background:var(--brand);
    border:0; color:#fff; text-decoration:none; white-space:nowrap;
    box-shadow:0 4px 6px -1px rgba(22,163,74,.2);
    transition: all 0.2s;
  }
  .btn-brand:hover { background:var(--brand-700); transform: translateY(-1px); box-shadow:0 6px 8px -1px rgba(22,163,74,.3); }

  .btn-soft{
    display:inline-flex; align-items:center; gap:8px;
    border-radius:8px; font-weight:600; padding:8px 16px;
    background:#fff; color:var(--ink-600); text-decoration:none; white-space:nowrap;
    border:1px solid #cbd5e1;
    box-shadow:0 1px 2px rgba(0,0,0,0.05);
    transition: all 0.2s;
  }
  .btn-soft:hover { background:#f8fafc; border-color:#94a3b8; color:var(--ink); transform: translateY(-1px); }

  .card-form{
    background:var(--card);
    border:1px solid #e2e8f0;
    border-radius:16px;
    padding:24px;
    box-shadow:0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);
  }
  .label{
    font-weight:600; font-size:.85rem;
    color:var(--ink-600);
    margin-bottom:8px; display: block;
  }
  .field{
    border-radius:8px;
    border:1px solid #cbd5e1;
    padding:8px 12px; font-weight:400; background:#fff;
    width: 100%; transition: all 0.2s;
  }
  .field:focus{
    outline: none; border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
  }
  textarea.field{ height:auto; border-radius:8px; padding:12px; min-height: 100px; }
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

        <div class="col-md-3">
          <div class="label">Kuantitas *</div>
          <input type="number" name="kuantitas" id="kuantitas" class="form-control field" value="{{ old('kuantitas', 1) }}" min="1" required>
        </div>

        <div class="col-md-3">
          <div class="label">Harga Satuan (Rp) *</div>
          <input type="number" name="harga_satuan" id="harga_satuan" class="form-control field" value="{{ old('harga_satuan') }}" min="0" required>
        </div>

        <div class="col-md-2">
          <div class="label">Subtotal</div>
          <div class="field d-flex align-items-center" style="background:#f0fdf4; border-color:#bbf7d0; color:#166534; font-weight:800; height:42px;" id="subtotalDisplay">
            Rp 0
          </div>
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

  // ✅ Live subtotal calculation
  (function(){
    const qty = document.getElementById('kuantitas');
    const harga = document.getElementById('harga_satuan');
    const display = document.getElementById('subtotalDisplay');

    function calc(){
      const q = parseInt(qty?.value) || 0;
      const h = parseFloat(harga?.value) || 0;
      const total = q * h;
      display.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    qty?.addEventListener('input', calc);
    harga?.addEventListener('input', calc);
    calc();
  })();
</script>
@endpush
