<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\AuthController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route Autentikasi yang Membutuhkan Login
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Grup Route Admin menggunakan Resource
Route::prefix('admin')->group(function () {
    
    // Karena controller kita berbasis API JSON (mengembalikan response()->json), 
    // kita mengecualikan method `create` dan `edit` (yang biasanya untuk view form Blade).
    
    Route::resource('categories', CategoriesController::class)->except(['create', 'edit']);
    
    Route::resource('products', ProductsController::class)->except(['create', 'edit']);
    
    Route::resource('payment-methods', PaymentMethodsController::class)->except(['create', 'edit']);
    
    // Transaksi (CRUD standar + custom route untuk update status pembayaran/proses)
    Route::resource('transactions', TransactionsController::class)->except(['create', 'edit', 'update', 'destroy']);
    Route::put('transactions/{id}/status', [TransactionsController::class, 'updateStatus'])->name('transactions.update-status');

});