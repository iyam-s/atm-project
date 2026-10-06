<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\http\Controllers\TransaksiController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/ganti-pin', [AuthController::class, 'gantiPin']);

    Route::get('/profile', [AuthController::class, 'profile']);

    Route::get('/saldo/{nomor_rekening}', [TransaksiController::class, 'saldo']);

    Route::post('/setor', [TransaksiController::class, 'setor']);

    Route::post('/tarik', [TransaksiController::class, 'tarik']);

    Route::post('/transfer', [TransaksiController::class, 'transfer']);

    Route::get('/transaksi/{nomor_rekening}', [TransaksiController::class, 'riwayat']);

});