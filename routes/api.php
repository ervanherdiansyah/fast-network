<?php

use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\BalanceWithdrawController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\CheckoutContoller;
use App\Http\Controllers\Api\CheckReferral;
use App\Http\Controllers\Api\CitiesController;
use App\Http\Controllers\Api\CourierController;
use App\Http\Controllers\Api\GambarBannerController;
use App\Http\Controllers\Api\GambarInformasiBannerController;
use App\Http\Controllers\Api\InfoBonusController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaketController;
use App\Http\Controllers\Api\PilihanPencairanController;
use App\Http\Controllers\Api\PointWithdrawController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProvinsiController;
use App\Http\Controllers\Api\RajaOngkirController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\TargetBonusController;
use App\Http\Controllers\Api\UserDetailController;
use App\Http\Controllers\Api\UserKomisiHistoryController;
use App\Http\Controllers\Api\UserPoinHistoryController;
use App\Http\Controllers\Api\UserWalletController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
    Route::post('logout', [AuthController::class, 'logout'])->middleware('checkTokenExpiration');
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);


    //middleware member affliasi
    Route::middleware(['checkTokenExpiration', 'check.role:mitra'])->group(function () {

        //Order
        Route::get('/get-order', [OrderController::class, 'getOrderByUserIdOnOrder']);
        Route::get('/get-order/by-user-id', [OrderController::class, 'getOrderByUserID']);
        Route::get('/get-allorder', [OrderController::class, 'getAllOrderByUser']);
        Route::post('/order', [OrderController::class, 'addToOrder']);
        Route::get('/get-order-byuseronafiliasi', [OrderController::class, 'getOrderByUserOnAfiliasi']);
        Route::get('/get-sumorder-onafiliasi', [OrderController::class, 'getSumOrderOnAfiliasiByUser']);
        Route::get('/get-sumorder-allafliasi', [OrderController::class, 'getSumAOrderOnAfiliasiAllUser']);
        Route::get('/get-userorders', [OrderController::class, 'getOrderByUserID2']);

        //Ongkir
        Route::post('/rajaongkir', [RajaOngkirController::class, 'getOngkir']);

        //Checkout
        Route::post('/checkout', [CheckoutContoller::class, 'checkout']);

        // Alamat
        Route::get('/useralamat-by-user', [AlamatController::class, 'getAlamatByUserId']);
        Route::get('/useralamatutama-by-user', [AlamatController::class, 'getAlamatUtamaByUserId']);
        Route::post('/useralamat/create', [AlamatController::class, 'createAlamat']);
        Route::post('/useralamat/update/{id}', [AlamatController::class, 'updateAlamat']);
        Route::delete('/useralamat/delete/{id}', [AlamatController::class, 'deleteAlamat']);

        // User Bank Detail API
        Route::get('userbanks', [BankController::class, 'getBankByUserID']);
        Route::get('userbankutama', [BankController::class, 'getBankUtamaByUserID']);
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
        // Route::get('/user-detail/all', [UserDetailController::class, 'getUserDetails']);

        Route::get('/user-detail/by-id', [UserDetailController::class, 'getUserDetailById']);

        // Route::post('/user-detail/create', [UserDetailController::class, 'createUserDetail']);

        Route::post('/user-detail/update', [UserDetailController::class, 'updateUserDetail']);
        Route::post('/user-detail/delete-profile-picture', [UserDetailController::class, 'deleteProfilePic']);

        //Use Referral By User
        Route::get('/use-referral/byuser', [UserDetailController::class, 'getUseReferralByUser']);
        Route::get('/count-use-referral/byuser', [UserDetailController::class, 'getCountUseReferralByUser']);

        //Change Password
        Route::post('/update-password', [UserDetailController::class, 'updatepassword']);

        //Confirmation Password
        Route::post('/confirmation-password', [UserDetailController::class, 'confirmationpassword']);

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

        // HISTORI BALANCE DAN POIN
        Route::get('/user-komisi-history',[UserKomisiHistoryController::class, 'getKomisiHistory']);
        Route::get('/user-poin-history', [UserPoinHistoryController::class, 'getPointHistory']);
        Route::get('/user-komisi-data', [UserKomisiHistoryController::class, 'getKomisiData']);

        // Get Wallet Information
        Route::get('/user-wallet', [UserWalletController::class, 'getUserWallet']);

        // GET User Notification
        Route::get('/user-notification', [NotificationController::class, 'getUserNotification']);

        // CREATE Notification
        Route::post('/notification-create', [NotificationController::class, 'createNotification']);
    });

    Route::middleware(['checkTokenExpiration'])->get('/check-token-expiration', function (Request $request) {
        // Mendapatkan waktu kedaluwarsa dari atribut request
        $expires_at = $request->attributes->get('expires_at');

        // Konversi waktu ke zona waktu Asia/Jakarta
        $expires_at_indonesia = Carbon::parse($expires_at)->timezone('Asia/Jakarta')->toDateTimeString();

        return response()->json([
            'message' => 'Token is valid',
            'expires_at' => $expires_at_indonesia
        ]);
    });
});


