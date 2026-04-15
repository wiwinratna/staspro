{{-- resources/views/transaksi/pengajuan_transaksi/detail_pengajuan.blade.php --}}
@extends('layouts.panel')

@section('title', 'Detail Pengajuan Dana')

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

  .card-box{
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
  .val{ font-weight:800; }
  .tnum{ font-variant-numeric: tabular-nums; }

  .badge-pill{
    display:inline-flex; align-items:center; gap:6px;
    border-radius:999px; padding:6px 10px;
    font-weight:900; font-size:.76rem;
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    white-space:nowrap;
  }
  .badge-submit{ background:#eff6ff; color:#1d4ed8; border-color:rgba(59,130,246,.25); }
  .badge-approve{ background:#ecfeff; color:#155e75; border-color:rgba(6,182,212,.25); }
  .badge-bukti{ background:#fff7ed; color:#9a3412; border-color:rgba(245,158,11,.25); }
  .badge-done{ background:#f0fdf4; color:#166534; border-color:rgba(22,163,74,.25); }
  .badge-reject{ background:#fef2f2; color:#991b1b; border-color:rgba(239,68,68,.25); }

  .field{
    height:38px; border-radius:999px;
    border:1px solid rgba(226,232,240,.95);
    padding:0 12px; font-weight:700; background:#fff;
  }
  textarea.field{ height:auto; border-radius:16px; padding:12px; }
</style>
@endpush

@section('content')

  @php
    $status = strtolower($trx->status ?? 'submit');
    $badgeClass = $status==='submit' ? 'badge-submit'
                : ($status==='approve' ? 'badge-approve'
                : ($status==='bukti' ? 'badge-bukti'
                : ($status==='done' ? 'badge-done'
                : 'badge-reject')));

    $tim = $trx->project->nama_project ?? 'Tidak Ada';
    $sub = $trx->subKategoriSumberDana->nama ?? '-';

    $role = strtolower(auth()->user()->role ?? auth()->user()->level ?? '');
    $isApprover = in_array($role, ['admin','bendahara','superadmin']);
  @endphp

  <section class="hero">
    <div class="hero-inner">
      <h1 class="title">Detail Pengajuan Dana</h1>
      <p class="sub">
        No Request: <b class="tnum">{{ $trx->no_request }}</b>
        <span class="ms-2 badge-pill {{ $badgeClass }}">
          @php
            $statusLabel = match($status){
                'submit'  => 'DIAJUKAN',
                'approve' => 'DISETUJUI',
                'bukti'   => 'BUKTI DIUPLOAD',
                'done'    => 'SELESAI',
                'reject'  => 'DITOLAK',
                default   => strtoupper($status),
            };
            @endphp

            @php
            $statusIcon = match($status){
                'done' => 'bi-check-circle',
                'reject' => 'bi-x-circle',
                'bukti' => 'bi-file-earmark-check',
                default => 'bi-clock-history',
            };
            @endphp
            <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}


        </span>
      </p>

      <div class="d-flex gap-2 flex-wrap mt-3">
        <a href="{{ route('pengajuan_transaksi.index') }}" class="btn-soft">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>

        @if($trx->status === 'done' && $trx->pencatatan_keuangan_id)
          <a href="{{ route('pencatatan_keuangan') }}" class="btn-brand">
            <i class="bi bi-journal-check"></i> Lihat di Pencatatan Keuangan
          </a>
        @endif
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card-box">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="label">Project (Tim)</div>
            <div class="val">{{ $tim }}</div>
          </div>
          <div class="col-md-6">
            <div class="label">Sub Kategori</div>
            <div class="val">{{ $sub }}</div>
          </div>
          <div class="col-md-6">
            <div class="label">Tanggal Request</div>
            <div class="val">{{ $trx->tgl_request ? \Carbon\Carbon::parse($trx->tgl_request)->format('d-m-Y') : '-' }}</div>
          </div>
          <div class="col-md-6">
            <div class="label">Estimasi</div>
            <div class="val tnum">Rp {{ number_format($trx->estimasi_nominal ?? 0, 0, ',', '.') }}</div>
          </div>
          <div class="col-12">
            <div class="label">Deskripsi</div>
            <div class="val">{{ $trx->deskripsi ?? '-' }}</div>
          </div>

          <div class="col-md-6">
            <div class="label">Nama Bank</div>
            <div class="val">{{ $trx->nama_bank ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <div class="label">No Rekening</div>
            <div class="val tnum">{{ $trx->no_rekening ?? '-' }}</div>
          </div>
        </div>
      </div>

      <div class="card-box mt-3">
        <div class="label">Approval & Pencairan</div>
        <div class="row g-3">
          <div class="col-md-4">
            <div class="label">Tanggal Cair</div>
            <div class="val">{{ $trx->tgl_cair ? \Carbon\Carbon::parse($trx->tgl_cair)->format('d-m-Y') : '-' }}</div>
          </div>
          <div class="col-md-4">
            <div class="label">Nominal Disetujui</div>
            <div class="val tnum">{{ is_null($trx->nominal_disetujui) ? '-' : 'Rp '.number_format($trx->nominal_disetujui,0,',','.') }}</div>
          </div>
          <div class="col-md-4">
            <div class="label">Metode</div>
            <div class="val">{{ strtoupper($trx->metode_pembayaran ?? '-') }}</div>
          </div>

          @if($status==='reject')
            <div class="col-12">
              <div class="label">Keterangan Penolakan</div>
              <div class="val text-danger">{{ $trx->keterangan_reject ?? '-' }}</div>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      {{-- Bukti --}}
      <div class="card-box">
        <div class="d-flex justify-content-between align-items-center">
          <div class="label mb-0">Bukti Transaksi</div>

          @if(!empty($trx->bukti_file))
            <a href="#" class="btn-soft" style="height:32px;padding:0 10px;border-radius:999px;font-weight:900;"
               data-bs-toggle="modal" data-bs-target="#modalBukti">
              <i class="bi bi-image"></i> Lihat
            </a>
          @endif
        </div>

        <div class="mt-2">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="label">Tanggal Bukti</div>
              <div class="val">{{ $trx->tgl_bukti ? \Carbon\Carbon::parse($trx->tgl_bukti)->format('d-m-Y') : '-' }}</div>
            </div>
            <div class="col-md-6">
              <div class="label">Nominal Realisasi</div>
              <div class="val tnum">{{ is_null($trx->nominal_realisasi) ? '-' : 'Rp '.number_format($trx->nominal_realisasi,0,',','.') }}</div>
            </div>
            <div class="col-12">
              <div class="label">File</div>
              <div class="val">{{ !empty($trx->bukti_file) ? 'Tersedia' : '-' }}</div>
            </div>
          </div>
        </div>

        {{-- Modal Bukti --}}
        @if(!empty($trx->bukti_file))
          <div class="modal fade" id="modalBukti" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Bukti Transaksi</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                  <img src="{{ asset('storage/' . $trx->bukti_file) }}" alt="Bukti" class="img-fluid rounded shadow-sm mb-3">
                  <a href="{{ asset('storage/' . $trx->bukti_file) }}" class="btn-brand" download>
                    <i class="bi bi-download"></i> Unduh
                  </a>
                </div>
              </div>
            </div>
          </div>
        @endif
      </div>


        {{-- ACTIONS --}}

        {{-- ✅ DONE: tidak ada aksi --}}
        @if($status === 'done')
        <div class="card-box mt-3">
            <div class="label">Status</div>
            <div class="alert alert-success mb-0" style="border-radius:16px;font-weight:800;">
            Status sudah <b>Selesai</b>. Tidak ada aksi lanjutan.
            </div>
        </div>

        {{-- ✅ SUBMIT: hanya Approver yang bisa approve/reject --}}
        @elseif($status === 'submit')
        @if($isApprover)
            <div class="card-box mt-3">
            <div class="label">Aksi Approver</div>

            <form method="POST" action="{{ route('pengajuan_transaksi.approve', $trx->id) }}" class="mt-2">
                @csrf
                <div class="row g-2">
                <div class="col-md-4">
                    <div class="label">Tgl Cair *</div>
                    <input type="date" name="tgl_cair" class="form-control field" required>
                </div>
                <div class="col-md-4">
                    <div class="label">Nominal *</div>
                    <input type="number" name="nominal_disetujui" class="form-control field" required>
                </div>
                <div class="col-md-4">
                    <div class="label">Metode *</div>
                    <select name="metode_pembayaran" class="form-select field" required>
                    <option value="transfer">Transfer</option>
                    <option value="cash">Cash</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <button type="submit" class="btn-brand"><i class="bi bi-check2"></i> Setujui</button>
                    <button type="button" class="btn-soft" onclick="openReject()"><i class="bi bi-x-lg"></i> Tolak</button>
                </div>
                </div>
            </form>

            <form id="rejectForm" method="POST" action="{{ route('pengajuan_transaksi.reject', $trx->id) }}" class="d-none">
                @csrf
                <input type="hidden" name="keterangan_reject" id="rejectReason">
            </form>
            </div>
        @endif

        {{-- ✅ APPROVE: Upload bukti boleh semua (peneliti juga bisa) --}}
        @elseif($status === 'approve')
        <div class="card-box mt-3">
            <div class="label">Upload Bukti (Tahap Bukti)</div>
            <form method="POST" action="{{ route('pengajuan_transaksi.upload_bukti', $trx->id) }}" enctype="multipart/form-data" class="mt-2">
            @csrf
            <div class="row g-2">
                <div class="col-md-4">
                <div class="label">Tgl Bukti *</div>
                <input type="date" name="tgl_bukti" class="form-control field" required>
                </div>
                <div class="col-md-4">
                <div class="label">Nominal *</div>
                <input type="number" name="nominal_realisasi" class="form-control field" required>
                </div>
                <div class="col-md-4">
                <div class="label">File *</div>
                <input type="file" name="bukti_file" class="form-control field" accept="image/*" required>
                </div>
                <div class="col-12 d-flex justify-content-end mt-2">
                <button type="submit" class="btn-brand"><i class="bi bi-upload"></i> Simpan Bukti</button>
                </div>
            </div>
            </form>
        </div>

        {{-- ✅ BUKTI: Finalize hanya Approver --}}
        @elseif($status === 'bukti')
        @if($isApprover)
            <div class="card-box mt-3">
            <div class="label">Selesaikan</div>
            <p class="mb-2" style="color:var(--ink-600);font-weight:600;">
                Menyelesaikan akan mengubah status menjadi <b>Selesai</b> dan memasukkan transaksi ke Pencatatan Keuangan.
            </p>
            <form method="POST" action="{{ route('pengajuan_transaksi.finalize', $trx->id) }}">
                @csrf
                <button type="submit" class="btn-brand w-100">
                <i class="bi bi-flag"></i> Selesaikan
                </button>
            </form>
            </div>
        @endif
        @endif


    </div>
  </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function openReject(){
    Swal.fire({
      title: "Tolak Pengajuan?",
      input: "textarea",
      inputLabel: "Keterangan Penolakan",
      inputPlaceholder: "Tulis alasan penolakan...",
      showCancelButton: true,
      confirmButtonText: "Ya, Tolak",
      cancelButtonText: "Batal",
      confirmButtonColor: "#dc2626",
    }).then((res)=>{
      if(!res.isConfirmed) return;
      document.getElementById('rejectReason').value = res.value || '';
      document.getElementById('rejectForm').submit();
    });
  }
</script>
@endpush
