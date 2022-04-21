<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Creditor;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaleController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            // 'name' => 'required|max:191',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else{
            \Log::info($request);
            $sale = new Sale();
            $sale->seller = $request->seller;  // it should be the authenticated user Auth::user
            $sale->customerName = $request->customerName;
            // $sale->sellingprice = $request->sellingprice;
            $sale->totalSalePrice = $request->totalSalePrice;
            $sale->amountPaid = $request->amountPaid;
            $sale->date = Carbon::now()->toDateString();
            if ($request->totalSalePrice == $request->amountPaid) {
                $sale->status = "complete";
            }else{
                $sale->status = "incomplete";
            }
            switch (JWTAuth::user()->userType) {
                //////////  the case of admin will be remove it is just for testing case //////////
                case 'admin':
                    $sale->category_id = "1";
                    break;
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
            // $sale->save();
            // $sale->products()->attach($request->productsInSale);

            //////////////  update the product quantity after making sales ////////////////////
            /// products id are in the form [1,3,4]
            /// and their respective quantity sold are the form [10, 20, 3]
            // \Log::info( $request->productsInSale);
            // \Log::info( $request->quantityArr);
            $count = $request->productsInSale;
            for ($i=0; $i < count($count); $i++) { 
                
                $productQuantity =  DB::table('products')->where('id', '=', $request->productsInSale[$i])->first()->totalQuantity;
                if ($productQuantity < $request->quantityArr[$i]) {
                    return response()->json([
                        'status' => 204,
                        'message' => 'insufficient products in stock',
                    ]);
                }
                DB::table('products')->where('id', '=', $request->productsInSale[$i])->update(['totalQuantity' => $productQuantity - $request->quantityArr[$i]]);
            }

            $sale->save();
            $sale->products()->attach($request->productsInSale);
            ///////////    now check for the sum of the costprice of the products sold ie $sale //////////
            $salesTotalCostprice = $sale->find($sale->id)->products->sum('costprice');
            $profit = $request->totalSalePrice - $salesTotalCostprice;
            DB::table('sales')->where('id', '=', $sale->id)->update(['profit' => $profit]);
           
            // \Log::info("profit ". $profit." "."totalCost ". $salesTotalCostprice." "."total sales ". $request->totalSalePrice);
            return response()->json([
                'status' => 200,
                'sale_id' => $sale->id,
                'message' => 'sale added successfully',
            ]);
            ///////////////////////   IF THE CUSTOMER DID NOT PAY ALL THEN HE WILL BE UNDER THE CREDITORS //////////////
            if ($request->amountPaid < $costprice ) {
                $creditor = new Creditor();
                $creditor->customername = $request->customerName;
                $creditor->sellername = $request->seller;
                $creditor->totalprice = $request->sellingprice;
                $creditor->amountPaid = $request->amountPaid;
                $creditor->paymentstatus = 0;   ///  0 means not completed and   1 means completely paid
                $creditor->balance = $request->sellingprice - $request->amountPaid;
                $creditor->save();

                  //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
                $previousCashAthand = DB::table('cashes')->first()->cashAthand;
                DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->sellingprice - $request->amountPaid]);
                /////////////////////
         
                // ////////////////////////////////////////// ends here  //////////////////////////////////
                return response()->json([
                    'status' => 200,
                    'message' => 'Creditor added successfully',
                ]);
            } else{
                return [ "amountpaid" => $request->amountPaid, "costprice" => $costprice];
            }
    
            
            return response()->json([
                'categoryOftheProduct' => Product::find($request->product_id)->category,
                'status' => 200,
                'message' => 'Sale added successfully',
            ]);
        }
       
    }

    public function index(){

         $allsales = Sale::all(); 
        return response()->json([
            'status' => 200,
             'allsales' => $allsales->sum('amountpaid'),
            'salesToday' => Sale::where('date', '=', Carbon::now()->toDateString())->get(),
            'totalProfit' => Sale::sum('profit'),
            'totalAmountPaidToday' =>  Sale::where('date', '=', Carbon::now()->toDateString())->sum('amountPaid'),  
            'todaysProfit' => Sale::where('date', '=', Carbon::now()->toDateString())->sum('profit'),
            'noOfSaleToday' => Sale::where('date', '=', Carbon::now()->toDateString())->count(),        
        ]);
    }

    public function allSales(){
        $allsales = Sale::with('products')->get();
       return response()->json([
           'status' => 200,
           'allsales' => $allsales,     
       ]);
   }

    public function mobileSales(Request $request){
            ///////////////////
        if ($request->sort == "-id") {
            $mobileSales = Sale::where('category_id', '=', 3)->with('products')->orderBy('id', 'desc')->paginate(20);
        } else {
            $mobileSales = Sale::where('category_id', '=', 3)->with('products')->paginate(20);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $mobileSales = Sale::where('name', 'LIKE', '%' . $request->name . '%')
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

    public function accessoriesSales(Request $request){
     
        ///////////////////
        if ($request->sort == "-id") {
            $accessoriesSales = Sale::where('category_id', '=', 1)->with('products')->orderBy('id', 'desc')->paginate(20);
        } else {
            $accessoriesSales = Sale::where('category_id', '=', 1)->with('products')->paginate(20);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $accessoriesSales = Sale::where('name', 'LIKE', '%' . $request->name . '%')
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

    public function electronicsSales(Request $request){
             ///////////////////
        if ($request->sort == "-id") {
            $electronicSales = Sale::where('category_id', '=', 2)->with('products')->orderBy('id', 'desc')->paginate(20);
        } else {
            $electronicSales = Sale::where('category_id', '=', 2)->with('products')->paginate(20);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $electronicSales = Sale::where('name', 'LIKE', '%' . $request->name . '%')
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

    

    public function productInSale(Request $request, $id){
        
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

    public function salesByCategory($categoryid){
        return response()->json([
            'salesByCategory' => Sale::where('id', '=', $categoryid)->get(),
        ]);
    }



    public function edit($id){
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

    public function update(Request $request, $id){
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
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "sale id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
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
