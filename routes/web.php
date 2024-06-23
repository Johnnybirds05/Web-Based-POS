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
    Route::get('/user',[App\Http\Controllers\admin\AdminDashboard::class,'fetchCurrentUser']);
    //Manage Stocks routes
    Route::resource('/transactions',App\Http\Controllers\user\TransactionController::class);
    Route::post('/transactions/{remarks}', [App\Http\Controllers\user\TransactionController::class,'store']);
    Route::post('/transactions/{id}/{remarks}', [App\Http\Controllers\user\TransactionController::class,'update']);
    Route::get('/fetch-remarks', [App\Http\Controllers\user\TransactionController::class,'fetchAllRemarks']);
    Route::get('/fetch-products', [App\Http\Controllers\user\TransactionController::class,'fetchAllProducts']);
});

Route::middleware(['auth','role:ADMIN'])->group(function(){
    Route::get('/dashboard', function () {
        return view('dashboard.dashboard');
    });
    //Manage Users Profile Routes
    Route::resource('/users', App\Http\Controllers\admin\UserController::class);
    Route::post('/user-update', [App\Http\Controllers\admin\UserController::class,'update']);
    Route::post('/change-password', [App\Http\Controllers\admin\UserController::class,'changePassword']);

    //Manage Product information Routes
    Route::resource('/products',App\Http\Controllers\admin\ProductsController::class);
    Route::post('/delete-products', [App\Http\Controllers\admin\ProductsController::class,'destroyMultiple']);
    Route::get('/fetch-users', [App\Http\Controllers\admin\ProductsController::class,'users']);
    Route::get('/product-user/{id}', [App\Http\Controllers\admin\ProductsController::class,'productsbyUser']);
    Route::get('/product-user/{id}/{to}', [App\Http\Controllers\admin\ProductsController::class,'productsbyUserWithRange']);
    Route::get('/product-range/{to}', [App\Http\Controllers\admin\ProductsController::class,'productsWithRange']);

    //Generate Reports Route
    Route::get('/fetch-reports', [App\Http\Controllers\admin\ReportsController::class,'generateReports']);
    Route::get('/fetch-reports-total', [App\Http\Controllers\admin\ReportsController::class,'generateReportsTotal']);
    Route::get('/fetch-reports/{id}', [App\Http\Controllers\admin\ReportsController::class,'generateReportsUser']);
    Route::get('/fetch-reports-total/{id}', [App\Http\Controllers\admin\ReportsController::class,'generateReportsTotalUser']);
    Route::get('/fetch-reports-date/{date}', [App\Http\Controllers\admin\ReportsController::class,'generateReportsDate']);
    Route::get('/fetch-reports-total-date/{date}', [App\Http\Controllers\admin\ReportsController::class,'generateReportsTotalDate']);
    Route::get('/fetch-reports/{id}/{date}', [App\Http\Controllers\admin\ReportsController::class,'generateReportsUserDate']);
    Route::get('/fetch-reports-total/{id}/{date}', [App\Http\Controllers\admin\ReportsController::class,'generateReportsTotalUserDate']);

    //transaction 

});
Route::middleware(['auth','role:USER'])->group(function(){
    Route::get('/dashboard-user', function () {
        return view('dashboard.regular-dashboard');
    });
    //transactions and stocks
    Route::get('/fetch-products-user', [App\Http\Controllers\admin\ProductsController::class,'fetchUserProducts']);
    Route::get('/user-transactions', [App\Http\Controllers\user\TransactionController::class,'fetchAllUserTransactions']);

    //report generation
    Route::get('/fetch-reports-user', [App\Http\Controllers\admin\ReportsController::class,'generateReportsCurrentUser']);
    Route::get('/fetch-reports-total-user', [App\Http\Controllers\admin\ReportsController::class,'generateReportsTotalCurrentUser']);

    Route::get('/fetch-reports-users/{date}', [App\Http\Controllers\admin\ReportsController::class,'generateReportsCurrentUserDate']);
    Route::get('/fetch-reports-total-users/{date}', [App\Http\Controllers\admin\ReportsController::class,'generateReportsTotalCurrentUserDate']);




});
