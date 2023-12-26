<?php

use App\Http\Controllers\api\AtasanController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CutiController;
use App\Http\Controllers\api\JenisCutiController;
use App\Http\Controllers\api\LemburController;
use App\Http\Controllers\api\RolesController;
use App\Http\Controllers\api\SupervisorController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('absen-pertama',  [LaporanController::class, 'cekFileAbsen']);

Route::post('login', [AuthController::class, 'login']);
Route::post('loginuser', [AuthController::class, 'loginUser']);
Route::post('register', [AuthController::class, 'store']);
Route::post('user/{id}/updatepassword', [AuthController::class, 'updateUserPassword']);

Route::post('lembur', [LemburController::class, 'index']);
Route::post('lembur/search', [LemburController::class, 'search']);
Route::get('lembur/{id}', [LemburController::class, 'getByIdSpv']);
Route::get('lembur/user/{id}', [LemburController::class, 'getByIdUser']);
Route::post('lembur/{id}/update', [LemburController::class, 'update']);
Route::get('tobeexpired/{id}', [LemburController::class, 'toBeExpired']);
Route::get('detail/{id}', [LemburController::class, 'show']);
Route::post('approval/{id}', [LemburController::class, 'approval']);

Route::get('atasan', [AtasanController::class, 'index']);
Route::post('atasan', [AtasanController::class, 'store']);
Route::put('atasan/{id}/password', [AtasanController::class, 'updatePassword']);
Route::post('inuptoken/{token}', [AuthController::class, 'inupToken']);
Route::delete('inuptoken/{token}', [AuthController::class, 'deleteToken']);

Route::get('supervisor', [SupervisorController::class, 'index']);
Route::get('supervisor/{id}', [SupervisorController::class, 'show']);
Route::post('supervisor', [SupervisorController::class, 'store']);
Route::put('supervisor/{id}', [SupervisorController::class, 'update']);
Route::put('supervisor/{id}/update-password', [SupervisorController::class, 'updatePassword']);
Route::delete('supervisor/{id}', [SupervisorController::class, 'destroy']);

Route::post('users/kalibrasi',  [UserController::class, 'apikalibrasiAbsen']);
Route::resource('users', UserController::class);
Route::post('users/search', [UserController::class, 'search']);
Route::post('users/{id}/changepassword', [UserController::class, 'changePassword']);

Route::apiResource('jeniscuti', JenisCutiController::class);
Route::apiResource('cuti', CutiController::class);
Route::post('cuti/{id}/takeaction', [CutiController::class, 'takeAction']);
Route::get('cuti-user/{userId}', [CutiController::class, 'getByUserId']);

Route::apiResource('roles', RolesController::class);
