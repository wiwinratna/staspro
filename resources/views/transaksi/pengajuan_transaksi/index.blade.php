{{-- resources/views/transaksi/pengajuan_transaksi/index.blade.php --}}
@extends('layouts.panel')

@section('title', 'Pengajuan Transaksi')

@push('styles')
<style>
  /* ❗ CSS khusus halaman saja */
  .hero{
    border-radius:22px;
    padding:18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow: var(--shadow);
    position:relative;
    overflow:hidden;
    margin-bottom:14px;
  }
  .hero::after{
    content:"";
    position:absolute; inset:-1px;
    background:
      radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
      radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
    pointer-events:none;
    opacity:.65;
  }
  .hero-inner{ position:relative; z-index:2; }
  .title{ font-size:1.65rem; font-weight:800; margin:0; letter-spacing:-.2px; }
  .sub{ margin:6px 0 0; color:var(--ink-600); font-weight:500; }

  .tools-row{
    margin-top:14px;
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; flex-wrap:wrap;
  }
  .tools-right{ margin-left:auto; display:flex; gap:10px; flex-wrap:wrap; }

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

  .tabs-wrap{ margin-top:12px; display:flex; justify-content:center; }
  .tabs{
    display:inline-flex; gap:8px;
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:999px;
    padding:6px;
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    flex-wrap:wrap;
    justify-content:center;
  }
  .tab-pill{
    height:32px; display:inline-flex; align-items:center;
    padding:0 12px; border-radius:999px;
    font-weight:900; font-size:.78rem; text-transform:uppercase; letter-spacing:.06em;
    color:var(--ink-600); text-decoration:none;
  }
  .tab-pill.active{
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    color:#fff;
    box-shadow:0 16px 28px rgba(22,163,74,.18);
  }

  .table-wrap{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    overflow:hidden;
    margin-top:14px;
    box-shadow:var(--shadow);
  }
  .table-responsive{ max-height:68vh; overflow-y:auto; overflow-x:auto; }
  .table-modern{ margin:0; font-size:.92rem; table-layout:auto; width:100%; border-collapse:separate; border-spacing:0; min-width:1180px; }
  .table-modern thead th{
    background:#f8fafc; color:var(--ink-600);
    font-weight:900; text-transform:uppercase; font-size:.72rem; letter-spacing:.08em;
    padding:14px 12px; border-bottom:1px solid rgba(226,232,240,.95);
    position:sticky; top:0; z-index:5; white-space:nowrap;
  }
  .table-modern tbody td{ padding:14px 12px; vertical-align:middle; border-top:1px solid #eef2f7; overflow:visible; }
  .clamp-2{
    display:-webkit-box; -webkit-box-orient:vertical; -webkit-line-clamp:2;
    overflow:hidden; word-break:break-word;
  }
  .tnum{ font-variant-numeric: tabular-nums; }

  .badge-pill{
    display:inline-flex; align-items:center; gap:6px;
    border-radius:999px; padding:6px 10px;
    font-weight:900; font-size:.76rem;
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    white-space:nowrap;
    max-width:none;
  }
  .badge-submit{ background:#eff6ff; color:#1d4ed8; border-color:rgba(59,130,246,.25); }
  .badge-approve{ background:#ecfeff; color:#155e75; border-color:rgba(6,182,212,.25); }
  .badge-bukti{ background:#fff7ed; color:#9a3412; border-color:rgba(245,158,11,.25); }
  .badge-done{ background:#f0fdf4; color:#166534; border-color:rgba(22,163,74,.25); }
  .badge-reject{ background:#fef2f2; color:#991b1b; border-color:rgba(239,68,68,.25); }

  .btn-act{
    width:34px; height:34px; border-radius:10px;
    display:inline-flex; align-items:center; justify-content:center;
    border:1px solid rgba(226,232,240,.95);
    background:#fff; text-decoration:none;
  }
  .btn-act.detail{ border-color:rgba(22,163,74,.25); color:var(--brand-700); background:#f0fdf4; }
  .btn-act.ok{ border-color:rgba(6,182,212,.25); color:#155e75; background:#ecfeff; }
  .btn-act.no{ border-color:rgba(239,68,68,.25); color:#991b1b; background:#fef2f2; }

  .proof-trigger{
    cursor:pointer !important;
    position:relative;
    z-index:2;
    color:#0f172a !important;
    border-color:#dbe3ef !important;
    background:#fff !important;
  }
  .proof-trigger:hover{
    background:#ecfdf5 !important;
    color:#166534 !important;
  }
</style>
@endpush

@section('content')

  {{-- HERO --}}
  <section class="hero">
    <div class="hero-inner">
      <h1 class="title">Pengajuan Transaksi</h1>
      <p class="sub">
        Kelola pengajuan dana & reimbursement. Transaksi baru masuk ke Pencatatan Keuangan saat status <b>Selesai</b>.
      </p>

      <div class="tools-row">
        <div class="tools-right">
          <a href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
             target="_blank" rel="noopener" class="btn-soft">
            <i class="bi bi-journal-bookmark"></i> Manual Book
          </a>
        </div>
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
  @endif

  {{-- TABS STATUS --}}
  @php $active = request('status', 'semua'); @endphp
  <div class="card card-soft mb-4 border-0 shadow-sm" style="border-radius:18px;">
    <div class="card-body p-4">
      <div class="fw-bold fs-6 mb-3 text-dark"><i class="bi bi-funnel-fill me-2 text-primary"></i>Filter Status Pengajuan</div>
      <div class="d-flex flex-wrap gap-2" id="statusTabs">
        <a href="{{ route('pengajuan_transaksi.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold px-3 {{ $active==='semua' ? 'active' : '' }}">Semua</a>
        <a href="{{ route('pengajuan_transaksi.index', ['status'=>'submit']) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3 {{ $active==='submit' ? 'active' : '' }}">Diajukan</a>
        <a href="{{ route('pengajuan_transaksi.index', ['status'=>'approve']) }}" class="btn btn-outline-info btn-sm rounded-pill fw-bold px-3 {{ $active==='approve' ? 'active' : '' }}">Disetujui</a>
        <a href="{{ route('pengajuan_transaksi.index', ['status'=>'bukti']) }}" class="btn btn-outline-warning btn-sm rounded-pill fw-bold px-3 {{ $active==='bukti' ? 'active' : '' }}">Bukti Diupload</a>
        <a href="{{ route('pengajuan_transaksi.index', ['status'=>'done']) }}" class="btn btn-outline-success btn-sm rounded-pill fw-bold px-3 {{ $active==='done' ? 'active' : '' }}">Selesai</a>
        <a href="{{ route('pengajuan_transaksi.index', ['status'=>'reject']) }}" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3 {{ $active==='reject' ? 'active' : '' }}">Ditolak</a>
      </div>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table table-modern table-striped align-middle">
        <thead>
          <tr>
            <th style="min-width:160px">No Request</th>
            <th style="min-width:130px">Tanggal</th>
            <th style="min-width:180px">Tim</th>
            <th style="min-width:200px">Sub Kategori</th>
            <th style="min-width:140px">Status</th>
            <th class="text-end" style="min-width:150px">Nominal</th>
            <th class="text-center" style="min-width:180px">Aksi</th>
          </tr>
        </thead>

        <tbody>
          @forelse($items as $index => $trx)
           @php
            $tim = $trx->project->nama_project ?? 'Tidak Ada';
            $sub = $trx->subKategoriSumberDana->nama ?? '-';

            $status = strtolower($trx->status ?? 'submit');
            $tipe   = strtolower($trx->tipe ?? 'pengajuan');

            $badgeClass = $status==='submit' ? 'badge-submit'
                        : ($status==='approve' ? 'badge-approve'
                        : ($status==='bukti' ? 'badge-bukti'
                        : ($status==='done' ? 'badge-done'
                        : 'badge-reject')));

            // ✅ LABEL STATUS BIAR JELAS
            $statusLabel = match($status){
                'submit'  => 'DIAJUKAN',
                'approve' => 'DISETUJUI',
                'bukti'   => 'BUKTI DIUPLOAD',
                'done'    => 'SELESAI',
                'reject'  => 'DITOLAK',
                default   => strtoupper($status),
            };

            $tipeLabel   = ($tipe==='reimbursement') ? 'REIMBURSEMENT' : 'PENGAJUAN DANA';
            $hasBukti = !empty($trx->bukti_file);

            $role = strtolower(auth()->user()->role ?? auth()->user()->level ?? '');
            $isApprover = in_array($role, ['admin','bendahara','superadmin']);
            @endphp


            <tr>
              <td>
                <div class="fw-bold tnum mb-1" style="font-size:0.95rem;">{{ $trx->no_request }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--ink-600);"><i class="bi {{ $tipe==='reimbursement' ? 'bi-receipt' : 'bi-cash-coin' }} me-1"></i>{{ $tipeLabel }}</div>
              </td>

              <td>
                <div class="fw-bold">
                  {{ $trx->tgl_request ? \Carbon\Carbon::parse($trx->tgl_request)->format('d-m-Y') : '-' }}
                </div>
                <div style="font-size:.82rem;color:var(--ink-600);font-weight:700;">
                  {{ $trx->created_at->timezone('Asia/Jakarta')->format('H:i') }}
                </div>
              </td>

              <td title="{{ $tim }}" class="fw-semibold">
                {{ \Illuminate\Support\Str::limit($tim, 24) }}
              </td>

              <td title="{{ $sub }}">
                <div class="clamp-2 fw-semibold">{{ $sub }}</div>
              </td>

              <td>
                <span class="badge-pill {{ $badgeClass }}">
                  <i class="bi {{ $status==='done' ? 'bi-check-circle' : ($status==='reject' ? 'bi-x-circle' : 'bi-clock-history') }}"></i>
                  {{ $statusLabel }}
                </span>
              </td>

              <td class="text-end tnum fw-bold" style="white-space: nowrap;">
                @if(!is_null($trx->nominal_disetujui))
                   <div style="font-size:0.7rem; color:var(--ink-600); font-weight:600; line-height:1; margin-bottom: 2px;">Disetujui:</div>
                   <div style="font-size:0.95rem; color:#166534;">Rp {{ number_format($trx->nominal_disetujui, 0, ',', '.') }}</div>
                @else
                   <div style="font-size:0.7rem; color:var(--ink-600); font-weight:600; line-height:1; margin-bottom: 2px;">Estimasi:</div>
                   <div style="font-size:0.95rem; color:var(--ink);">Rp {{ number_format($trx->estimasi_nominal ?? 0, 0, ',', '.') }}</div>
                @endif
              </td>

              <td class="text-center">
                <div class="d-flex justify-content-center gap-1 flex-wrap">
                  <a href="{{ route('pengajuan_transaksi.show', $trx->id) }}" class="btn btn-outline-success btn-sm shadow-sm rounded-pill fw-bold py-1 px-3" style="font-size:0.75rem; border-color: rgba(22,163,74, 0.4);">
                    Detail
                  </a>

                  @if($hasBukti)
                    <a href="#"
                       class="btn btn-outline-primary btn-sm shadow-sm rounded-pill fw-bold border-1 py-1 px-2 d-inline-flex align-items-center"
                       style="font-size:0.75rem;" title="Lihat Bukti"
                       data-bs-toggle="modal" data-bs-target="#modalBuktiTrx{{ $trx->id }}">
                      <i class="bi bi-image"></i>
                    </a>

                    <div class="modal fade" id="modalBuktiTrx{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header" style="background:linear-gradient(135deg, rgba(139,92,246,.06), rgba(139,92,246,.02));">
                            <h5 class="modal-title"><i class="bi bi-image me-2" style="color:#7c3aed;"></i>Bukti Transaksi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body text-center" style="padding:24px;">
                            <img src="{{ asset('storage/' . $trx->bukti_file) }}" alt="Bukti" class="img-fluid rounded shadow-sm mb-3" style="border-radius:14px !important; max-height:400px; object-fit:contain;">
                            <div class="mt-2">
                              <a href="{{ asset('storage/' . $trx->bukti_file) }}" class="btn btn-sm btn-success rounded-pill fw-bold px-4" download>
                                <i class="bi bi-download me-1"></i> Unduh
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @else
                    <span class="btn btn-outline-secondary btn-sm rounded-pill fw-bold border-1 py-1 px-2 d-inline-flex align-items-center disabled"
                          style="font-size:0.75rem; opacity:0.5; background-color:#f1f5f9; cursor:not-allowed;" title="Belum ada bukti">
                      <i class="bi bi-image"></i>
                    </span>
                  @endif

                    {{-- Aksi Approver --}}
                    @if($isApprover)

                    @if($status === 'done')
                        <!-- No Action -->

                    @elseif($status==='submit')
                        <button type="button" class="btn btn-success btn-sm rounded-circle shadow-sm" title="Approve" onclick="openApprove({{ $trx->id }})" style="width: 26px; height: 26px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                          <i class="bi bi-check-lg" style="font-size: 1rem;"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm rounded-circle shadow-sm" title="Reject" onclick="openReject({{ $trx->id }})" style="width: 26px; height: 26px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                          <i class="bi bi-x-lg" style="font-size: 0.8rem;"></i>
                        </button>

                    @elseif($status==='approve')
                        @if($tipe==='reimbursement')
                        <button type="button" class="btn btn-primary btn-sm rounded-circle shadow-sm" title="Finalize" onclick="openFinalize({{ $trx->id }})" style="width: 26px; height: 26px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-flag-fill" style="font-size: 0.8rem;"></i>
                        </button>
                        @endif

                    @elseif($status==='bukti')
                        <button type="button" class="btn btn-primary btn-sm rounded-circle shadow-sm" title="Finalize" onclick="openFinalize({{ $trx->id }})" style="width: 26px; height: 26px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                          <i class="bi bi-flag-fill" style="font-size: 0.8rem;"></i>
                        </button>
                    @endif

                    @endif

                </div>

                {{-- Hidden Forms --}}
                <form id="formApprove{{ $trx->id }}" action="{{ route('pengajuan_transaksi.approve', $trx->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
                  @csrf
                  <input type="hidden" name="tgl_cair" id="tglCair{{ $trx->id }}">
                  <input type="hidden" name="nominal_final" id="nominalFinal{{ $trx->id }}">
                  <input type="hidden" name="biaya_admin" id="biayaAdmin{{ $trx->id }}">
                  <input type="hidden" name="metode_pembayaran" id="metodeBayar{{ $trx->id }}">
                  <input type="hidden" name="is_talangan" id="isTalangan{{ $trx->id }}" value="0">
                </form>

                <form id="formReject{{ $trx->id }}" action="{{ route('pengajuan_transaksi.reject', $trx->id) }}" method="POST" class="d-none">
                  @csrf
                  <input type="hidden" name="keterangan_reject" id="ketReject{{ $trx->id }}">
                </form>

                <form id="formFinalize{{ $trx->id }}" action="{{ route('pengajuan_transaksi.finalize', $trx->id) }}" method="POST" class="d-none">
                  @csrf
                </form>

              </td>
            </tr>

          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-5" style="font-weight:600;">Belum ada pengajuan transaksi.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function openApprove(id){
    Swal.fire({
      title: "Setujui Pengajuan?",
      html: `
        <div class="text-start">
          <label class="fw-bold mb-1">Tanggal Cair</label>
          <input type="date" id="sw_tgl" class="form-control mb-2">

          <label class="fw-bold mb-1">Nominal Final (yang diberikan)</label>
          <input type="number" id="sw_nom" class="form-control mb-2" placeholder="contoh: 150000">

          <label class="fw-bold mb-1">Biaya Admin Transfer</label>
          <input type="number" id="sw_admin" class="form-control mb-2" placeholder="0" value="0">

          <label class="fw-bold mb-1">Metode Pembayaran</label>
          <select id="sw_met" class="form-select mb-2">
            <option value="transfer">Transfer</option>
            <option value="cash">Cash</option>
          </select>

          <label class="fw-bold mb-1">Bukti Transfer <small class="text-muted">(Opsional)</small></label>
          <input type="file" id="sw_bukti" name="bukti_transfer" class="form-control mb-2" accept=".jpg,.jpeg,.png,.pdf">

          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" id="sw_talangan">
            <label class="form-check-label fw-bold" for="sw_talangan">Tandai sebagai Talangan</label>
          </div>
        </div>
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Setujui",
      cancelButtonText: "Batal",
      confirmButtonColor: "#16a34a",
    }).then((res)=>{
      if(!res.isConfirmed) return;
      const tgl = document.getElementById('sw_tgl').value;
      const nom = document.getElementById('sw_nom').value;
      const adm = document.getElementById('sw_admin').value;
      const met = document.getElementById('sw_met').value;
      const talangan = document.getElementById('sw_talangan').checked;
      const fileInput = document.getElementById('sw_bukti');

      document.getElementById('tglCair'+id).value = tgl;
      document.getElementById('nominalFinal'+id).value = nom;
      document.getElementById('biayaAdmin'+id).value = adm || '0';
      document.getElementById('metodeBayar'+id).value = met;
      document.getElementById('isTalangan'+id).value = talangan ? '1' : '0';

      if(fileInput && fileInput.files.length > 0) {
        fileInput.style.display = 'none';
        document.getElementById('formApprove'+id).appendChild(fileInput);
      }

      document.getElementById('formApprove'+id).submit();
    });
  }

  function openReject(id){
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
      document.getElementById('ketReject'+id).value = res.value || '';
      document.getElementById('formReject'+id).submit();
    });
  }

  function openFinalize(id){
    Swal.fire({
      title: "Selesaikan & Masukkan ke Pencatatan Keuangan?",
      text: "Setelah diselesaikan, data akan dianggap selesai dan masuk ke buku besar.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Selesaikan",
      cancelButtonText: "Batal",
      confirmButtonColor: "#16a34a",
    }).then((res)=>{
      if(res.isConfirmed){
        document.getElementById('formFinalize'+id).submit();
      }
    });
  }
</script>
@endpush
