<?php

use App\Http\Controllers\Berkas\BerkasSuratMandat;
use App\Http\Controllers\Berkas\BerkasSuratPengesahan;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PendaftaranController;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => 'sso'], function() {
    
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
