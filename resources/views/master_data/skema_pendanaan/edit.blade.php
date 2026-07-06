@extends('layouts.panel')
@section('title', 'Konfigurasi Skema Pendanaan')

@push('styles')
<style>
    .drag-handle { cursor: grab; }
    .drag-handle:active { cursor: grabbing; }
    .komponen-row { transition: all 0.2s ease; }
    .komponen-row.dragging { background: #f8fafc; opacity: 0.5; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 mt-3">
    <h3 class="mb-0">Konfigurasi: {{ $data->nama }} ({{ $data->kode }})</h3>
    <a href="{{ route('skema-pendanaan.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

@if ($message = Session::get('success')) <div class="alert alert-success mb-3">{{ $message }}</div> @endif

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <ul class="nav nav-tabs" id="skemaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Informasi Dasar</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-primary" id="komponen-tab" data-bs-toggle="tab" data-bs-target="#komponen" type="button" role="tab">Komponen Biaya</button>
            </li>
        </ul>
    </div>
    <div class="card-body p-4">
        <div class="tab-content" id="skemaTabsContent">
            
            <!-- TAB 1: Informasi -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <form action="{{ route('skema-pendanaan.update', $data->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Kode Skema</label>
                            <input type="text" name="kode" class="form-control" value="{{ $data->kode }}" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Nama Skema</label>
                            <input type="text" name="nama" class="form-control" value="{{ $data->nama }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Jenis Project</label>
                            <select name="jenis_project_id" class="form-select" required>
                                @foreach($jenisProjects as $j)
                                    <option value="{{ $j->id }}" {{ $data->jenis_project_id == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Pendanaan</label>
                            <select name="jenis_pendanaan_id" class="form-select" required>
                                @foreach($jenisPendanaans as $j)
                                    <option value="{{ $j->id }}" {{ $data->jenis_pendanaan_id == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Provider Pendanaan</label>
                            <select name="provider_id" class="form-select" required>
                                @foreach($providers as $p)
                                    <option value="{{ $p->id }}" {{ $data->provider_id == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3">{{ $data->deskripsi }}</textarea>
                    </div>
                    
                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $data->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </form>
            </div>

            <!-- TAB 2: Komponen Biaya -->
            <div class="tab-pane fade" id="komponen" role="tabpanel">
                <div class="alert alert-light border shadow-sm d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1"><i class="bi bi-info-circle text-primary"></i> Pengaturan Komponen Biaya</h6>
                        <small class="text-muted">Gunakan tombol arah atas/bawah untuk mengubah urutan RAB. Perubahan komponen pada master ini hanya memengaruhi project yang akan dibuat <b>setelah</b> hari ini.</small>
                    </div>
                </div>

                <div class="table-responsive border rounded mb-4">
                    <table class="table table-hover mb-0" id="komponenTable">
                        <thead class="table-light">
                            <tr>
                                <th width="10%" class="text-center">Urutan</th>
                                <th width="50%">Komponen Biaya</th>
                                <th width="20%" class="text-center">Wajib?</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="komponenTbody">
                            @forelse($data->komponen as $index => $k)
                            <tr class="komponen-row" data-id="{{ $k->id }}">
                                <td class="text-center align-middle">
                                    <span class="badge bg-secondary rounded-pill urutan-label" style="font-size:0.9rem; width:28px">{{ $k->urutan }}</span>
                                </td>
                                <td class="align-middle fw-bold">{{ $k->komponenBiaya->nama }}</td>
                                <td class="text-center align-middle">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input toggle-wajib" type="checkbox" data-id="{{ $k->id }}" {{ $k->is_wajib ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-sm btn-light border btn-up" title="Pindah Ke Atas" {{ $loop->first ? 'disabled' : '' }}><i class="bi bi-arrow-up"></i></button>
                                    <button class="btn btn-sm btn-light border btn-down" title="Pindah Ke Bawah" {{ $loop->last ? 'disabled' : '' }}><i class="bi bi-arrow-down"></i></button>
                                    <button class="btn btn-sm btn-danger ms-2 btn-delete" data-id="{{ $k->id }}" title="Hapus"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada komponen biaya untuk skema ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Form Tambah Komponen -->
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="mb-3">Tambah Komponen Baru</h6>
                        <form id="formAddKomponen" class="row gx-2 align-items-end">
                            <div class="col-md-7">
                                <label class="form-label small">Pilih Komponen</label>
                                <select class="form-select" id="new_komponen_id" required>
                                    <option value="">-- Pilih Komponen --</option>
                                    @foreach($availableKomponen as $ak)
                                        <option value="{{ $ak->id }}">{{ $ak->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 text-center pb-1">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" id="new_is_wajib" checked>
                                    <label class="form-check-label small" for="new_is_wajib">Wajib</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus"></i> Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Tampilkan Tab Komponen secara otomatis jika ada parameter ?tab=komponen atau diarahkan dari redirect success create.
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('tab') === 'komponen' || "{{ session('success') }}" != ""){
        var triggerEl = document.querySelector('#komponen-tab');
        var tab = new bootstrap.Tab(triggerEl);
        tab.show();
    }

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" }
    });

    const skemaId = {{ $data->id }};

    // Tambah Komponen
    $('#formAddKomponen').submit(function(e) {
        e.preventDefault();
        let komId = $('#new_komponen_id').val();
        let isWajib = $('#new_is_wajib').is(':checked') ? 1 : 0;
        
        if(!komId) return;

        $.ajax({
            url: `/master/skema-pendanaan/${skemaId}/komponen`,
            type: 'POST',
            data: { komponen_biaya_id: komId, is_wajib: isWajib },
            success: function(res) {
                // Reload halaman tab komponen untuk memperbarui dropdown dan urutan
                window.location.href = window.location.pathname + "?tab=komponen";
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON.error || 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Toggle Wajib
    $(document).on('change', '.toggle-wajib', function() {
        let id = $(this).data('id');
        let isWajib = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: `/master/skema-pendanaan/${skemaId}/komponen/${id}/toggle-wajib`,
            type: 'PUT',
            data: { is_wajib: isWajib }
        });
    });

    // Delete Komponen
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus komponen ini dari skema?',
            text: 'Project yang sudah terbuat sebelumnya tidak akan terpengaruh.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/master/skema-pendanaan/${skemaId}/komponen/${id}`,
                    type: 'DELETE',
                    success: function() {
                        window.location.href = window.location.pathname + "?tab=komponen";
                    }
                });
            }
        });
    });

    // Pindah Ke Atas
    $(document).on('click', '.btn-up', function() {
        let row = $(this).closest('tr');
        if(row.prev().length) {
            row.insertBefore(row.prev());
            updateOrderUI();
            saveOrder();
        }
    });

    // Pindah Ke Bawah
    $(document).on('click', '.btn-down', function() {
        let row = $(this).closest('tr');
        if(row.next().length) {
            row.insertAfter(row.next());
            updateOrderUI();
            saveOrder();
        }
    });

    function updateOrderUI() {
        let rows = $('#komponenTbody tr.komponen-row');
        rows.each(function(idx) {
            $(this).find('.urutan-label').text(idx + 1);
            
            // disable/enable up down buttons
            $(this).find('.btn-up').prop('disabled', idx === 0);
            $(this).find('.btn-down').prop('disabled', idx === rows.length - 1);
        });
    }

    function saveOrder() {
        let orderedIds = [];
        $('#komponenTbody tr.komponen-row').each(function() {
            orderedIds.push($(this).data('id'));
        });

        $.ajax({
            url: `/master/skema-pendanaan/${skemaId}/komponen/reorder`,
            type: 'PUT',
            data: { order: orderedIds }
        });
    }
});
</script>
@endpush
