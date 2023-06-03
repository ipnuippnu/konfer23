<?php

use App\Events\QrCodeScanned;
use App\Exports\EventExport;
use App\Http\Controllers\Admin\BroadcastController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DelegatorController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RecapController;
use App\Http\Controllers\Admin\ScannerController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PendaftaranController;
use App\Models\Code;
use App\Models\Event;
use App\Permissions\AdminPermission;
use App\Permissions\FilePermission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
        Route::get('participants/recap', [RecapController::class, 'participants'])->can(AdminPermission::PARTICIPANT_RECAP)->name('participants.recap');
        Route::apiResource('participants', ParticipantController::class);
        Route::get('delegators/recap', [RecapController::class, 'delegators'])->can(AdminPermission::DELEGATOR_RECAP)->name('delegators.recap');
        Route::apiResource('delegators', DelegatorController::class);
        Route::get('payments/recap', [RecapController::class, 'payments'])->can(AdminPermission::PAYMENT_RECAP)->name('payments.recap');
        Route::apiResource('payments', PaymentController::class);

        // /*!SECTION Tools */

        //Event
        Route::apiResource('events', EventController::class);

        //Broadcast
        Route::get('broadcast', BroadcastController::class)->name('broadcast');
        Route::post('broadcast/unpaids', [BroadcastController::class, 'sendUnpaids'])->name('broadcast.unpaids');
        Route::post('broadcast/revisions', [BroadcastController::class, 'sendRevisions'])->name('broadcast.revisions');

        //Undangan
        Route::apiResource('guests', GuestController::class);
        Route::post('guests/download', [GuestController::class, 'download'])->name('guests.download');

        //Scanner
        Route::apiResource('qr', ScannerController::class);

    });


});



//File
Route::group(['as' => 'file.', 'prefix' => sha1('osas')], function(){
    Route::get('sp/{filename}', function($filename){

        Gate::authorize(FilePermission::SURAT_PENGESAHAN_READ, [$filename]);

        $path = Storage::disk('surat_pengesahan')->path($filename);
        if (!Storage::disk('surat_pengesahan')->exists($filename)) {
            abort(404);
        }
        return response()->file($path);

    })->name('sp');

    Route::get('st/{filename}', function($filename){

        Gate::authorize(FilePermission::SURAT_TUGAS_READ, [$filename]);

        $path = Storage::disk('surat_tugas')->path($filename);
        if (!Storage::disk('surat_tugas')->exists($filename)) {
            abort(404);
        }
        return response()->file($path);

    })->name('st');

    Route::get('bukti_pembayaran/{filename}', function($filename){

        Gate::authorize(FilePermission::BUKTI_TRANSFER_READ, [$filename]);

        $path = Storage::disk('bukti_transfer')->path($filename);
        if (!Storage::disk('bukti_transfer')->exists($filename)) {
            abort(404);
        }
        return response()->file($path);

    })->name('bukti_pembayaran');


});

Route::get('gas', function(){
    return Excel::download(new EventExport(Event::latest()->first()), 'a.xlsx');
});