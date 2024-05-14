<?php

use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\BalanceWithdrawController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\CheckoutContoller;
use App\Http\Controllers\Api\CitiesController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaketController;
use App\Http\Controllers\Api\PointWithdrawController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProvinsiController;
use App\Http\Controllers\Api\RajaOngkirController;
use App\Http\Controllers\Api\UserDetailController;
use App\Http\Controllers\Api\UserWalletController;
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


    // test middleware member affliasi
    Route::middleware(['check.role:mitra'])->group(function () {

        //test Order
        Route::get('/get-order', [OrderController::class, 'getOrderByUserIdOnOrder']);
        Route::post('/order', [OrderController::class, 'addToOrder']);

        //Ongkir
        Route::post('/rajaongkir', [RajaOngkirController::class, 'getOngkir']);

        //Checkout
        Route::post('/checkout', [CheckoutContoller::class, 'checkout']);

        // Alamat
        Route::get('/useralamat-by-userid', [AlamatController::class, 'getAlamatByUserId']);
        Route::post('/useralamat/create', [AlamatController::class, 'createAlamat']);
        Route::post('/useralamat/update/{id}', [AlamatController::class, 'updateAlamat']);
        Route::delete('/useralamat/delete/{id}', [AlamatController::class, 'deleteAlamat']);

        // User Bank Detail API
        Route::get('userbanks', [BankController::class, 'getBankByUserID']);
        Route::post('userbanks/add', [BankController::class, 'AddBankByUserID']);
        Route::delete('userbanks/delete/{id}', [BankController::class, 'deleteBankByID']);
        Route::post('userbanks/update/{id}', [BankController::class, 'updateBankDataByID']);

        // Produk
        Route::get('/produk', [ProductController::class, 'getProduct']);
        Route::get('/produk-byid/{id}', [ProductController::class, 'getProductById']);

        // Paket
        Route::get('/paket', [PaketController::class, 'getPaket']);
        Route::get('/paket-byid/{id}', [PaketController::class, 'getPaketById']);

        //User Detail
        Route::get('/user-detail/all', [UserDetailController::class, 'getUserDetails']);
        Route::get('/user-detail/by-id', [UserDetailController::class, 'getUserDetailById']);
        Route::post('/user-detail/create', [UserDetailController::class, 'createUserDetail']);
        Route::post('/user-detail/update', [UserDetailController::class, 'updateUserDetail']);

        // Pencairan Balance
        // Ambil Seluruh Permintaan Pencairan Balance Yang Pernah user lakukan
        Route::get('/user-balancewithdrawhistory', [BalanceWithdrawController::class, 'getWithdrawBalanceByUser']);
        // Buat Permintaan Pencairan Balance Yang Baru untuk User
        Route::post('/user-withdrawbalance/new', [BalanceWithdrawController::class, 'createBalanceWithdrawRequest']);

        // Pencairan Poin
        // Ambil Seluruh Permintaan Pencairan Balance Yang Pernah user lakukan
        Route::get('/user-pointwithdrawhistory', [PointWithdrawController::class, 'getWithdrawPointByUser']);
        // Buat Permintaan Pencairan Balance Yang Baru untuk User
        Route::post('/user-withdrawpoint/new', [PointWithdrawController::class, 'createPointWithdrawRequest']);

        // Get Wallet Information
        Route::get('/user-wallet', [UserWalletController::class, 'getUserWallet']);



    });
});

Route::middleware(['role:superadmin'])->group(function () {
    // Produk Admin
    Route::get('/produk', [ProductController::class, 'getProduct']);
    Route::post('/produk/create', [ProductController::class, 'createProduct']);
    Route::get('/produk-byid/{id}', [ProductController::class, 'getProductById']);
    Route::post('/produk/update/{id}', [ProductController::class, 'updateProduct']);
    Route::delete('/produk/delete/{id}', [ProductController::class, 'deleteProduct']);

    // Paket Admin
    Route::get('/paket', [PaketController::class, 'getPaket']);
    Route::post('/paket/create', [PaketController::class, 'createPaket']);
    Route::get('/paket-byid/{id}', [PaketController::class, 'getPaketById']);
    Route::post('/paket/update/{id}', [PaketController::class, 'updatePaket']);
    Route::delete('/paket/delete/{id}', [PaketController::class, 'deletePaket']);
});


//callback payment gateway
Route::post('/callback', [CheckoutContoller::class, 'callback']);


// Get Cities 
Route::get('/cities', [CitiesController::class, 'getAllCities']);
Route::get('/cities/by-id/{id}', [CitiesController::class, 'getCitiesById']);
Route::get('/cities/by-province-id', [CitiesController::class, 'getcitiesByIdProvinsi']);

// Get Provinsi
Route::get('/province', [ProvinsiController::class, 'getAllProvinsi']);
Route::get('/province/by-id/{id}', [ProvinsiController::class, 'getProvinsiById']);




// Produk
// Route::get('/produk', [ProductController::class, 'getProduct']);
// Route::post('/produk/create', [ProductController::class, 'createProduct']);
// Route::get('/produk-byid/{id}', [ProductController::class, 'getProductById']);
// Route::post('/produk/update/{id}', [ProductController::class, 'updateProduct']);
// Route::delete('/produk/delete/{id}', [ProductController::class, 'deleteProduct']);


// Paket
// Route::get('/paket', [PaketController::class, 'getPaket']);
// Route::post('/paket/create', [PaketController::class, 'createPaket']);
// Route::get('/paket-byid/{id}', [PaketController::class, 'getPaketById']);
// Route::post('/paket/update/{id}', [PaketController::class, 'updatePaket']);
// Route::delete('/paket/delete/{id}', [PaketController::class, 'deletePaket']);

// //User Detail
// Route::get('/user-detail/all', [UserDetailController::class, 'getUserDetails']);
// Route::get('/user-detail/by-id/{id}', [UserDetailController::class, 'getUserDetailById']);
// Route::post('/user-detail/create', [UserDetailController::class, 'createUserDetail']);
// Route::post('/user-detail/update/{id}', [UserDetailController::class, 'updateUserDetailAdmin']);
// Route::delete('/user-detail/delete/{id}', [UserDetailController::class, 'deleteUserDetail']);
