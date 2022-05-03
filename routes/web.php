<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\CashRegisterController::class, 'home'])->name('home');
Route::post('scan-start', [\App\Http\Controllers\CashRegisterController::class, 'startScanning'])->name('scan-start');

Route::get('scan', [\App\Http\Controllers\CashRegisterController::class, 'scan'])->name('scan');
Route::post('scan-product', [\App\Http\Controllers\CashRegisterController::class, 'scanProduct'])->name('scan-product');
Route::post('scan-end', [\App\Http\Controllers\CashRegisterController::class, 'endScanning'])->name('scan-end');

Route::get('scan-details/{scan_token}', [\App\Http\Controllers\CashRegisterController::class, 'scanDetails'])->name('scan-details');
