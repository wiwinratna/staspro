<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DetailprojectController;
use App\Http\Controllers\RequestpembelianController;
use App\Http\Controllers\SumberdanaController;
use App\Http\Controllers\PencatatanKeuanganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KasTransactionController;
use App\Http\Controllers\BendaharaDashboardController;
use App\Http\Controllers\ProjectFundingController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengajuanTransaksiController;

/*
|--------------------------------------------------------------------------
| PUBLIC (Guest) Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/login'));

Route::middleware('guest')->controller(AuthController::class)->group(function () {
    // User Auth
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'login')->name('login.post');

    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register')->name('register.post');

    // Admin Auth
    Route::get('/admin/login', 'showAdminAuth')->name('admin.login');
    Route::post('/admin/login', 'loginAdmin')->name('admin.login.post');

    // Biar 1 tab (admin register diarahkan ke login)
    Route::get('/admin/register', fn () => redirect()->route('admin.login'))->name('admin.register');
    Route::post('/admin/register', 'registerAdmin')->name('admin.register.post');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
    Route::post('/admin/logout', 'logoutAdmin')->name('admin.logout');
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED Routes (wajib login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'profile.complete'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::get('/profile/edit', 'edit')->name('profile.edit');
        Route::put('/profile', 'update')->name('profile.update');
        Route::post('/profile/update-password', 'updatePassword')->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/admin/dashboard', 'index')->name('admin.dashboard');
        Route::get('/peneliti-dashboard', 'index')->name('peneliti.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Project
    |--------------------------------------------------------------------------
    */
    Route::controller(ProjectController::class)->group(function () {
        Route::get('/project', 'index')->name('project.index');
        Route::get('/project/create', 'create')->name('project.create');
        Route::post('/project', 'store')->name('project.store');
        Route::get('/project/{project}', 'show')->name('project.show');

        Route::get('/project/{id}/edit', 'edit')->name('project.edit');
        Route::put('/project/{id}', 'update')->name('project.update');
        Route::delete('/project/{id}', 'destroy')->name('project.destroy');

        Route::get('/project/tracking', 'tracking')->name('project.tracking');

        Route::get('/project/download_proposal/{id}', 'download_proposal')->name('project.downloadproposal');
        Route::get('/project/download_rab/{id}', 'download_rab')->name('project.downloadrab');
        Route::get('/project/{project}/detail-pembelian/export', 'exportDetailPembelianExcel')->name('project.detail_pembelian.export');

        Route::get('/project/sumberdana/{id}', 'getSubkategori')->name('project.get_subkategori');
        Route::get('/project/sumberdana-by-tipe/{tipe}', 'getSumberDanaByTipe')->name('project.get_sumberdana_by_tipe');

        Route::post('/project/{id}/close', 'close')->name('project.close');
        
        Route::post('/project/{project}/ketua', 'setKetua')->name('project.setKetua');
        Route::post('/project/{project}/set-ketua', 'setKetua');

        Route::get('/project/{project}/subcategories', 'getProjectSubcategories');
        Route::delete('/project/{project}/member/{user}', 'removeMember')->name('project.member.remove');

        Route::get('/project/{project}/rab/revise', 'rabRevise')->name('project.rab.revise');
        Route::post('/project/{project}/rab/revise', 'rabReviseSave')->name('project.rab.revise.save');
    });

    Route::post('/detailproject', [DetailprojectController::class, 'store'])->name('detailproject.store');

    /*
    |--------------------------------------------------------------------------
    | Pengajuan Komponen & Verifikasi Bendahara
    |--------------------------------------------------------------------------
    */
    Route::controller(RequestpembelianController::class)->group(function () {
        Route::get('/requestpembelian', 'index')->name('requestpembelian.index');
        Route::get('/requestpembelian/track', 'track')->name('requestpembelian.track');
        Route::post('/requestpembelian/track/{id}/sampai', 'markSampai')->name('requestpembelian.track.sampai');
        Route::post('/requestpembelian/track/{id}/pelaporan', 'markPelaporan')->name('requestpembelian.track.pelaporan');
        Route::get('/requestpembelian/create', 'create')->name('requestpembelian.create');
        Route::post('/requestpembelian', 'store')->name('requestpembelian.store');
        Route::post('/requestpembelian/{id}/submit', 'submitRequest')->name('requestpembelian.submit');

        Route::get('/requestpembelian/{id}', 'edit')->whereNumber('id')->name('requestpembelian.edit');
        Route::post('/requestpembelian/{id}', 'update')->whereNumber('id')->name('requestpembelian.update');
        Route::delete('/requestpembelian/destroy/{id}', 'destroy')->name('requestpembelian.destroy');

        Route::get('/requestpembelian/detail/{id}', 'detail')->name('requestpembelian.detail');
        Route::post('/requestpembelian/detail/changestatus', 'changestatus')->name('requestpembelian.changestatus');
        Route::post('/requestpembelian/detail/store', 'storedetail')->name('requestpembelian.storedetail');

        Route::get('/requestpembelian/detail/bukti/{id}', 'addbukti')->name('requestpembelian.addbukti');
        Route::post('/requestpembelian/detail/bukti/{id}', 'storebukti')->name('requestpembelian.storebukti');
        Route::post('/requestpembelian/detail/invoice/{id}', 'storeInvoiceItem')->name('requestpembelian.storeinvoiceitem');
        Route::post('/requestpembelian/detail/invoice-bulk/{id}', 'storeInvoiceBulk')->name('requestpembelian.storeinvoicebulk');
        
        Route::get('/requestpembelian/talangan', 'talanganIndex')->name('requestpembelian.talangan.index');
        Route::post('/requestpembelian/talangan/{id}/alokasi', 'talanganAllocate')->name('requestpembelian.talangan.allocate');

        Route::get('/requestpembelian/detail/edit/{id}', 'editdetail')->name('requestpembelian.editdetail');
        Route::post('/requestpembelian/detail/edit/{id}', 'updatedetail')->name('requestpembelian.updatedetail');
        Route::match(['get', 'post', 'delete'], '/requestpembelian/detail/destroy/{id}', 'destroydetail')->name('requestpembelian.destroydetail');
        Route::get('/requestpembelian/detail/pengajuanulang/{id}', 'pengajuanulang')->name('requestpembelian.pengajuanulang');

        // Verifikasi Bendahara
        Route::get('/bendahara/verifikasi', 'bendaharaIndex')->name('bendahara.verifikasi');
        Route::post('/bendahara/verifikasi/{id}/approve', 'bendaharaApprove')->name('bendahara.approve');
        Route::post('/bendahara/verifikasi/{id}/reject', 'bendaharaReject')->name('bendahara.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Sumber Dana
    |--------------------------------------------------------------------------
    */
    Route::controller(SumberdanaController::class)->group(function () {
        Route::get('sumberdana', 'index')->name('sumberdana.index');
        Route::get('sumberdana/create', 'create')->name('sumberdana.create');
        Route::post('sumberdana', 'store')->name('sumberdana.store');

        Route::get('sumberdana/edit/{id}', 'edit')->name('sumberdana.edit');
        Route::post('sumberdana/{id}', 'update')->name('sumberdana.update');
        Route::delete('sumberdana/destroy/{id}', 'destroy')->name('sumberdana.destroy');

        Route::get('sumberdana/detail/{id}', 'detail')->name('sumberdana.detail');
        Route::post('sumberdana/detail/store', 'storedetail')->name('sumberdana.storedetail');
        Route::get('sumberdana/detail/destroy/{id}', 'destroydetail')->name('sumberdana.destroydetail');
        Route::post('sumberdana/detail/update/{id}', 'updatesubkategori')->name('sumberdana.updatesubkategori');
    });

    /*
    |--------------------------------------------------------------------------
    | Pengajuan Transaksi (Pengajuan Dana + Reimbursement)
    |--------------------------------------------------------------------------
    */
    Route::prefix('pengajuan-transaksi')->name('pengajuan_transaksi.')->controller(PengajuanTransaksiController::class)->group(function () {
        Route::get('/', 'index')->name('index');

        Route::get('/create/pengajuan', 'createPengajuan')->name('create_pengajuan');
        Route::post('/store/pengajuan', 'storePengajuan')->name('store_pengajuan');

        Route::get('/create/reimbursement', 'createReimbursement')->name('create_reimbursement');
        Route::post('/store/reimbursement', 'storeReimbursement')->name('store_reimbursement');

        Route::get('/{id}', 'show')->name('show');

        // Action
        Route::post('/{id}/approve', 'approve')->name('approve');
        Route::post('/{id}/reject', 'reject')->name('reject');
        Route::post('/{id}/upload-bukti', 'uploadBukti')->name('upload_bukti');
        Route::post('/{id}/finalize', 'finalize')->name('finalize');

        Route::get('/subkategori/{project}', 'subkategori')->name('subkategori');
    });

    /*
    |--------------------------------------------------------------------------
    | Pencatatan Keuangan + Laporan
    |--------------------------------------------------------------------------
    */
    Route::controller(PencatatanKeuanganController::class)->group(function () {
        Route::get('/pencatatan-keuangan', 'index')->name('pencatatan_keuangan');
        Route::get('/form_input_pencatatan_keuangan', 'create')->name('form_input_pencatatan_keuangan');
        Route::post('/pencatatan-keuangan/store', 'store')->name('pencatatan_keuangan.store');
        Route::get('/pencatatan-keuangan/edit/{id}', 'edit')->name('pencatatan_keuangan.edit');
        Route::put('/pencatatan-keuangan/{id}', 'update')->name('pencatatan_keuangan.update');
        Route::delete('/pencatatan_keuangan/{id}', 'destroy')->name('pencatatan_keuangan.destroy');

        Route::get('/get-subkategori', 'getSubkategori')->name('getSubkategori');
        Route::get('/filter-pencatatan-keuangan', 'filterTransaksi')->name('filter_pencatatan_keuangan');

        Route::get('/laporan_keuangan', 'laporanKeuangan')->name('laporan_keuangan');
        Route::get('/laporan/export/{format}', 'export')->name('laporan.export');
    });

    /*
    |--------------------------------------------------------------------------
    | Users Management
    |--------------------------------------------------------------------------
    */
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::get('/users/create', 'create')->name('users.create');
        Route::post('/users', 'store')->name('users.store');
        Route::get('/users/{user}/edit', 'edit')->name('users.edit');
        Route::put('/users/{user}', 'update')->name('users.update');
        Route::delete('/users/{user}', 'destroy')->name('users.destroy');
        Route::post('/users/{user}/change-password', 'changePassword')->name('users.changePassword');
    });

    /*
    |--------------------------------------------------------------------------
    | Bendahara Area (Dashboard, Kas, Funding)
    |--------------------------------------------------------------------------
    */
    Route::get('/bendahara/dashboard', [BendaharaDashboardController::class, 'index'])->name('bendahara.dashboard');

    Route::controller(KasTransactionController::class)->group(function () {
        Route::get('/kas', 'index')->name('kas.index');
        Route::get('/kas/create', 'create')->name('kas.create');
        Route::post('/kas', 'store')->name('kas.store');
    });

    Route::controller(ProjectFundingController::class)->group(function () {
        Route::get('/funding', 'index')->name('funding.index');
        Route::get('/funding/create', 'create')->name('funding.create');
        Route::post('/funding', 'store')->name('funding.store');
        Route::get('/funding/{id}/bukti', 'downloadBukti')->name('funding.bukti');
    });

    /*
    |--------------------------------------------------------------------------
    | Pengajuan Akses Admin/Bendahara
    |--------------------------------------------------------------------------
    */
    Route::controller(PengajuanController::class)->group(function () {
        // Pengajuan Saya (Peneliti)
        Route::get('/pengajuan-saya', 'index')->name('pengajuan.saya');

        // Pengajuan Masuk (Admin & Bendahara)
        Route::middleware(['role:admin,bendahara'])->group(function () {
            Route::get('/pengajuan-masuk', 'masuk')->name('pengajuan.masuk');
            Route::post('/pengajuan/{project}/fund', 'fund')->name('pengajuan.fund');
            Route::post('/pengajuan/{project}/finalize', 'finalize')->name('pengajuan.finalize');
        });

        // Approve/Reject (Khusus Admin)
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/pengajuan/{project}/approve', 'approve')->name('pengajuan.approve');
            Route::post('/pengajuan/{project}/reject', 'reject')->name('pengajuan.reject');
        });
    });

});
