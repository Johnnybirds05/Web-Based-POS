<?php

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
    //Manage Users Profile Route
    Route::resource('/users', App\Http\Controllers\admin\UserController::class);
    Route::post('/user-update', [App\Http\Controllers\admin\UserController::class,'update']);
    Route::post('/change-password', [App\Http\Controllers\admin\UserController::class,'changePassword']);

    //Manage Product information Route
    Route::resource('/products',App\Http\Controllers\admin\ProductsController::class);

});
