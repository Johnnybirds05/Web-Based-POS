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


    //Manage Stocks routes
    Route::resource('/quantity',App\Http\Controllers\admin\QuantityChangeController::class);
    Route::post('/store-quantity', [App\Http\Controllers\admin\QuantityChangeController::class,'store']);
    Route::get('/fetch-products', [App\Http\Controllers\admin\QuantityChangeController::class,'fetchAllProducts']);


});

Route::get('/session', function(){
    return Session::all();
});
