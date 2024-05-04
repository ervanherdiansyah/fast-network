<?php

use App\Http\Controllers\Api\PaketController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserDetailController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

// Produk
Route::get('/produk', [ProductController::class, 'getProduct']);
Route::post('/produk/create', [ProductController::class, 'createProduct']);
Route::get('/produk-byid/{id}', [ProductController::class, 'getProductById']);
Route::put('/produk/update/{id}', [ProductController::class, 'updateProduct']);
Route::delete('/produk/delete/{id}', [ProductController::class, 'deleteProduct']);

// Paket
Route::get('/paket', [PaketController::class, 'getPaket']);
Route::post('/paket/create', [PaketController::class, 'createPaket']);
Route::get('/paket-byid/{id}', [PaketController::class, 'getPaketById']);
Route::put('/paket/update/{id}', [PaketController::class, 'updatePaket']);
Route::delete('/paket/delete/{id}', [PaketController::class, 'deletePaket']);

Route::put('updateUserDetails/{id}', [UserDetailController::class, 'updateUserDetailAdmin']);
Route::get('alluserdetails', [UserDetailController::class, 'getUserDetails']);
