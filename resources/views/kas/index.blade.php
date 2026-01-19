@extends('layouts.panel')
@section('title','Kas')

@push('styles')
<style>
  :root{
    --brand:#16a34a;
    --brand-700:#15803d;
    --brand-50:#ecfdf5;

    --ink:#0f172a;
    --ink-600:#475569;
    --line:#e2e8f0;

    --bg:#f6f7fb;
    --card:#ffffff;

    --shadow:0 10px 30px rgba(15,23,42,.08);
    --shadow2:0 18px 40px rgba(15,23,42,.10);

    --danger:#ef4444;
  }

  /* HERO ala dashboard */
  .hero{
    border-radius:22px;
    padding:18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow:var(--shadow);
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
  .hero-inner{ position:relative; z-index:2; width:100%; }

  .hero-left .title{
    font-size:1.65rem;
    font-weight:800;
    margin:0;
    letter-spacing:-.2px;
  }
  .hero-left .sub{
    margin:6px 0 0;
    color:var(--ink-600);
    font-weight:500;
  }

  .tools-row{
    margin-top:14px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
  }
  .tools-left{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
  }
  .tools-right{
    margin-left:auto;
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
  }

  .btn-brand{
    height:38px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    border-radius:999px;
    font-weight:800;
    padding:0 14px;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    border:0;
    box-shadow:0 16px 28px rgba(22,163,74,.18);
    color:#fff;
    white-space:nowrap;
    text-decoration:none;
  }
  .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }
  .btn-brand i{ line-height:1; }

  .btn-soft{
    height:38px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    border-radius:999px;
    font-weight:800;
    padding:0 14px;
    background:#fff;
    color:var(--ink);
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    white-space:nowrap;
    text-decoration:none;
  }
  .btn-soft:hover{
    background:var(--brand-50);
    transform:translateY(-1px);
    color:var(--brand-700);
    border-color:rgba(226,232,240,.95);
  }

  /* Summary cards */
  .stats-grid{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:14px;
    margin-top:12px;
  }
  .stat-card{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    padding:14px;
    box-shadow:var(--shadow);
  }
  .stat-label{
    font-size:.72rem;
    letter-spacing:.08em;
    text-transform:uppercase;
    font-weight:900;
    color:var(--ink-600);
  }
  .stat-value{
    margin-top:6px;
    font-weight:900;
    font-size:1.35rem;
  }
  .tnum{ font-variant-numeric: tabular-nums; }

  /* Table */
  .table-wrap{
    background:var(--card);
    border:1px solid rgba(226,232,240,.95);
    border-radius:22px;
    overflow:hidden;
    margin-top:14px;
    box-shadow:var(--shadow);
  }
  .table-responsive{ max-height:68vh; overflow-y:auto; }

  .table-modern{ margin:0; font-size:.92rem; border-collapse:separate; border-spacing:0; }
  .table-modern thead th{
    background:#f8fafc;
    color:var(--ink-600);
    font-weight:900;
    text-transform:uppercase;
    font-size:.72rem;
    letter-spacing:.08em;
    padding:14px 12px;
    border-bottom:1px solid rgba(226,232,240,.95);
    position:sticky;
    top:0;
    z-index:5;
  }
  .table-modern tbody td{
    padding:14px 12px;
    vertical-align:middle;
    border-top:1px solid #eef2f7;
    font-weight:500;
  }
  .table-striped > tbody > tr:nth-of-type(odd){ background:#fcfcfd; }
  .table-modern tbody tr:hover{ background:var(--brand-50); transition:.12s; }

  /* Pill tipe */
  .pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:.42rem .72rem;
    border-radius:999px;
    font-weight:900;
    font-size:.74rem;
    border:1px solid transparent;
    white-space:nowrap;
    text-transform:uppercase;
    letter-spacing:.02em;
  }
  .pill-in{ background:#ecfdf5; color:#166534; border-color:#bbf7d0; }
  .pill-out{ background:#fef2f2; color:#991b1b; border-color:#fecaca; }

  @media(max-width:991px){
    .stats-grid{ grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-left">
        <h1 class="title">Kas</h1>
        <p class="sub">Ringkasan transaksi kas (masuk/keluar) dan saldo terkini.</p>
      </div>

      <div class="tools-row">
        <div class="tools-left">
          <a href="{{ route('kas.create') }}" class="btn btn-brand">
            <i class="bi bi-plus-lg"></i> Tambah Kas
          </a>
        </div>

        <div class="tools-right">
          <a
            href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
            target="_blank"
            rel="noopener"
            class="btn btn-soft"
            title="Buka Manual Book"
          >
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

  <!-- SUMMARY -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label">Total Masuk</div>
      <div class="stat-value tnum">Rp {{ number_format($totalMasuk ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Keluar</div>
      <div class="stat-value tnum">Rp {{ number_format($totalKeluar ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Saldo</div>
      <div class="stat-value tnum">Rp {{ number_format($saldo ?? 0, 0, ',', '.') }}</div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table table-modern table-striped align-middle">
        <thead>
          <tr>
            <th class="text-center" style="min-width:140px">Tanggal</th>
            <th class="text-center" style="min-width:140px">Tipe</th>
            <th class="text-center" style="min-width:180px">Kategori</th>
            <th class="text-end" style="min-width:160px">Nominal</th>
            <th class="text-start" style="min-width:320px">Deskripsi</th>
          </tr>
        </thead>

        <tbody>
          @forelse($rows as $r)
            <tr>
              <td class="text-center">{{ $r->tanggal }}</td>
              <td class="text-center">
                @php $tipe = strtolower($r->tipe ?? ''); @endphp
                <span class="pill {{ $tipe === 'masuk' ? 'pill-in' : 'pill-out' }}">
                  {{ $r->tipe }}
                </span>
              </td>
              <td class="text-center">{{ $r->kategori }}</td>
              <td class="text-end tnum">Rp {{ number_format($r->nominal ?? 0, 0, ',', '.') }}</td>
              <td class="text-start">{{ $r->deskripsi ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-secondary py-4">Belum ada transaksi kas.</td>
            </tr>
          @endforelse
        </tbody>

      </table>
    </div>
  </div>

@endsection

@push('scripts')
<script>
/*
  HARD LOCK: MINUS & 0 DILARANG (MINIMAL 1)
  - bekerja di semua input type=number (termasuk halaman create)
  - tanpa ubah tampilan
*/
(function(){
  const isNumberInput = (el) => el && el.tagName === 'INPUT' && el.type === 'number';

  function sanitize(el){
    if (!isNumberInput(el)) return;

    // paksa min/step via JS (tanpa ubah HTML)
    el.min = '1';
    el.step = '1';

    // bersihkan value: hanya digit, minimal 1
    let v = String(el.value ?? '');

    // jika user ngetik "-" atau hal aneh, buang semua non-digit
    v = v.replace(/[^\d]/g, '');

    // buang leading zero (00012 -> 12)
    v = v.replace(/^0+/, '');

    // kalau kosong, biarkan kosong (nanti submit diblock)
    if (v === '') {
      el.value = '';
      return;
    }

    // kalau hasilnya 0 atau <1, kosongkan
    if (Number(v) < 1) {
      el.value = '';
      return;
    }

    el.value = v;
  }

  // pas halaman siap, set min & lock wheel + sanitize awal
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="number"]').forEach((el) => {
      el.min = '1';
      el.step = '1';

      // stop scroll changing values
      el.addEventListener('wheel', (e) => e.preventDefault(), { passive:false });

      // sanitize jika ada value dari autofill
      sanitize(el);
    });

    // blok submit kalau ada number input kosong / <1
    document.querySelectorAll('form').forEach((form) => {
      form.addEventListener('submit', (e) => {
        const nums = form.querySelectorAll('input[type="number"]');
        for (const el of nums) {
          sanitize(el);
          if (el.value === '' || Number(el.value) < 1) {
            e.preventDefault();
            e.stopPropagation();
            el.focus();
            return false;
          }
        }
      }, true);
    });
  });

  // kunci input realtime (ini yang bikin "-" & "0" ga bertahan)
  document.addEventListener('input', (e) => {
    if (isNumberInput(e.target)) sanitize(e.target);
  }, true);

  // kunci ketikan: blok minus, plus, e, E, koma, titik, spasi, dan 0 kalau di awal
  document.addEventListener('keydown', (e) => {
    const el = e.target;
    if (!isNumberInput(el)) return;

    // izinkan tombol kontrol
    const allowed = [
      'Backspace','Delete','Tab','Enter','Escape',
      'ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home','End'
    ];
    if (allowed.includes(e.key)) return;
    if (e.ctrlKey || e.metaKey) return;

    // larang karakter non digit (termasuk -, +, e, E, ., ,)
    if (!/^\d$/.test(e.key)) {
      e.preventDefault();
      return;
    }

    // larang "0" sebagai digit pertama (biar gak bisa 0 / 00 / 000)
    if (e.key === '0') {
      const cur = String(el.value ?? '');
      const start = el.selectionStart ?? cur.length;
      const end = el.selectionEnd ?? cur.length;
      const next = cur.slice(0,start) + '0' + cur.slice(end);

      // kalau hasilnya jadi "0" di awal / leading zero, block
      if (/^0/.test(next)) {
        e.preventDefault();
        return;
      }
    }
  }, true);

  // blok paste yang mengandung minus atau hasilnya 0
  document.addEventListener('paste', (e) => {
    const el = e.target;
    if (!isNumberInput(el)) return;

    const text = (e.clipboardData || window.clipboardData).getData('text') || '';
    if (text.includes('-')) {
      e.preventDefault();
      return;
    }

    const digits = text.replace(/[^\d]/g,'').replace(/^0+/, '');
    if (!digits || Number(digits) < 1) {
      e.preventDefault();
      return;
    }
  }, true);
})();
</script>
@endpush
