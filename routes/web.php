<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ProfileController;
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


Route::group(['middleware' => ['sso', 'guest']], function() {
    
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
    });


});