// Produk Admin
Route::get('/product', [ProductController::class, 'getProduct']);
Route::post('/product/create', [ProductController::class, 'createProduct']);
Route::get('/product-byid/{id}', [ProductController::class, 'getProductById']);
Route::post('/product/update/{id}', [ProductController::class, 'updateProduct']);
Route::delete('/product/delete/{id}', [ProductController::class, 'deleteProduct']);

// Paket Admin
Route::get('/get-package', [PaketController::class, 'getPaket']);
Route::get('/package', [PaketController::class, 'getFilterPaket']);
Route::post('/package/create', [PaketController::class, 'createPaket']);
Route::get('/package-byid/{id}', [PaketController::class, 'getPaketById']);
Route::post('/package/update/{id}', [PaketController::class, 'updatePaket']);
Route::delete('/package/delete/{id}', [PaketController::class, 'deletePaket']);

// Gambar Banner
Route::get('/gambar-banner', [GambarBannerController::class, 'getAllBannerImages']);
Route::post('/gambar-banner/create', [GambarBannerController::class, 'createNewBannerImage']);
Route::get('/gambar-banner-byid/{id}', [GambarBannerController::class, 'getBannerImagesBy']);
Route::post('/gambar-banner/update/{id}', [GambarBannerController::class, 'updateBannerImage']);
Route::delete('/gambar-banner/delete/{id}', [GambarBannerController::class, 'deleteGambarBanner']);

// Gambar Informasi Banner
Route::get('/gambar-informasi-banner', [GambarInformasiBannerController::class, 'getAllBannerInformationImages']);
Route::post('/gambar-informasi-banner/create', [GambarInformasiBannerController::class, 'createNewBannerInformationImage']);
Route::get('/gambar-informasi-banner-byid/{id}', [GambarInformasiBannerController::class, 'getBannerInformationImagesByID']);
Route::post('/gambar-informasi-banner/update/{id}', [GambarInformasiBannerController::class, 'updateBannerInformationImage']);
Route::delete('/gambar-informasi-banner/delete/{id}', [GambarInformasiBannerController::class, 'deleteGambarBanner']);

// Route::middleware(['role:superadmin'])->group(function () {
// });getBannerInformationImagesByID


//callback payment gateway
Route::post('/callback', [CheckoutContoller::class, 'callback']);


// Get Cities 
Route::get('/cities', [CitiesController::class, 'getAllCities']);
Route::get('/cities/by-id/{id}', [CitiesController::class, 'getCitiesById']);
Route::get('/cities/by-province-id/{provinsi_id}', [CitiesController::class, 'getcitiesByIdProvinsi']);

//Get Courier
Route::get('/courier', [CourierController::class, 'getAllCourier']);

//Reward
Route::get('/reward', [RewardController::class, 'getReward']);

//Info Bonus
Route::get('/info-bonus', [InfoBonusController::class, 'getInfoBonus']);

//Target Bonus
Route::get('/target-bonus', [TargetBonusController::class, 'getTargetBonus']);


// Get Provinsi
Route::get('/province', [ProvinsiController::class, 'getAllProvinsi']);
Route::get('/province/by-id/{id}', [ProvinsiController::class, 'getProvinsiById']);

// Cek Referral Use
Route::post('/check-referral-user', [CheckReferral::class, 'userReferral']);

// Pilihan Cepat Pencairan Admin
Route::get('/pilihancepat', [PilihanPencairanController::class, 'getPilihanCepatPencairan']);
Route::post('/pilihancepat/create', [PilihanPencairanController::class, 'createPilihanCepatPencairan']);
Route::post('/pilihancepat/update/{id}', [PilihanPencairanController::class, 'updatePilihanCepatPencairan']);
Route::delete('/pilihancepat/delete/{id}', [PilihanPencairanController::class, 'deletePilihanCepatPencairan']);


// Produk
// Route::get('/produk', [ProductController::class, 'getProduct']);
// Route::post('/produk/create', [ProductController::class, 'createProduct']);
// Route::get('/produk-byid/{id}', [ProductController::class, 'getProductById']);
// Route::post('/produk/update/{id}', [ProductController::class, 'updateProduct']);
// Route::delete('/produk/delete/{id}', [ProductController::class, 'deleteProduct']);


// Paket
Route::get('/get-paket-no-authentication', [PaketController::class, 'getPaket']);
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
