<?php

use App\Http\Controllers\API\BorrowerController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CreditorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DepositorController;
use App\Http\Controllers\API\ExpenditureController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\SalaryController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserTypeController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SellerInventoryController;
use App\Http\Controllers\API\BorrowertransactionController;
use App\Http\Controllers\API\CashExpenditureController;
use App\Http\Controllers\API\ShopExpenditureController;
use App\Models\Usertype;

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
    Route::get('info', [AuthController::class, 'info']);
    // Route::post('refresh', 'AuthController@refresh');
    Route::post('me', [AuthController::class, 'me']);
});
// JWTAuth::user()->userType;
// $this->middleware(['auth:api', 'seller'], ['except' => ['login', 'register']]);




Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['middleware' => 'seller'], function () {
        ////////////////////////////////        Product      ///////////////////////////////
        Route::post('store-product', [ProductController::class, 'store']);
        Route::get('view-products', [ProductController::class, 'index']);
        Route::delete('delete-product/{id}', [ProductController::class, 'destroy']);
        Route::get('edit-product/{id}', [ProductController::class, 'edit']);
        Route::post('update-product/{id}', [ProductController::class, 'update']);
        Route::post('append-product-stock-quantity/{id}', [ProductController::class, 'appendStockToProduct']);
        Route::get('stock-count', [ProductController::class, 'stockCount']);


        ////////////////////////////////        Category      ///////////////////////////////
        Route::post('store-category', [CategoryController::class, 'store']);
        Route::get('view-categories', [CategoryController::class, 'index']);
        Route::delete('delete-category/{id}', [CategoryController::class, 'destroy']);
        Route::get('edit-category/{id}', [CategoryController::class, 'edit']);
        Route::post('update-category/{id}', [CategoryController::class, 'update']);


        ////////////////////////////////        Brand      ///////////////////////////////
        Route::post('store-brand', [BrandController::class, 'store']);
        Route::get('view-brands', [BrandController::class, 'index']);
        Route::delete('delete-brand/{id}', [BrandController::class, 'destroy']);
        Route::get('edit-brand/{id}', [BrandController::class, 'edit']);
        Route::post('update-brand/{id}', [BrandController::class, 'update']);


        ////////////////////////////////        Stock      ///////////////////////////////
        Route::post('store-stock', [StockController::class, 'store']);
        Route::get('view-stocks', [StockController::class, 'index']);
        Route::delete('delete-stock/{id}', [StockController::class, 'destroy']);
        Route::get('edit-stock/{id}', [StockController::class, 'edit']);
        Route::post('update-stock/{id}', [StockController::class, 'update']);
    });

    Route::group(['middleware' => 'superAdminMiddleWare'], function () {
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
        Route::get('borrowers-transactions/{id}', [BorrowerController::class, 'transactionsOfBorrower']);
        Route::post('store-borrower-transaction', [BorrowertransactionController::class, 'storeBorrowerTransaction']);
        Route::get('borrowers-count', [BorrowerController::class, 'borrowersCount']);


        ////////////////////////////////        Depositor      ///////////////////////////////
        Route::post('store-depositor', [DepositorController::class, 'store']);
        Route::get('view-depositors', [DepositorController::class, 'index']);
        Route::delete('delete-depositor/{id}', [DepositorController::class, 'destroy']);
        Route::get('transactions-of-depositor/{id}', [DepositorController::class, 'transactionsOfdepositor']);
        Route::get('edit-depositor/{id}', [DepositorController::class, 'edit']);
        Route::post('update-depositor/{id}', [DepositorController::class, 'update']);
        Route::get('depositor-count', [DepositorController::class, 'depositorCount']);
        

        ////////////////////////////////        Sales      ///////////////////////////////
        Route::post('store-sale', [SaleController::class, 'store']);
        Route::get('view-sales', [SaleController::class, 'index']);
        Route::get('view-all-sales', [SaleController::class, 'allSales']); //////// might be deleted
        Route::get('view-all-creditors', [SaleController::class, 'creditors']); //////// might be deleted
        Route::get('view-detail-creditors-info', [SaleController::class, 'creditorsDetailInfo']); //////// might be deleted
        Route::get('view-electronic-sales', [SaleController::class, 'electronicsSales']);
        Route::get('view-mobile-sales', [SaleController::class, 'mobileSales']);
        Route::get('view-accessories-sales', [SaleController::class, 'accessoriesSales']);
        Route::delete('delete-sale/{id}', [SaleController::class, 'destroy']);
        Route::get('edit-sale/{id}', [SaleController::class, 'edit']);
        Route::post('update-sale/{id}', [SaleController::class, 'update']);
        Route::get('view-productsInSale/{SaleId}', [SaleController::class, 'productInSale']);
        Route::post('add-payment/{SaleId}', [SaleController::class, 'addPayment']);
        Route::get('top-five-mobile-sales', [SaleController::class, 'topMobileSales']);
        Route::get('top-five-electronic-sales', [SaleController::class, 'topElectronicSales']);
        Route::get('top-five-accessories-sales', [SaleController::class, 'topAccessoriesSales']);
        Route::get('creditors-count', [SaleController::class, 'creditorsCount']);
        

        ////////////////////////////////        Creditors      ///////////////////////////////
        Route::post('store-creditor/{id}', [CreditorController::class, 'store']);
        Route::get('view-creditors', [CreditorController::class, 'index']);
        Route::delete('delete-creditor/{id}', [CreditorController::class, 'destroy']);
        Route::get('edit-creditor/{id}', [CreditorController::class, 'edit']);
        Route::post('update-creditor/{id}', [CreditorController::class, 'update']);

        ////////////////////////////////        Payments      ///////////////////////////////
        Route::get('view-payments/{saleId}', [PaymentController::class, 'index']);
        Route::delete('delete-payments/{id}', [PaymentController::class, 'destroy']);
        Route::get('edit-payments/{id}', [PaymentController::class, 'edit']);

        ////////////////////////////////        Users      ///////////////////////////////
        Route::get('view-users', [UserController::class, 'index']);
        Route::delete('delete-users/{id}', [UserController::class, 'destroy']);
        Route::get('edit-users/{id}', [UserController::class, 'edit']);
        Route::get('user-based-on-category/{id}', [UserController::class, 'usersBasedOnCategory']);
        Route::get('users-count', [UserController::class, 'usersCount']);


        ////////////////////////////////        SellerInventory      ///////////////////////////////
        Route::post('store-sellerInventory/{productId}', [SellerInventoryController::class, 'store']);
        Route::post('update-sellerStock-quantity/{productId}', [SellerInventoryController::class, 'updateSellerStockQuantity']);
        Route::get('view-sellerInventory/{productId}', [SellerInventoryController::class, 'index']);
        Route::delete('delete-sellerInventory/{id}', [SellerInventoryController::class, 'destroy']);
        Route::get('edit-sellerInventory/{id}', [SellerInventoryController::class, 'edit']);


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

        ////////////////////////////////        CashExpenditure      ///////////////////////////////
        Route::post('store-cash-expenditure', [CashExpenditureController::class, 'store']);
        Route::get('view-cash-expenditure', [CashExpenditureController::class, 'index']);
        Route::delete('delete-cash-expenditure/{id}', [CashExpenditureController::class, 'destroy']);
        Route::get('edit-cash-expenditure/{id}', [CashExpenditureController::class, 'edit']);
        Route::post('update-cash-expenditure/{id}', [CashExpenditureController::class, 'update']);

        ////////////////////////////////        ShopExpenditure      ///////////////////////////////
        Route::post('store-shop-expenditure', [ShopExpenditureController::class, 'store']);
        Route::get('view-shop-expenditure', [ShopExpenditureController::class, 'index']);
        Route::delete('delete-shop-expenditure/{id}', [ShopExpenditureController::class, 'destroy']);
        Route::get('edit-shop-expenditure/{id}', [ShopExpenditureController::class, 'edit']);
        Route::post('update-shop-expenditure/{id}', [ShopExpenditureController::class, 'update']);

        ////////////////////////////////        UserType      ///////////////////////////////
        Route::get('user-types', [UserTypeController::class, 'index']);
    });
});
