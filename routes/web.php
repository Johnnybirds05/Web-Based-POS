<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Session\Session;

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

Route::get('/', function () { return view('auth.login');})->name('login');

Route::post('/login',[App\Http\Controllers\auth\LoginController::class,'login']);
Route::get('/logout',[App\Http\Controllers\auth\LoginController::class,'logout']);

Route::middleware(['auth'])->group(function(){

    Route::get('/dashboard', function () {
        return view('dashboard.dashboard');
    });
    Route::get('/user',[App\Http\Controllers\admin\AdminDashboard::class,'fetchCurrentUser']);
});

Route::middleware(['auth','role:ADMIN'])->group(function(){
    //Manage Users Profile Routes
    Route::resource('/users', App\Http\Controllers\admin\UserController::class);
    Route::post('/user-update', [App\Http\Controllers\admin\UserController::class,'update']);
    Route::post('/change-password', [App\Http\Controllers\admin\UserController::class,'changePassword']);

    //Manage Product information Routes
    Route::resource('/products',App\Http\Controllers\admin\ProductsController::class);
    Route::post('/delete-products', [App\Http\Controllers\admin\ProductsController::class,'destroyMultiple']);
    Route::get('/fetch-users', [App\Http\Controllers\admin\ProductsController::class,'users']);
    Route::get('/product-user/{id}', [App\Http\Controllers\admin\ProductsController::class,'productsbyUser']);
    Route::get('/product-user/{id}/{from}/{to}', [App\Http\Controllers\admin\ProductsController::class,'productsbyUserWithRange']);
    Route::get('/product-range/{from}/{to}', [App\Http\Controllers\admin\ProductsController::class,'productsWithRange']);

    //Manage Stocks routes
    Route::resource('/transactions',App\Http\Controllers\user\TransactionController::class);
    Route::post('/transactions/{remarks}', [App\Http\Controllers\user\TransactionController::class,'store']);
    Route::post('/transactions/{id}/{remarks}', [App\Http\Controllers\user\TransactionController::class,'update']);
    Route::get('/fetch-products', [App\Http\Controllers\user\TransactionController::class,'fetchAllProducts']);
    Route::get('/fetch-remarks', [App\Http\Controllers\user\TransactionController::class,'fetchAllRemarks']);


});
