<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Creditor;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Cash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'name' => 'required|max:191',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $sale = new Sale();
            $sale->seller = JWTAuth::user()->firstname;  // it should be the authenticated user Auth::user
            $sale->customerName = $request->customerName;
            $sale->totalSalePrice = $request->totalSalePrice;
            $sale->amountPaid = $request->amountPaid;
            $sale->balance = $request->totalSalePrice - $request->amountPaid;
            $sale->date = Carbon::now()->toDateString();
            if ($request->totalSalePrice == $request->amountPaid) {
                $sale->status = "complete";
            } else {
                $sale->status = "incomplete";
            }
            switch (JWTAuth::user()->userType) {
                    //////////  the case of admin will be remove it is just for testing case //////////
                    // case 'admin':
                    //     $sale->category_id = "1";
                    //     break;
                case 'mobileSeller':
                    $sale->category_id = "3";
                    break;
                case 'accessoriesSeller':
                    $sale->category_id = "1";
                    break;
                case 'electronicDeviceSeller':
                    $sale->category_id = "2";
                    break;
                default:
                    # code...
                    break;
            }
            //////////////  update the product quantity after making sales ////////////////////
            /// products id are in the form [1,3,4]
            /// and their respective quantity sold are the form [10, 20, 3]
            // \Log::info( $request->productsInSale);
            // \Log::info( $request->quantityArr);

            //// this first for loop is to check if their are enough quanity to make sales, if not then return from the functions

            $count = $request->productsInSale;
            for ($i = 0; $i < count($count); $i++) {

                $testSellerStockProductQuantity =  DB::table('sellerinventories')->where('product_id', '=', $request->productsInSale[$i])->where('user_id', '=', JWTAuth::user()->id)->first();
                // \Log::info( $testSellerStockProductQuantity == null);
                if ($testSellerStockProductQuantity == null) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'product not for you',
                    ]);
                    break;
                }
                $sellerStockProductQuantity =  DB::table('sellerinventories')->where('product_id', '=', $request->productsInSale[$i])->where('user_id', '=', JWTAuth::user()->id)->first()->sellerStockQuantity;


                if ($sellerStockProductQuantity < $request->quantityArr[$i]) {
                    return response()->json([
                        'status' => 204,
                        'message' => 'insufficient products in seller stock',
                    ]);
                }
                // the product quantity of the seller should reduced and not the main stock product quantity
                //DB::table('products')->where('id', '=', $request->productsInSale[$i])->update(['totalQuantity' => $productQuantity - $request->quantityArr[$i]]); // this was the stopping point before 
                DB::table('sellerinventories')->where('product_id', '=', $request->productsInSale[$i])->where('user_id', '=', JWTAuth::user()->id)->update(['sellerStockQuantity' => $sellerStockProductQuantity - $request->quantityArr[$i]]);
            }

            $sale->save();
            $sale->products()->attach($request->productsInSale);
            ///////////    now check for the sum of the costprice of the products sold ie $sale //////////
            $salesTotalCostprice = $sale->find($sale->id)->products->sum('costprice');
            $profit = $request->totalSalePrice - $salesTotalCostprice;
            DB::table('sales')->where('id', '=', $sale->id)->update(['profit' => $profit]);

            ////////////////////////   this the logic to add the amount paid to the current balance//////////////////
            $previousCurrentBalance = DB::table('cashes')->first();
            if ($previousCurrentBalance == null) {
                $cash = new Cash();
                $cash->currentBalance = $request->amountPaid;
                $cash->save();
            } else {
                $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
                DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance + $request->amountPaid]);
            }

            /////////////////////////  WANT TO ADD TO THE PAYMENT TABLES SOME OF THE SALES VALUES ///////////
            $payment = new Payment();
            $payment->sale_id = $sale->id;
            $payment->amount = $request->amountPaid;
            $payment->description = "None";
            $payment->balance = $request->totalSalePrice - $request->amountPaid;
            $payment->save();


            return response()->json([
                'status' => 200,
                'sale_id' => $sale->id,
                'message' => 'sale added successfully',
            ]);
        }
    }

    public function addPayment(Request $request, $saleId)
    {
        $status =  DB::table('sales')->where('id', '=', $saleId)->first()->status;
        if ($status == 'complete') {
            return response()->json([
                'status' => 204,
                'result' => "payment completed",
            ]);
        }
        /////// update the amount paid ie the previous amount + the new added payment
        $amountpaid =  DB::table('sales')->where('id', '=', $saleId)->first()->amountpaid;
        $newTotalAmountPaid = $amountpaid + $request->amountToAdd;
        DB::table('sales')->where('id', '=', $saleId)->update(['amountpaid' => $newTotalAmountPaid]);
        ///// update the balance that is the previous blance - the new amountpaid
        $balance =  DB::table('sales')->where('id', '=', $saleId)->first()->balance;
        $newBalance = $balance - $request->amountToAdd;
        DB::table('sales')->where('id', '=', $saleId)->update(['balance' => $newBalance]);
        /////////////// update the status to complete 
        $totalSalePrice =  DB::table('sales')->where('id', '=', $saleId)->first()->totalSalePrice;
        if ($newTotalAmountPaid == $totalSalePrice) {
            DB::table('sales')->where('id', '=', $saleId)->update(['status' => 'complete']);
        }

        ////////////////  add this payment to payment table  //////////////////////////////
        $payment = new Payment();
        $payment->sale_id = $saleId;
        $payment->description = $request->description;
        $payment->balance = $newBalance;
        $payment->amount = $request->amountToAdd;
        $payment->save();
        ////////////////////////   this the logic to add the amount paid to the current balance //////////////////
        $previousCurrentBalance = DB::table('cashes')->first();
        if ($previousCurrentBalance == null) {
            $cash = new Cash();
            $cash->currentBalance = $request->amountToAdd;
            $cash->save();
        } else {
            $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
            DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance + $request->amountToAdd]);
        }
        /////////////////////  the logic ends here  ////////////////////////
        return response()->json([
            'status' => 200,
            'result' => $newTotalAmountPaid,
        ]);
    }

    public function creditorsCount()
    {
        $creditors = Sale::where('status', '=', 'incomplete')->get();
        return response()->json([
            'status' => 422,
            'creditorsCount' => Sale::where('status', '=', 'incomplete')->count(),
            'totalCredits' => $creditors->sum('balance'),
        ]);
    }

    public function currentBalanceAndExpenditures()
    {
        return response()->json([
            'status' => 422,
            'currentBalance' => Cash::sum('currentBalance'),
            'totalExpenses' => Cash::sum('totalExpense'),
        ]);
    }

    public function index()
    {

        $allsales = Sale::all();
        return response()->json([
            'status' => 200,
            'allsales' => $allsales->sum('amountpaid'),
            'totalGrandSales' => $allsales->sum('totalSalePrice'),
            'totalAmountPaidToday' => Sale::where('date', '=', Carbon::now()->toDateString())->get()->sum('amountpaid'),
            'salesToday' => Sale::with('products')->where('date', '=', Carbon::now()->toDateString())->get(),
            'totalProfit' => Sale::sum('profit'),
            'totalAmountPaidToday' =>  Sale::where('date', '=', Carbon::now()->toDateString())->sum('amountPaid'),
            'todaysProfit' => Sale::where('date', '=', Carbon::now()->toDateString())->sum('profit'),
            'noOfSaleToday' => Sale::where('date', '=', Carbon::now()->toDateString())->count(),
        ]);
    }
    public function creditorsDetailInfo()
    {

        return response()->json([
            'status' => 200,
            'totalAmountPaidToday' => Sale::where('date', '=', Carbon::now()->toDateString())->where('status', '=', 'incomplete')->get()->sum('amountpaid'),
            'noOfCreditorsToday' => Sale::where('date', '=', Carbon::now()->toDateString())->where('status', '=', 'incomplete')->count(),
            'salesToday' => Sale::with('products')->where('date', '=', Carbon::now()->toDateString())->where('status', '=', 'incomplete')->get(),
            'totalAmountPaidToday' =>  Sale::where('date', '=', Carbon::now()->toDateString())->where('status', '=', 'incomplete')->sum('amountPaid'),
        ]);
    }

    public function allSales(Request $request)
    {
        if ($request->sort == "-id") {
            $allsales = Sale::with('products', 'payments')->orderBy('id', 'desc')->paginate(20);
        } else {
            $allsales = Sale::with('products', 'payments')->paginate(20);
        }

        if ($request->customername) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $allsales = Sale::where('customername', 'LIKE', '%' . $request->customername . '%')
                ->with(
                    'products',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $allsales->total(),
                'per_page' => $allsales->perPage(),
                'current_page' => $allsales->currentPage(),
                'last_page' => $allsales->lastPage(),
                'from' => $allsales->firstItem(),
                'to' => $allsales->lastItem()
            ],
            'data' => $allsales
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function creditors(Request $request)
    {
        if ($request->sort == "-id") {
            $creditors = Sale::where('status', '=', 'incomplete')->with('products')->orderBy('id', 'desc')->paginate(20);
        } else {
            $creditors = Sale::where('status', '=', 'incomplete')->with('products')->paginate(20);
        }

        if ($request->customername) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $creditors = Sale::where('customername', 'LIKE', '%' . $request->customername . '%')
                ->with(
                    'products',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $creditors->total(),
                'per_page' => $creditors->perPage(),
                'current_page' => $creditors->currentPage(),
                'last_page' => $creditors->lastPage(),
                'from' => $creditors->firstItem(),
                'to' => $creditors->lastItem()
            ],
            'data' => $creditors
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }
    public function mobileSales(Request $request)
    {
        ///////////////////
        if ($request->sort == "-id") {
            $mobileSales = Sale::where('category_id', '=', 3)->with('products', 'payments')->orderBy('id', 'desc')->paginate(20);
        } else {
            $mobileSales = Sale::where('category_id', '=', 3)->with('products', 'payments')->paginate(20);
        }

        if ($request->customername) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $mobileSales = Sale::where('customername', 'LIKE', '%' . $request->customername . '%')
                ->with(
                    'products',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $mobileSales->total(),
                'per_page' => $mobileSales->perPage(),
                'current_page' => $mobileSales->currentPage(),
                'last_page' => $mobileSales->lastPage(),
                'from' => $mobileSales->firstItem(),
                'to' => $mobileSales->lastItem()
            ],
            'data' => $mobileSales
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function accessoriesSales(Request $request)
    {

        ///////////////////
        if ($request->sort == "-id") {
            $accessoriesSales = Sale::where('category_id', '=', 1)->with('products', 'payments')->orderBy('id', 'desc')->paginate(20);
        } else {
            $accessoriesSales = Sale::where('category_id', '=', 1)->with('products', 'payments')->paginate(20);
        }

        if ($request->customername) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $accessoriesSales = Sale::where('customername', 'LIKE', '%' . $request->customername . '%')
                ->with(
                    'products',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $accessoriesSales->total(),
                'per_page' => $accessoriesSales->perPage(),
                'current_page' => $accessoriesSales->currentPage(),
                'last_page' => $accessoriesSales->lastPage(),
                'from' => $accessoriesSales->firstItem(),
                'to' => $accessoriesSales->lastItem()
            ],
            'data' => $accessoriesSales
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function electronicsSales(Request $request)
    {
        ///////////////////
        if ($request->sort == "-id") {
            $electronicSales = Sale::where('category_id', '=', 2)->with('products', 'payments')->orderBy('id', 'desc')->paginate(20);
        } else {
            $electronicSales = Sale::where('category_id', '=', 2)->with('products', 'payments')->paginate(20);
        }

        if ($request->customername) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $electronicSales = Sale::where('customername', 'LIKE', '%' . $request->customername . '%')
                ->with(
                    'products',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $electronicSales->total(),
                'per_page' => $electronicSales->perPage(),
                'current_page' => $electronicSales->currentPage(),
                'last_page' => $electronicSales->lastPage(),
                'from' => $electronicSales->firstItem(),
                'to' => $electronicSales->lastItem()
            ],
            'data' => $electronicSales
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }



    public function productInSale(Request $request, $id)
    {

        if ($request->sort == "-id") {
            $sales = Sale::where('id', $id)->first()->products()->orderBy('id', 'desc')->paginate(20);
        } else {
            $sales = Sale::where('id', $id)->first()->products()->paginate(20);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $sales = Sale::where('name', 'LIKE', '%' . $request->name . '%')
                ->with(
                    'products',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $sales->total(),
                'per_page' => $sales->perPage(),
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'from' => $sales->firstItem(),
                'to' => $sales->lastItem()
            ],
            'data' =>  $sales,
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function salesByCategory($categoryid)
    {
        return response()->json([
            'salesByCategory' => Sale::where('id', '=', $categoryid)->get(),
        ]);
    }
    public function topMobileSales()
    {
        return response()->json([
            'topMobileSales' => Sale::where('category_id', '=', 3)->orderBy('totalSalePrice', 'desc')->take(5)->get(),
        ]);
    }
    public function topElectronicSales()
    {
        return response()->json([
            'topElectronicSales' => Sale::where('category_id', '=', 2)->orderBy('totalSalePrice', 'desc')->take(5)->get(),
        ]);
    }
    public function topAccessoriesSales()
    {
        return response()->json([
            'topAccessoriesSales' => Sale::where('category_id', '=', 1)->orderBy('totalSalePrice', 'desc')->take(5)->get(),
        ]);
    }



    public function edit($id)
    {
        $sale = Sale::find($id)->with('products')->get();
        if ($sale) {
            return response()->json([
                'status' => 200,
                'sale' => $sale,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no sale found',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $sale = Sale::find($id);
            if ($sale) {
                $sale->name = $request->name;
                $sale->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Sale updated successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'messages' => "sale id not found",
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $sale->delete();
            return response()->json([
                'status' => 200,
                'message' => 'sale deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no sale found',
            ]);
        }
    }
}
