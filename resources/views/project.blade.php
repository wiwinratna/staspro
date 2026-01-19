{{-- resources/views/project/index.blade.php --}}
@extends('layouts.panel')

@section('title','Project')

@push('styles')
<style>
  /* =========================
     PAGE-ONLY STYLES (Project)
     ========================= */

  /* HERO */
  .hero{
    border-radius:22px;
    padding:18px;
    background:
      radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
      radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
      linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
    border:1px solid rgba(226,232,240,.95);
    box-shadow:0 10px 30px rgba(15,23,42,.08);
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
    font-weight:900;
    margin:0;
    letter-spacing:-.2px;
  }
  .hero-left .sub{
    margin:6px 0 0;
    color:#475569;
    font-weight:600;
  }

  /* Tabs (Project / Pengajuan) */
  .tabs{
    margin-top:12px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
  }
  .tab-btn{
    height:36px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 14px;
    border-radius:999px;
    font-weight:900;
    text-decoration:none;
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    color:#0f172a;
    box-shadow:0 12px 24px rgba(15,23,42,.06);
    transition:.15s;
    white-space:nowrap;
  }
  .tab-btn:hover{ background:#ecfdf5; color:#15803d; transform:translateY(-1px); }
  .tab-btn.active{
    background:linear-gradient(135deg,#15803d,#16a34a);
    color:#fff;
    border-color:transparent;
    box-shadow:0 16px 28px rgba(22,163,74,.18);
  }

  /* Tools */
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

  .search-wrap{
    height:38px;
    display:flex;
    align-items:center;
    gap:10px;
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:999px;
    padding:0 12px;
    box-shadow:0 10px 26px rgba(15,23,42,.05);
    width:420px;
    max-width:100%;
  }
  .search-wrap i{ color:#475569; line-height:1; }
  .search-input{
    height:100%;
    width:100%;
    border:0;
    outline:0;
    font-weight:700;
    background:transparent;
    padding:0;
  }

  .btn-apply{
    height:38px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    border-radius:999px;
    font-weight:900;
    padding:0 14px;
    background:linear-gradient(135deg,#15803d,#16a34a);
    border:0;
    box-shadow:0 16px 28px rgba(22,163,74,.18);
    white-space:nowrap;
    color:#fff;
    text-decoration:none;
  }
  .btn-apply:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }
  .btn-apply i{ line-height:1; }

  .btn-manual{
    height:36px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 14px;
    border-radius:999px;
    font-weight:900;
    background:#fff;
    color:#0f172a;
    border:1px solid rgba(226,232,240,.95);
    text-decoration:none;
    box-shadow:0 12px 24px rgba(15,23,42,.06);
    white-space:nowrap;
    transition:.15s;
  }
  .btn-manual:hover{
    background:#ecfdf5;
    color:#15803d;
    transform:translateY(-1px);
  }

  @media(max-width:991.98px){
    .search-wrap{ width:100%; }
    .tools-right{ width:100%; margin-left:0; justify-content:flex-start; }
  }

  /* Section */
  .section-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin:18px 0 12px;
    flex-wrap:wrap;
  }
  .section-title{
    font-weight:900;
    font-size:1.02rem;
    margin:0;
    display:flex;
    align-items:center;
    gap:8px;
  }
  .section-pill{
    height:34px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 12px;
    border-radius:999px;
    background:#ecfdf5;
    color:#15803d;
    border:1px solid rgba(2,6,23,.06);
    font-weight:800;
    white-space:nowrap;
    font-size:.82rem;
  }

  /* grid rapih */
  .row.g-3 > [class*="col-"]{ display:flex; }

  /* card project */
  .proj-card{
    width:100%;
    background:
      radial-gradient(900px 220px at 18% 0%, rgba(255,255,255,.16), transparent 60%),
      radial-gradient(600px 200px at 85% 12%, rgba(255,255,255,.10), transparent 55%),
      linear-gradient(180deg,#1a8f4a,#157a3b);
    color:#fff;
    border-radius:20px;
    padding:16px;
    height:180px;
    position:relative;
    box-shadow:0 10px 26px rgba(15,23,42,.10);
    transition: transform .15s ease, box-shadow .15s ease;
    cursor:pointer;
    overflow:hidden;
    border:1px solid rgba(255,255,255,.14);
  }
  .proj-card:hover{ transform:translateY(-2px); box-shadow:0 18px 40px rgba(15,23,42,.10); }
  .proj-card.archived{
    background:
      radial-gradient(900px 220px at 18% 0%, rgba(255,255,255,.14), transparent 60%),
      radial-gradient(600px 200px at 85% 12%, rgba(255,255,255,.08), transparent 55%),
      linear-gradient(180deg,#94a3b8,#64748b);
    border:1px solid rgba(255,255,255,.18);
  }

  .proj-year{
    position:absolute; top:14px; left:14px;
    font-size:.72rem;
    padding:.22rem .6rem;
    border-radius:999px;
    background:rgba(255,255,255,.16);
    border:1px solid rgba(255,255,255,.22);
    font-weight:800;
    white-space:nowrap;
  }
  .proj-status{
    position:absolute; top:14px; right:14px;
    font-size:.72rem;
    font-weight:800;
    padding:.22rem .6rem;
    border-radius:999px;
    background:rgba(255,255,255,.16);
    border:1px solid rgba(255,255,255,.22);
    white-space:nowrap;
  }
  .proj-title{
    margin-top:40px;
    font-size:1.05rem;
    font-weight:900;
    line-height:1.35;
    letter-spacing:-.1px;
  }
  .proj-meta{
    margin-top:10px;
    font-size:.82rem;
    opacity:.95;
    display:flex;
    flex-direction:column;
    gap:4px;
    font-weight:600;
  }

  /* actions (admin only) */
  .proj-actions{
    position:absolute;
    right:12px;
    bottom:12px;
    display:flex;
    gap:8px;
    opacity:0;
    transition:.15s;
  }
  .proj-card:hover .proj-actions{ opacity:1; }
  .proj-actions .btn{
    --bs-btn-padding-y:.2rem;
    --bs-btn-padding-x:.5rem;
    --bs-btn-bg:rgba(255,255,255,.18);
    --bs-btn-border-color:rgba(255,255,255,.30);
    --bs-btn-hover-bg:rgba(255,255,255,.26);
    color:#fff;
    border-radius:12px;
    font-weight:800;
  }
  .proj-actions form{ margin:0; }

  /* badge anggota */
  .proj-member{
    position:absolute;
    left:14px;
    bottom:14px;
    font-size:.72rem;
    font-weight:900;
    padding:.22rem .6rem;
    border-radius:999px;
    background:rgba(255,255,255,.22);
    border:1px solid rgba(255,255,255,.30);
    display:inline-flex;
    align-items:center;
    gap:6px;
    white-space:nowrap;
  }
  .proj-card.joined{
    outline:2px solid rgba(255,255,255,.35);
    box-shadow:0 18px 42px rgba(2,6,23,.18);
  }
</style>
@endpush

@section('content')

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-left">
        <h1 class="title">Project</h1>
        <p class="sub">
          @if(in_array(Auth::user()->role, ['admin','bendahara']))
            Daftar project aktif & arsip. Kelola dan pantau pengajuan masuk di sini.
          @else
            Daftar project aktif & arsip + pantau pengajuan kamu di sini.
          @endif
        </p>

        {{-- Tabs: Project / Pengajuan --}}
        <div class="tabs">
          <a href="{{ route('project.index') }}" class="tab-btn active">
            <i class="bi bi-kanban"></i> Project
          </a>

          @if(Auth::user()->role === 'peneliti')
            <a href="{{ route('pengajuan.saya') }}" class="tab-btn">
              <i class="bi bi-clipboard-check"></i> Pengajuan Saya
            </a>
          @endif

          @if(in_array(Auth::user()->role, ['admin','bendahara']))
            <a href="{{ route('pengajuan.masuk') }}" class="tab-btn">
              <i class="bi bi-inbox"></i> Pengajuan Masuk
            </a>
          @endif
        </div>

        <div class="tools-row">
          <div class="tools-left">
            <div class="search-wrap">
              <i class="bi bi-search"></i>
              <input id="searchProject" class="search-input" placeholder="Cari project (nama / tahun / status)">
            </div>

            {{-- Admin & Peneliti boleh ajukan/input --}}
            @if(in_array(Auth::user()->role, ['admin','peneliti']))
              <a href="{{ route('project.create') }}" class="btn-apply">
                <i class="bi bi-plus-lg"></i>
                {{ Auth::user()->role === 'admin' ? 'Input Project' : 'Ajukan Project' }}
              </a>
            @endif
          </div>

          <div class="tools-right">
            <a class="btn-manual"
               href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
               target="_blank" rel="noopener" title="Buka Manual Book">
              <i class="bi bi-book"></i> Manual Book
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  @if ($message = Session::get('success'))
    <div class="alert alert-success">{{ $message }}</div>
  @endif
  @if ($message = Session::get('error'))
    <div class="alert alert-danger">{{ $message }}</div>
  @endif

  @php
    $aktif = $projects->filter(fn($p)=>($p->status ?? 'aktif')==='aktif');
    $arsip = $projects->filter(fn($p)=>($p->status ?? '')==='ditutup');
  @endphp

  <div class="section-head">
    <h6 class="section-title"><i class="bi bi-play-circle-fill"></i> Project Aktif</h6>
    <span class="section-pill"><i class="bi bi-layers"></i> {{ $aktif->count() }} item</span>
  </div>

  <div class="row g-3">
    @foreach($aktif as $p)
      @php
        $isJoined = Auth::user()->role !== 'admin' && in_array($p->id, $joinedProjectIds ?? []);
      @endphp

      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 project-item"
           data-search="{{ strtolower(($p->nama_project ?? '').' '.($p->tahun ?? '').' on going ongoing aktif') }}">
        <div class="proj-card {{ $isJoined ? 'joined' : '' }}" role="button" tabindex="0"
             onclick="location.href='{{ route('project.show',$p->id) }}'"
             onkeydown="if(event.key==='Enter'){ this.click(); }">

          <span class="proj-year">{{ $p->tahun }}</span>
          <span class="proj-status">ON GOING</span>

          <div class="proj-title">{{ $p->nama_project }}</div>

          <div class="proj-meta">
            <div>Sumber Dana: {{ $p->sumberDana->jenis_pendanaan ?? '-' }}</div>
          </div>

          @if(Auth::user()->role !== 'admin' && $isJoined)
            <span class="proj-member">
              <i class="bi bi-people-fill"></i> Kamu tergabung
            </span>
          @endif

          @if(Auth::user()->role === 'admin')
            <div class="proj-actions" onclick="event.stopPropagation()">
              <a href="{{ route('project.edit',$p->id) }}" class="btn btn-sm" title="Edit Project">
                <i class="bi bi-pencil"></i>
              </a>

              <form action="{{ route('project.destroy',$p->id) }}" method="POST"
                    onsubmit="return confirm('Yakin hapus project ini? (soft delete)');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm" title="Hapus Project">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          @endif

        </div>
      </div>
    @endforeach
  </div>

  <div class="section-head">
    <h6 class="section-title"><i class="bi bi-archive-fill"></i> Arsip (Ditutup)</h6>
    <span class="section-pill"><i class="bi bi-layers"></i> {{ $arsip->count() }} item</span>
  </div>

  <div class="row g-3">
    @foreach($arsip as $p)
      @php
        $isJoined = Auth::user()->role !== 'admin' && in_array($p->id, $joinedProjectIds ?? []);
      @endphp

      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 project-item"
           data-search="{{ strtolower(($p->nama_project ?? '').' '.($p->tahun ?? '').' ditutup arsip') }}">
        <div class="proj-card archived {{ $isJoined ? 'joined' : '' }}" role="button" tabindex="0"
             onclick="location.href='{{ route('project.show',$p->id) }}'"
             onkeydown="if(event.key==='Enter'){ this.click(); }">

          <span class="proj-year">{{ $p->tahun }}</span>
          <span class="proj-status">DITUTUP</span>

          <div class="proj-title">{{ $p->nama_project }}</div>
          <div class="proj-meta">Project telah diarsipkan</div>

          @if(Auth::user()->role !== 'admin' && $isJoined)
            <span class="proj-member">
              <i class="bi bi-people-fill"></i> Kamu tergabung
            </span>
          @endif

          @if(Auth::user()->role === 'admin')
            <div class="proj-actions" onclick="event.stopPropagation()">
              <a href="{{ route('project.edit',$p->id) }}" class="btn btn-sm" title="Edit Project">
                <i class="bi bi-pencil"></i>
              </a>

              <form action="{{ route('project.destroy',$p->id) }}" method="POST"
                    onsubmit="return confirm('Yakin hapus project ini? (soft delete)');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm" title="Hapus Project">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          @endif

        </div>
      </div>
    @endforeach
  </div>

@endsection

@push('scripts')
<script>
  // Search filter
  document.getElementById('searchProject')?.addEventListener('input', function(){
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.project-item').forEach(el=>{
      el.style.display = (el.dataset.search || '').includes(q) ? '' : 'none';
    });
  });
</script>
@endpush
