<?php

use App\Http\Controllers\AtasanController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TerlambatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|     Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [HomeController::class, 'index']);
    // Route::get('/', [LemburController::class, 'index']);
    Route::get('/list/{tgl}', [LemburController::class, 'range']);

    Route::get('/lembur', [LemburController::class, 'lembur']);
    Route::get('/lemburforother', [LemburController::class, 'lemburForOther']);
    Route::get('/batallembur/{id}', [LemburController::class, 'cancel']);
    Route::post('/storelembur', [LemburController::class, 'store']);
    Route::post('/storelemburother', [LemburController::class, 'storeOther']);
    Route::get('/status', [LemburController::class, 'successAlert'])->name('status');
    Route::post('/updatelemburstatus', [LemburController::class, 'updateLemburStatus']);
    Route::post('/lewathari', [LemburController::class, 'lewatHari']);

    Route::get('/admin/{month}', [AtasanController::class, 'index']);
    Route::get('/adminlogin', [AtasanController::class, 'login'])->name('admin.login');
    Route::post('/admin/login', [AtasanController::class, 'authenticate']);
    Route::get('/lemburan/{month}', [AtasanController::class, 'lemburan']);
    Route::get('lemburan/export',  [AtasanController::class, 'exportToExcel'])->name('lemburan.export');
    // Route::post('lemburan/read',  [AtasanController::class, 'readExcel'])->name('lemburan.read');

    Route::get('laporan',  [LaporanController::class, 'index'])->name('admin.laporan');
    Route::post('laporan/generate',  [LaporanController::class, 'generateLaporan'])->name('laporan.generate');
    Route::get('laporan/test',  [LaporanController::class, 'testGenerateLaporan']);

    Route::get('cuti',  [CutiController::class, 'index']);

    Route::resource('users', UserController::class);
    Route::post('users/kalibrasi', [UserController::class, 'kalibrasiAbsen']);
    Route::resource('terlambats', TerlambatController::class);
});


Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/logout', [LoginController::class, 'logout']);
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/register', [LoginController::class, 'register']);
Route::post('/register', [LoginController::class, 'storeuser']);
Route::get('/fcm', [LemburController::class, 'sendFCM']);




// Route::post('/uploadabsen', [LaporanController::class, 'upload']);


// Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
//     Route::get('/atasan', [AtasanController::class, 'index'])->name('admin.dashboard');
// });
