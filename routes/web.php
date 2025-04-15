<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailprojectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RequestpembelianController;
use App\Http\Controllers\SumberdanaController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('auth');
    Route::get('/peneliti-dashboard', [DashboardController::class, 'index'])->name('peneliti.dashboard');

    Route::post('/detailproject', [DetailprojectController::class, 'store'])->name('detailproject.store');

    Route::get('/project', [ProjectController::class, 'index'])->name('project.index');
    Route::get('/project/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/project', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/project/{project}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/project/download_proposal/{id}', [ProjectController::class, 'download_proposal'])->name('project.downloadproposal');
    Route::get('/project/download_rab/{id}', [ProjectController::class, 'download_rab'])->name('project.downloadrab');
    Route::get('/project/sumberdana/{id}', [ProjectController::class, 'getSubkategori'])->name('project.get_subkategori');
    Route::get('/project/{id}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    Route::put('/project/{id}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/project/{id}', [ProjectController::class, 'destroy'])->name('project.destroy');

    Route::get('/requestpembelian', [RequestpembelianController::class, 'index'])->name('requestpembelian.index');
    Route::get('/requestpembelian/create', [RequestpembelianController::class, 'create'])->name('requestpembelian.create');
    Route::post('/requestpembelian', [RequestpembelianController::class, 'store'])->name('requestpembelian.store');
    Route::get('/requestpembelian/{id}', [RequestpembelianController::class, 'edit'])->name('requestpembelian.edit');
    Route::post('/requestpembelian/{id}', [RequestpembelianController::class, 'update'])->name('requestpembelian.update');
    Route::get('/requestpembelian/destroy/{id}', [RequestpembelianController::class, 'destroy'])->name('requestpembelian.destroy');
    Route::get('/requestpembelian/detail/{id}', [RequestpembelianController::class, 'detail'])->name('requestpembelian.detail');
    Route::post('/requestpembelian/detail/changestatus', [RequestpembelianController::class, 'changestatus'])->name('requestpembelian.changestatus');
    Route::post('/requestpembelian/detail/store', [RequestpembelianController::class, 'storedetail'])->name('requestpembelian.storedetail');
    Route::get('/requestpembelian/detail/bukti/{id}', [RequestpembelianController::class, 'addbukti'])->name('requestpembelian.addbukti');
    Route::get('/requestpembelian/detail/edit/{id}', [RequestpembelianController::class, 'editdetail'])->name('requestpembelian.editdetail');
    Route::post('/requestpembelian/detail/bukti/{id}', [RequestpembelianController::class, 'storebukti'])->name('requestpembelian.storebukti');
    Route::post('/requestpembelian/detail/edit/{id}', [RequestpembelianController::class, 'updatedetail'])->name('requestpembelian.updatedetail');
    Route::get('/requestpembelian/detail/destroy/{id}', [RequestpembelianController::class, 'destroydetail'])->name('requestpembelian.destroydetail');
    Route::get('/requestpembelian/detail/pengajuanulang/{id}', [RequestpembelianController::class, 'pengajuanulang'])->name('requestpembelian.pengajuanulang');

    Route::get('sumberdana', [SumberdanaController::class, 'index'])->name('sumberdana.index');
    Route::get('sumberdana/create', [SumberdanaController::class, 'create'])->name('sumberdana.create');
    Route::post('sumberdana', [SumberdanaController::class, 'store'])->name('sumberdana.store');
    Route::get('sumberdana/edit/{id}', [SumberdanaController::class, 'edit'])->name('sumberdana.edit');
    Route::post('sumberdana/{id}', [SumberdanaController::class, 'update'])->name('sumberdana.update');
    Route::get('sumberdana/destroy/{id}', [SumberdanaController::class, 'destroy'])->name('sumberdana.destroy');
    Route::get('sumberdana/detail/{id}', [SumberdanaController::class, 'detail'])->name('sumberdana.detail');
    Route::post('sumberdana/detail/store', [SumberdanaController::class, 'storedetail'])->name('sumberdana.storedetail');
    Route::get('sumberdana/detail/destroy/{id}', [SumberdanaController::class, 'destroydetail'])->name('sumberdana.destroydetail');

    Route::middleware(['auth'])->group(function () {
        Route::get('/pencatatan-transaksi', [TransaksiController::class, 'index'])->name('pencatatan_transaksi');
        Route::get('/form_input_transaksi', [TransaksiController::class, 'create'])->name('form_input_transaksi');
        Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/edit/{id}', [TransaksiController::class, 'edit'])->name('transaksi.edit');
        Route::put('/transaksi/{id}', [TransaksiController::class, 'update'])->name('transaksi.update');
        Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');
    });

    Route::get('/get-subkategori', [TransaksiController::class, 'getSubkategori'])->name('getSubkategori');

    // Filter Transaksi (jika diperlukan)
    Route::get('/filter_transaksi', [TransaksiController::class, 'filterTransaksi'])->name('filter_transaksi');

    // Laporan Keuangan dengan filter berdasarkan Tim Penelitian dan Kategori Pendanaan
    Route::get('/laporan_keuangan', [TransaksiController::class, 'laporanKeuangan'])->name('laporan_keuangan');
    Route::get('/laporan/export/{format}', [TransaksiController::class, 'export'])->name('laporan.export');
});
