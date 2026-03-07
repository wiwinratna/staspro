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
  .table-responsive{ max-height:68vh; overflow-y:auto; }
  .table-modern{ margin:0; font-size:.92rem; table-layout:fixed; width:100%; border-collapse:separate; border-spacing:0; }
  .table-modern thead th{
    background:#f8fafc; color:var(--ink-600);
    font-weight:900; text-transform:uppercase; font-size:.72rem; letter-spacing:.08em;
    padding:14px 12px; border-bottom:1px solid rgba(226,232,240,.95);
    position:sticky; top:0; z-index:5; white-space:nowrap;
  }
  .table-modern tbody td{ padding:14px 12px; vertical-align:middle; border-top:1px solid #eef2f7; overflow:hidden; }
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
</style>
@endpush

@section('content')

  {{-- HERO --}}
  <section class="hero">
    <div class="hero-inner">
      <h1 class="title">Pengajuan Transaksi</h1>
      <p class="sub">
        Kelola pengajuan dana & reimbursement. Transaksi baru masuk ke Pencatatan Keuangan saat status <b>DONE</b>.
      </p>

      <div class="tools-row">
        <a href="{{ route('pengajuan_transaksi.create_pengajuan') }}" class="btn-brand">
          <i class="bi bi-plus-lg"></i> Pengajuan Dana
        </a>

        <div class="tools-right">
          <a href="{{ route('pengajuan_transaksi.create_reimbursement') }}" class="btn-soft">
            <i class="bi bi-receipt"></i> Reimbursement
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
  <div class="tabs-wrap">
    <div class="tabs" id="statusTabs">
      <a href="{{ route('pengajuan_transaksi.index') }}" class="tab-pill {{ $active==='semua' ? 'active' : '' }}" data-filter="semua">Semua</a>
      <a href="{{ route('pengajuan_transaksi.index', ['status'=>'submit']) }}" class="tab-pill {{ $active==='submit' ? 'active' : '' }}" data-filter="submit">Submit</a>
      <a href="{{ route('pengajuan_transaksi.index', ['status'=>'approve']) }}" class="tab-pill {{ $active==='approve' ? 'active' : '' }}" data-filter="approve">Approve</a>
      <a href="{{ route('pengajuan_transaksi.index', ['status'=>'bukti']) }}" class="tab-pill {{ $active==='bukti' ? 'active' : '' }}" data-filter="bukti">Bukti Diupload</a>
      <a href="{{ route('pengajuan_transaksi.index', ['status'=>'done']) }}" class="tab-pill {{ $active==='done' ? 'active' : '' }}" data-filter="done">Finalized</a>
      <a href="{{ route('pengajuan_transaksi.index', ['status'=>'reject']) }}" class="tab-pill {{ $active==='reject' ? 'active' : '' }}" data-filter="reject">Reject</a>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table table-modern table-striped align-middle">
        <thead>
          <tr>
            <th style="width:60px">No</th>
            <th style="width:170px">No Request</th>
            <th style="width:150px">Tanggal</th>
            <th style="width:190px">Tim</th>
            <th style="width:230px">Sub Kategori</th>
            <th style="width:150px">Tipe</th>
            <th style="width:140px">Status</th>
            <th class="text-end" style="width:160px">Estimasi</th>
            <th class="text-end" style="width:170px">Disetujui</th>
            <th style="width:110px">Bukti</th>
            <th style="width:150px">Aksi</th>
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
                'submit'  => 'SUBMIT',
                'approve' => 'APPROVE',
                'bukti'   => 'BUKTI DIUPLOAD',
                'done'    => 'FINALIZED',
                'reject'  => 'REJECT',
                default   => strtoupper($status),
            };

            $tipeLabel   = ($tipe==='reimbursement') ? 'REIMBURSEMENT' : 'PENGAJUAN DANA';
            $hasBukti = !empty($trx->bukti_file);

            $role = strtolower(auth()->user()->role ?? auth()->user()->level ?? '');
            $isApprover = in_array($role, ['admin','bendahara','superadmin']);
            @endphp


            <tr>
              <td>{{ $index + 1 }}</td>

              <td class="fw-bold tnum">{{ $trx->no_request }}</td>

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
                <span class="badge-pill">
                  <i class="bi {{ $tipe==='reimbursement' ? 'bi-receipt' : 'bi-cash-coin' }}"></i>
                  {{ $tipeLabel }}
                </span>
              </td>

              <td>
                <span class="badge-pill {{ $badgeClass }}">
                  <i class="bi {{ $status==='done' ? 'bi-check-circle' : ($status==='reject' ? 'bi-x-circle' : 'bi-clock-history') }}"></i>
                  {{ $statusLabel }}
                </span>
              </td>

              <td class="text-end tnum fw-bold">
                Rp {{ number_format($trx->estimasi_nominal ?? 0, 0, ',', '.') }}
              </td>

              <td class="text-end tnum fw-bold">
                {{ is_null($trx->nominal_disetujui) ? '-' : 'Rp '.number_format($trx->nominal_disetujui, 0, ',', '.') }}
              </td>

              <td>
                @if($hasBukti)
                  <a href="#" class="btn-soft" style="height:32px;padding:0 10px;border-radius:999px;font-weight:900;"
                     data-bs-toggle="modal" data-bs-target="#modalBukti{{ $trx->id }}">
                    <i class="bi bi-image"></i> Lihat
                  </a>

                  <div class="modal fade" id="modalBukti{{ $trx->id }}" tabindex="-1" aria-hidden="true">
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
                @else
                  <span class="text-muted" style="font-weight:800;">-</span>
                @endif
              </td>

              <td>
                <div class="d-flex gap-1 flex-wrap">
                  <a href="{{ route('pengajuan_transaksi.show', $trx->id) }}" class="btn-act detail" title="Detail">
                    <i class="bi bi-eye"></i>
                  </a>

                    {{-- Aksi Approver --}}
                    @if($isApprover)

                    {{-- ✅ DONE = tidak ada aksi lanjutan --}}
                    @if($status === 'done')
                        <span class="text-muted" style="font-weight:800;">-</span>

                    @elseif($status==='submit')
                        <button type="button" class="btn-act ok" title="Approve" onclick="openApprove({{ $trx->id }})">
                        <i class="bi bi-check2"></i>
                        </button>

                        <button type="button" class="btn-act no" title="Reject" onclick="openReject({{ $trx->id }})">
                        <i class="bi bi-x-lg"></i>
                        </button>

                    @elseif($status==='approve')
                        {{-- pengajuan: tunggu bukti, reimbursement: bisa finalize --}}
                        @if($tipe==='reimbursement')
                        <button type="button" class="btn-act ok" title="Finalize" onclick="openFinalize({{ $trx->id }})">
                            <i class="bi bi-flag"></i>
                        </button>
                        @endif

                    @elseif($status==='bukti')
                        <button type="button" class="btn-act ok" title="Finalize" onclick="openFinalize({{ $trx->id }})">
                        <i class="bi bi-flag"></i>
                        </button>
                    @endif

                    @endif

                </div>

                {{-- Hidden Forms --}}
                <form id="formApprove{{ $trx->id }}" action="{{ route('pengajuan_transaksi.approve', $trx->id) }}" method="POST" class="d-none">
                  @csrf
                  <input type="hidden" name="tgl_cair" id="tglCair{{ $trx->id }}">
                  <input type="hidden" name="nominal_disetujui" id="nominalSetuju{{ $trx->id }}">
                  <input type="hidden" name="metode_pembayaran" id="metodeBayar{{ $trx->id }}">
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
              <td colspan="11" class="text-center text-muted py-4" style="font-weight:800;">Belum ada pengajuan transaksi.</td>
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
      title: "Approve Pengajuan?",
      html: `
        <div class="text-start">
          <label class="fw-bold mb-1">Tanggal Cair</label>
          <input type="date" id="sw_tgl" class="form-control mb-2">

          <label class="fw-bold mb-1">Nominal Disetujui</label>
          <input type="number" id="sw_nom" class="form-control mb-2" placeholder="contoh: 150000">

          <label class="fw-bold mb-1">Metode Pembayaran</label>
          <select id="sw_met" class="form-select">
            <option value="transfer">Transfer</option>
            <option value="cash">Cash</option>
          </select>
        </div>
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Approve",
      cancelButtonText: "Batal",
      confirmButtonColor: "#16a34a",
    }).then((res)=>{
      if(!res.isConfirmed) return;
      const tgl = document.getElementById('sw_tgl').value;
      const nom = document.getElementById('sw_nom').value;
      const met = document.getElementById('sw_met').value;

      document.getElementById('tglCair'+id).value = tgl;
      document.getElementById('nominalSetuju'+id).value = nom;
      document.getElementById('metodeBayar'+id).value = met;

      document.getElementById('formApprove'+id).submit();
    });
  }

  function openReject(id){
    Swal.fire({
      title: "Reject Pengajuan?",
      input: "textarea",
      inputLabel: "Keterangan Reject",
      inputPlaceholder: "Tulis alasan penolakan...",
      showCancelButton: true,
      confirmButtonText: "Ya, Reject",
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
      title: "Finalize & Masukkan ke Pencatatan Keuangan?",
      text: "Setelah finalize, data akan dianggap DONE dan masuk ledger.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Finalize",
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
