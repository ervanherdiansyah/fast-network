<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\PaketController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserDetailController;
use App\Http\Controllers\Authentication\AuthAdminController;
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

Route::get('/', function () {
    return view('welcome');
});
