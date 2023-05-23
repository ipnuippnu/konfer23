<?php

use App\Http\Controllers\Admin\BroadcastController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DelegatorController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RecapController;
use App\Http\Controllers\Berkas\BerkasSuratMandat;
use App\Http\Controllers\Berkas\BerkasSuratPengesahan;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PendaftaranController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['middleware' => ['sso', 'tutup']], function() {
    
    Route::get('/', WelcomeController::class)->name('/');

    Route::get('/daftar', [PendaftaranController::class, 'index'])->name('daftar');
    Route::post('/daftar', [PendaftaranController::class, 'create']);
    Route::get('/daftar/files/{type}', [PendaftaranController::class, 'berkas'])->name('daftar.file');

    Route::get('/bayar', [PembayaranController::class, 'index'])->name('bayar');
    Route::post('/bayar', [PembayaranController::class, 'create']);
    Route::get('/bayar/bukti', [PembayaranController::class, 'buktiTransfer'])->name('bayar.bukti');


    Route::get('/chat', function(){
        return view('chatting');
    })->name('chat');

});

Route::redirect('pasipasi', sha1('YunYun'), 301);
Route::group(['prefix' => sha1('YunYun'), 'as' => 'admin.'], function(){

    Route::middleware('guest')->group(function(){
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
    });

    Route::middleware('auth')->group(function(){
        Route::get('/', DashboardController::class)->name('/');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/my', ProfileController::class)->name('profile');
        Route::post('/my', [ProfileController::class, 'profileUpdate']);

        Route::post('/change-password', [ProfileController::class, 'changePassword'])->can('change-password')->name('change-password');
        Route::post('/change-picture', [ProfileController::class, 'changePicture'])->can('change-picture')->name('change-picture');

        //Pendaftaran
        Route::get('participants/recap', [RecapController::class, 'participants'])->can('participants-recap')->name('participants.recap');
        Route::apiResource('participants', ParticipantController::class);
        Route::get('delegators/recap', [RecapController::class, 'delegators'])->can('delegators-recap')->name('delegators.recap');
        Route::apiResource('delegators', DelegatorController::class);
        Route::apiResource('payments', PaymentController::class);

        //Tools
        Route::get('broadcast', BroadcastController::class)->name('broadcast');
        Route::post('broadcast/unpaids', [BroadcastController::class, 'sendUnpaids'])->name('broadcast.unpaids');
        Route::post('broadcast/revisions', [BroadcastController::class, 'sendRevisions'])->name('broadcast.revisions');

    });


});



//File
Route::group(['as' => 'file.', 'prefix' => sha1('osas')], function(){
    Route::get('sp/{filename}', function($filename){

        $path = Storage::disk('surat_pengesahan')->path($filename);
        if (!Storage::disk('surat_pengesahan')->exists($filename)) {
            abort(404);
        }
        return response()->file($path);

    })->can('sp-download')->name('sp');

    Route::get('st/{filename}', function($filename){

        $path = Storage::disk('surat_tugas')->path($filename);
        if (!Storage::disk('surat_tugas')->exists($filename)) {
            abort(404);
        }
        return response()->file($path);

    })->can('st-download')->name('st');

    Route::get('bukti_pembayaran/{filename}', function($filename){

        $path = Storage::disk('bukti_transfer')->path($filename);
        if (!Storage::disk('bukti_transfer')->exists($filename)) {
            abort(404);
        }
        return response()->file($path);

    })->can('bukti_pembayaran-download')->name('bukti_pembayaran');
});
