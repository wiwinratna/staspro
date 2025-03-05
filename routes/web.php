<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DetailprojectController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RequestpembelianController;
use App\Http\Controllers\SumberdanaController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/register', [AuthController::class, 'registration'])->name('register.post')->middleware('guest');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::resource('project', ProjectController::class);
    Route::get('/project/proposal/{id}', [ProjectController::class, 'download_proposal'])->name('project.downloadproposal');
    Route::get('/project/rab/{id}', [ProjectController::class, 'download_rab'])->name('project.downloadrab');

    Route::post('/detailproject', [DetailprojectController::class, 'store'])->name('detailproject.store');

    Route::get('/sumberdana', [SumberdanaController::class, 'create'])->name('sumberdana.create');
    Route::post('/sumberdana', [SumberdanaController::class, 'store'])->name('sumberdana.store');
    Route::get('/sumberdana/{id}', [SumberdanaController::class, 'show'])->name('sumberdana.show');

    Route::get('/requestpembelian', [RequestpembelianController::class, 'index'])->name('requestpembelian.index');
    Route::get('/requestpembelian/create', [RequestpembelianController::class, 'create'])->name('requestpembelian.create');
    Route::post('/requestpembelian', [RequestpembelianController::class, 'store'])->name('requestpembelian.store');
    Route::get('/requestpembelian/{id}', [RequestpembelianController::class, 'edit'])->name('requestpembelian.edit');
    Route::post('/requestpembelian/{id}', [RequestpembelianController::class, 'update'])->name('requestpembelian.update');
    Route::get('/requestpembelian/destroy/{id}', [RequestpembelianController::class, 'destroy'])->name('requestpembelian.destroy');
    Route::get('/requestpembelian/detail/{id}', [RequestpembelianController::class, 'detail'])->name('requestpembelian.detail');
    Route::post('/requestpembelian/detail/changestatus', [RequestpembelianController::class, 'changestatus'])->name('requestpembelian.changestatus');
    Route::post('/requestpembelian/detail/store', [RequestpembelianController::class, 'storedetail'])->name('requestpembelian.storedetail');
    Route::get('/requestpembelian/detail/edit/{id}', [RequestpembelianController::class, 'editdetail'])->name('requestpembelian.editdetail');
    Route::post('/requestpembelian/detail/edit/{id}', [RequestpembelianController::class, 'updatedetail'])->name('requestpembelian.updatedetail');
    Route::get('/requestpembelian/detail/destroy/{id}', [RequestpembelianController::class, 'destroydetail'])->name('requestpembelian.destroydetail');

});

Route::get('/pencatatan_transaksi', function () {
    return view('pencatatan_transaksi');
});

Route::get('/laporan_keuangan', function () {
    return view('laporan_keuangan');
});
