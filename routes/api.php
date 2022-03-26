<?php

use App\Http\Controllers\API\BorrowerController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CreditorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DepositorController;
use App\Http\Controllers\API\ExpenditureController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SalaryController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


/////// note the admin and superadmin in will be in all middleware

Route::group([

    // 'middleware' => 'api',
    // 'prefix' => 'auth'

], function ($router) {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    // Route::post('refresh', 'AuthController@refresh');
    Route::post('me', [AuthController::class, 'me']);

});
// JWTAuth::user()->userType;
// $this->middleware(['auth:api', 'seller'], ['except' => ['login', 'register']]);




Route::group(['middleware' => 'auth:api'], function(){

    Route::group(['middleware' => 'seller'], function(){
        ////////////////////////////////        Product      ///////////////////////////////
    Route::post('store-product', [ProductController::class, 'store']);
    Route::get('view-products', [ProductController::class, 'index']);
    Route::delete('delete-product/{id}', [ProductController::class, 'destroy']);
    Route::get('edit-product/{id}', [ProductController::class, 'edit']);
    Route::post('update-product/{id}', [ProductController::class, 'update']);
    

        ////////////////////////////////        Category      ///////////////////////////////
    Route::post('store-category', [CategoryController::class, 'store']);
    Route::get('view-categories', [CategoryController::class, 'index']);
    Route::delete('delete-category/{id}', [CategoryController::class, 'destroy']);
    Route::get('edit-category/{id}', [CategoryController::class, 'edit']);
    Route::post('update-category/{id}', [CategoryController::class, 'update']);
    Route::get('view-productsInCategory/{CategoryId}', [CategoryController::class, 'productInCategory']);

    ////////////////////////////////        Stock      ///////////////////////////////
    Route::post('store-stock', [StockController::class, 'store']);
    Route::get('view-stocks', [StockController::class, 'index']);
    Route::delete('delete-stock/{id}', [StockController::class, 'destroy']);
    Route::get('edit-stock/{id}', [StockController::class, 'edit']);
    Route::post('update-stock/{id}', [StockController::class, 'update']);
    });

    Route::group(['middleware' => 'superAdminMiddleWare'], function(){
                ////////////////////////////////        Staff      ///////////////////////////////
    Route::post('store-staff', [StaffController::class, 'store']);
    Route::get('view-staffs', [StaffController::class, 'index']);
    Route::delete('delete-staff/{id}', [StaffController::class, 'destroy']);
    Route::get('edit-staff/{id}', [StaffController::class, 'edit']);
    Route::post('update-staff/{id}', [StaffController::class, 'update']);



    ////////////////////////////////        Borrower      ///////////////////////////////
    Route::post('store-borrower', [BorrowerController::class, 'store']);
    Route::get('view-borrowers', [BorrowerController::class, 'index']);
    Route::delete('delete-borrower/{id}', [BorrowerController::class, 'destroy']);
    Route::post('pay-borrowed-amount/{id}', [BorrowerController::class, 'payBorrowedAmount']);
    Route::get('edit-borrower/{id}', [BorrowerController::class, 'edit']);
    Route::post('update-borrower/{id}', [BorrowerController::class, 'update']);


    ////////////////////////////////        Depositor      ///////////////////////////////
    Route::post('store-depositor', [DepositorController::class, 'store']);
    Route::get('view-depositors', [DepositorController::class, 'index']);
    Route::delete('delete-depositor/{id}', [DepositorController::class, 'destroy']);
    Route::get('transactions-of-depositor/{id}', [DepositorController::class, 'transactionsOfdepositor']);
    Route::get('edit-depositor/{id}', [DepositorController::class, 'edit']);
    Route::post('update-depositor/{id}', [DepositorController::class, 'update']);

    ////////////////////////////////        Sales      ///////////////////////////////
    Route::post('store-sale', [SaleController::class, 'store']);
    Route::get('view-sales', [SaleController::class, 'index']); //////// might be deleted
    Route::delete('delete-sale/{id}', [SaleController::class, 'destroy']);
    Route::get('edit-sale/{id}', [SaleController::class, 'edit']);
    Route::post('update-sale/{id}', [SaleController::class, 'update']);
    Route::get('view-productsInSale/{SaleId}', [SaleController::class, 'productInSale']);

    ////////////////////////////////        Creditors      ///////////////////////////////
    Route::post('store-creditor/{id}', [CreditorController::class, 'store']);
    Route::get('view-creditors', [CreditorController::class, 'index']);
    Route::delete('delete-creditor/{id}', [CreditorController::class, 'destroy']);
    Route::get('edit-creditor/{id}', [CreditorController::class, 'edit']);
    Route::post('update-creditor/{id}', [CreditorController::class, 'update']);


    ////////////////////////////////        Transaction      ///////////////////////////////
    Route::post('store-transaction', [TransactionController::class, 'store']);
    Route::get('view-transaction', [TransactionController::class, 'index']);
    Route::delete('delete-transaction/{id}', [TransactionController::class, 'destroy']);
    Route::get('edit-transaction/{id}', [TransactionController::class, 'edit']);
    Route::post('update-transaction/{id}', [TransactionController::class, 'update']);

    ////////////////////////////////        Salary      ///////////////////////////////
    Route::post('store-salary', [SalaryController::class, 'store']);
    Route::get('view-salary', [SalaryController::class, 'index']);
    Route::delete('delete-salary/{id}', [SalaryController::class, 'destroy']);
    Route::get('edit-salary/{id}', [SalaryController::class, 'edit']);
    Route::post('update-salary/{id}', [SalaryController::class, 'update']);

    ////////////////////////////////        Expenditure      ///////////////////////////////
    Route::post('store-expenditure', [ExpenditureController::class, 'store']);
    Route::get('view-expenditure', [ExpenditureController::class, 'index']);
    Route::delete('delete-expenditure/{id}', [ExpenditureController::class, 'destroy']);
    Route::get('edit-expenditure/{id}', [ExpenditureController::class, 'edit']);
    Route::post('update-expenditure/{id}', [ExpenditureController::class, 'update']);
    });


});




