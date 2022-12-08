<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MyTransactionController;
use App\Http\Controllers\ProductGalleryController;

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

Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/details/{slug}', [FrontendController::class, 'details'])->name('details');
Route::get('/our-products', [FrontendController::class, 'ourProducts'])->name('our-products');

Route::get('/meja', [FrontendController::class, 'meja'])->name('meja');
Route::get('/kursi', [FrontendController::class, 'kursi'])->name('kursi');
Route::get('/sofa', [FrontendController::class, 'sofa'])->name('sofa');
Route::get('/rak', [FrontendController::class, 'rak'])->name('rak');


// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function (){
    Route::get('/cart', [FrontendController::class, 'cart'])->name('cart');
    Route::post('/cart/{id}', [FrontendController::class, 'cartAdd'])->name('cart-add');
    Route::delete('/cart/{id}', [FrontendController::class, 'cartDelete'])->name('cart-delete');
    Route::post('/checkout', [FrontendController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [FrontendController::class, 'success'])->name('checkout-success');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->name('dashboard.')->prefix('dashboard')->group(function (){
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::resource('my-transaction', MyTransactionController::class)->only([
        'index', 'show',
    ]);

    Route::get('/invoice/{id}', [InvoiceController::class, 'invoice'])->name('invoice');

    Route::middleware(['admin'])->group(function (){
        Route::resource('product', ProductController::class);
        Route::resource('product.gallery', ProductGalleryController::class)->shallow()->only([
            'index', 'create', 'store', 'destroy'
        ]);
        Route::resource('reports', ReportsController::class)->shallow()->only([
            'index'
        ]);

        Route::resource('transaction', TransactionController::class)->only([
            'index', 'show', 'edit', 'update'
        ]);
        Route::resource('user', UserController::class)->only([
            'index', 'edit', 'update', 'destroy'
        ]);
    });
});

