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
    background:#fff;
    color:var(--ink-600);
    border-radius:20px;
    padding:20px;
    height:100%;
    min-height:180px;
    position:relative;
    box-shadow:0 6px 16px rgba(15,23,42,.04);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    cursor:pointer;
    border:1px solid rgba(226,232,240,.95);
    display:flex;
    flex-direction:column;
  }
  .proj-card:hover{ 
    transform:translateY(-3px); 
    box-shadow:0 14px 28px rgba(15,23,42,.08); 
    border-color:#bbf7d0;
  }
  .proj-card.archived{
    background:#f8fafc;
    border:1px dashed #cbd5e1;
    box-shadow:none;
  }
  .proj-card.archived:hover{
    box-shadow:0 8px 16px rgba(15,23,42,.04);
    border-color:#94a3b8;
  }

  .proj-title{
    font-size:1.1rem; font-weight:800; line-height:1.3; color:#1e293b; margin-bottom:12px;
    display:-webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
  }
  .proj-meta-grid { display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom:12px; }
  .proj-meta-grid > div { display:flex; flex-direction:column; }
  .proj-meta-val {
    font-size:0.85rem; font-weight:700; color:#334155; line-height:1.2; margin-bottom:2px;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .proj-meta-val.wrap-2 { white-space:normal; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
  .proj-meta-lbl { font-size:0.65rem; color:#94a3b8; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; }
  .proj-footer {
    margin-top:auto; padding-top:12px; border-top:1px solid #f1f5f9;
    display:flex; justify-content:space-between; align-items:center;
  }
  .proj-footer-status { font-size:.72rem; font-weight:800; padding:.2rem .6rem; border-radius:4px; }
  .proj-footer-status.ongoing { background:#ecfdf5; color:#15803d; }
  .proj-footer-status.closed { background:#f1f5f9; color:#64748b; }
  
  .proj-card.joined{ border-color:#bbf7d0; }

  /* FILTER CARD BAR */
  .filter-card{
    background:#fff;
    border:1px solid rgba(226,232,240,.95);
    border-radius:20px;
    padding:12px 16px;
    box-shadow:0 10px 30px rgba(15,23,42,.05);
    margin-top:-10px; /* pull up into hero slightly if needed, or keep it below */
    margin-bottom:20px;
    position:relative;
    z-index:10;
  }
  .filter-row{
    display:flex;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
  }
  .filter-label{
    font-weight:900;
    font-size:.84rem;
    color:#475569;
    white-space:nowrap;
    margin-right:4px;
  }
  .filter-select, .filter-input{
    height:38px;
    border-radius:999px;
    border:1px solid rgba(226,232,240,.95);
    padding:0 16px;
    font-weight:700;
    font-size:.85rem;
    background:#fff;
    outline:none;
    transition:all .2s;
  }
  .filter-select:focus, .filter-input:focus{
    border-color:rgba(22,163,74,.4);
    box-shadow:0 0 0 .2rem rgba(22,163,74,.05);
  }
  .filter-input{ width:300px; flex-grow:1; }

  .filter-reset{
    height:38px; width:38px;
    display:inline-flex; align-items:center; justify-content:center;
    border-radius:999px;
    border:1px solid rgba(226,232,240,.95);
    background:#f8fafc;
    color:#64748b;
    text-decoration:none;
    transition:.15s;
  }
  .filter-reset:hover{ background:#f1f5f9; color:#15803d; transform:rotate(-45deg); }

  .btn-filter-apply{
    height:38px;
    display:inline-flex; align-items:center; gap:8px;
    padding:0 18px;
    border-radius:999px;
    background:linear-gradient(135deg,#15803d,#16a34a);
    color:#fff;
    font-weight:900;
    font-size:.88rem;
    border:0;
    box-shadow:0 10px 20px rgba(22,163,74,.15);
    transition:transform .15s;
    text-decoration:none;
  }
  .btn-filter-apply:hover{ transform:translateY(-1px); color:#fff; brightness:1.05; }
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

        <div class="tools-row">
          <div class="tools-left">
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
               href="https://drive.google.com/file/d/1HKaZH2I-Ohq7S-SBb8ADMHMd3htU0nio/view?usp=sharing"
               target="_blank" rel="noopener" title="Buka Manual Book">
               <i class="bi bi-book"></i> Manual Book
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- FILTER BAR --}}
  <div class="filter-card">
    <form action="{{ route('project.index') }}" method="GET" class="filter-row">
      <div class="filter-label">Filter Project</div>
      
      <select name="filter" class="filter-select">
        <option value="all" {{ ($filter ?? 'all') === 'all' ? 'selected' : '' }}>All Project</option>
        <option value="my" {{ ($filter ?? '') === 'my' ? 'selected' : '' }}>My project</option>
        <option value="except_me" {{ ($filter ?? '') === 'except_me' ? 'selected' : '' }}>Except Me</option>
      </select>

      <input type="text" id="searchProject" name="q" class="filter-input" placeholder="Cari project (nama / tahun / status)..." value="{{ request('q') }}">

      <a href="{{ route('project.index') }}" class="filter-reset" title="Reset Filter">
        <i class="bi bi-arrow-counterclockwise"></i>
      </a>

      <div class="ms-auto">
        <button type="submit" class="btn-filter-apply">
          <i class="bi bi-funnel-fill"></i> Terapkan
        </button>
      </div>
    </form>
  </div>

  @if ($message = Session::get('success'))
    <div class="alert alert-success">{{ $message }}</div>
  @endif
  @if ($message = Session::get('error'))
    <div class="alert alert-danger">{{ $message }}</div>
  @endif

  @php
    $aktif = $projects->filter(fn($p)=>($p->status ?? 'aktif')==='aktif');
    $aktifPenelitian = $aktif->filter(fn($p) => strtolower($p->tipe_project ?? '') === 'penelitian');
    $aktifAbdimas = $aktif->filter(fn($p) => strtolower($p->tipe_project ?? '') === 'abdimas');
    $aktifLainnya = $aktif->filter(fn($p) => !in_array(strtolower($p->tipe_project ?? ''), ['penelitian', 'abdimas']));
    
    $arsip = $projects->filter(fn($p)=>($p->status ?? '')==='ditutup');
  @endphp

  @if($aktifPenelitian->count() > 0)
  <div class="section-head mt-4">
    <h6 class="section-title"><i class="bi bi-play-circle-fill"></i> Project Penelitian (Aktif)</h6>
    <span class="section-pill"><i class="bi bi-layers"></i> {{ $aktifPenelitian->count() }} item</span>
  </div>
  <div class="row g-3">
    @foreach($aktifPenelitian as $p)
      @php
        $isJoined = Auth::user()->role !== 'admin' && in_array($p->id, $joinedProjectIds ?? []);
      @endphp

      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 project-item"
           data-search="{{ strtolower(($p->nama_project ?? '').' '.($p->tahun ?? '').' '.($p->tipe_project ?? '').' on going ongoing aktif') }}">
        <div class="proj-card {{ $isJoined ? 'joined' : '' }}" role="button" tabindex="0"
             onclick="location.href='{{ route('project.show',$p->id) }}'"
             onkeydown="if(event.key==='Enter'){ this.click(); }">

          <!-- Title -->
          <div class="proj-title" title="{{ $p->nama_project }}">{{ $p->nama_project }}</div>
          
          <!-- Grid 2 Cols -->
          <div class="proj-meta-grid">
            <div>
              <div class="proj-meta-val">{{ $p->tipe_project ?? 'Penelitian' }}</div>
              <div class="proj-meta-lbl">Tipe Project</div>
            </div>
            <div class="text-end">
              <div class="proj-meta-val">{{ $p->tahun }}</div>
              <div class="proj-meta-lbl">Tahun</div>
            </div>
          </div>
          
          <!-- Full Row -->
          <div class="mb-3">
            <div class="proj-meta-val wrap-2">{{ ucwords(strtolower($p->sumberDana->nama ?? $p->sumberDana->nama_sumber_dana ?? 'Internal')) }}</div>
            <div class="proj-meta-lbl">Sumber Dana ({{ $p->sumberDana->jenis_pendanaan ?? '-' }})</div>
          </div>

          <!-- Footer -->
          <div class="proj-footer">
            <div class="proj-footer-status ongoing">ON GOING</div>
            
            <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
              @if(Auth::user()->role !== 'admin' && $isJoined)
                <span class="badge bg-light text-primary border border-primary-subtle rounded-pill" style="font-size:0.7rem"><i class="bi bi-people-fill"></i> Tim</span>
              @endif

              @if(Auth::user()->role === 'admin')
                <a href="{{ route('project.edit',$p->id) }}" class="text-secondary hover-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('project.destroy',$p->id) }}" method="POST" class="m-0"
                      onsubmit="return confirm('Yakin hapus project ini? (soft delete)');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-link text-danger p-0 border-0 text-decoration-none hover-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  @endif

  @if($aktifAbdimas->count() > 0)
  <div class="section-head mt-4">
    <h6 class="section-title"><i class="bi bi-play-circle-fill"></i> Project Abdimas (Aktif)</h6>
    <span class="section-pill"><i class="bi bi-layers"></i> {{ $aktifAbdimas->count() }} item</span>
  </div>
  <div class="row g-3">
    @foreach($aktifAbdimas as $p)
      @php
        $isJoined = Auth::user()->role !== 'admin' && in_array($p->id, $joinedProjectIds ?? []);
      @endphp

      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 project-item"
           data-search="{{ strtolower(($p->nama_project ?? '').' '.($p->tahun ?? '').' '.($p->tipe_project ?? '').' on going ongoing aktif') }}">
        <div class="proj-card {{ $isJoined ? 'joined' : '' }}" role="button" tabindex="0"
             onclick="location.href='{{ route('project.show',$p->id) }}'"
             onkeydown="if(event.key==='Enter'){ this.click(); }">

          <!-- Title -->
          <div class="proj-title" title="{{ $p->nama_project }}">{{ $p->nama_project }}</div>
          
          <!-- Grid 2 Cols -->
          <div class="proj-meta-grid">
            <div>
              <div class="proj-meta-val">{{ $p->tipe_project ?? 'Abdimas' }}</div>
              <div class="proj-meta-lbl">Tipe Project</div>
            </div>
            <div class="text-end">
              <div class="proj-meta-val">{{ $p->tahun }}</div>
              <div class="proj-meta-lbl">Tahun</div>
            </div>
          </div>
          
          <!-- Full Row -->
          <div class="mb-3">
            <div class="proj-meta-val wrap-2">{{ ucwords(strtolower($p->sumberDana->nama ?? $p->sumberDana->nama_sumber_dana ?? 'Internal')) }}</div>
            <div class="proj-meta-lbl">Sumber Dana ({{ $p->sumberDana->jenis_pendanaan ?? '-' }})</div>
          </div>

          <!-- Footer -->
          <div class="proj-footer">
            <div class="proj-footer-status ongoing">ON GOING</div>
            
            <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
              @if(Auth::user()->role !== 'admin' && $isJoined)
                <span class="badge bg-light text-primary border border-primary-subtle rounded-pill" style="font-size:0.7rem"><i class="bi bi-people-fill"></i> Tim</span>
              @endif

              @if(Auth::user()->role === 'admin')
                <a href="{{ route('project.edit',$p->id) }}" class="text-secondary hover-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('project.destroy',$p->id) }}" method="POST" class="m-0"
                      onsubmit="return confirm('Yakin hapus project ini? (soft delete)');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-link text-danger p-0 border-0 text-decoration-none hover-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  @endif

  @if($aktifLainnya->count() > 0)
  <div class="section-head mt-4">
    <h6 class="section-title"><i class="bi bi-play-circle-fill"></i> Project Lainnya (Aktif)</h6>
    <span class="section-pill"><i class="bi bi-layers"></i> {{ $aktifLainnya->count() }} item</span>
  </div>
  <div class="row g-3">
    @foreach($aktifLainnya as $p)
      @php
        $isJoined = Auth::user()->role !== 'admin' && in_array($p->id, $joinedProjectIds ?? []);
      @endphp

      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 project-item"
           data-search="{{ strtolower(($p->nama_project ?? '').' '.($p->tahun ?? '').' '.($p->tipe_project ?? '').' on going ongoing aktif') }}">
        <div class="proj-card {{ $isJoined ? 'joined' : '' }}" role="button" tabindex="0"
             onclick="location.href='{{ route('project.show',$p->id) }}'"
             onkeydown="if(event.key==='Enter'){ this.click(); }">

          <!-- Title -->
          <div class="proj-title" title="{{ $p->nama_project }}">{{ $p->nama_project }}</div>
          
          <!-- Grid 2 Cols -->
          <div class="proj-meta-grid">
            <div>
              <div class="proj-meta-val">{{ $p->tipe_project ?? '-' }}</div>
              <div class="proj-meta-lbl">Tipe Project</div>
            </div>
            <div class="text-end">
              <div class="proj-meta-val">{{ $p->tahun }}</div>
              <div class="proj-meta-lbl">Tahun</div>
            </div>
          </div>
          
          <!-- Full Row -->
          <div class="mb-3">
            <div class="proj-meta-val wrap-2">{{ ucwords(strtolower($p->sumberDana->nama ?? $p->sumberDana->nama_sumber_dana ?? 'Internal')) }}</div>
            <div class="proj-meta-lbl">Sumber Dana ({{ $p->sumberDana->jenis_pendanaan ?? '-' }})</div>
          </div>

          <!-- Footer -->
          <div class="proj-footer">
            <div class="proj-footer-status ongoing">ON GOING</div>
            
            <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
              @if(Auth::user()->role !== 'admin' && $isJoined)
                <span class="badge bg-light text-primary border border-primary-subtle rounded-pill" style="font-size:0.7rem"><i class="bi bi-people-fill"></i> Tim</span>
              @endif

              @if(Auth::user()->role === 'admin')
                <a href="{{ route('project.edit',$p->id) }}" class="text-secondary hover-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('project.destroy',$p->id) }}" method="POST" class="m-0"
                      onsubmit="return confirm('Yakin hapus project ini? (soft delete)');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-link text-danger p-0 border-0 text-decoration-none hover-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  @endif

  <div class="section-head mt-4">
    <h6 class="section-title"><i class="bi bi-archive-fill"></i> Arsip (Ditutup)</h6>
    <span class="section-pill"><i class="bi bi-layers"></i> {{ $arsip->count() }} item</span>
  </div>

  <div class="row g-3">
    @foreach($arsip as $p)
      @php
        $isJoined = Auth::user()->role !== 'admin' && in_array($p->id, $joinedProjectIds ?? []);
      @endphp

      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 project-item"
           data-search="{{ strtolower(($p->nama_project ?? '').' '.($p->tahun ?? '').' '.($p->tipe_project ?? '').' ditutup arsip') }}">
        <div class="proj-card archived {{ $isJoined ? 'joined' : '' }}" role="button" tabindex="0"
             onclick="location.href='{{ route('project.show',$p->id) }}'"
             onkeydown="if(event.key==='Enter'){ this.click(); }">

          <!-- Title -->
          <div class="proj-title text-muted" title="{{ $p->nama_project }}">{{ $p->nama_project }}</div>
          
          <!-- Grid 2 Cols -->
          <div class="proj-meta-grid">
            <div>
              <div class="proj-meta-val text-muted">{{ $p->tipe_project ?? 'Penelitian' }}</div>
              <div class="proj-meta-lbl">Tipe Project</div>
            </div>
            <div class="text-end">
              <div class="proj-meta-val text-muted">{{ $p->tahun }}</div>
              <div class="proj-meta-lbl">Tahun</div>
            </div>
          </div>

          <!-- Footer -->
          <div class="proj-footer mt-2 border-0 pt-0">
            <div class="proj-footer-status closed">DITUTUP</div>
            
            <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
              @if(Auth::user()->role !== 'admin' && $isJoined)
                <span class="badge bg-light text-secondary border border-secondary-subtle rounded-pill" style="font-size:0.7rem"><i class="bi bi-people-fill"></i> Tim</span>
              @endif

              @if(Auth::user()->role === 'admin')
                <a href="{{ route('project.edit',$p->id) }}" class="text-secondary hover-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('project.destroy',$p->id) }}" method="POST" class="m-0"
                      onsubmit="return confirm('Yakin hapus project ini? (soft delete)');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-link text-danger p-0 border-0 text-decoration-none hover-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

@endsection

@push('scripts')
<script>
  // Script pencarian client-side dihilangkan karena sekarang menggunakan
  // server-side search via form Terapkan.
</script>
@endpush
