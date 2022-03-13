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
            $sale = new Sale();

            $costprice = Product::where('id', '=', $request->product_id )->first()['costprice'];

            $sale->category_id = Product::find($request->product_id)->category_id;
            $sale->seller = $request->seller;  // it should be the authenticated user Auth::user
            $sale->customerName = $request->customerName;
            // $sale->product_id = $request->product_id;
            $sale->sellingprice = $request->sellingprice;
            $sale->amountPaid = $request->amountPaid;
            $sale->profit = $request->sellingprice - $costprice;
            $sale->date = Carbon::now()->toDateString();
            $sale->save();

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

                // ////////////////////////////   now add the credit amount to the credits in the cash tables ////////////
                // $credit = Cash::first();
                // if ($credit == null) {
                //     $cashOfCredit = new Cash();
                //     $cashOfCredit->totalCreditAmount = $request->sellingprice - $request->amountPaid;
                //     $cashOfCredit->save();
                // } else{
                //     $previousTotalCreditAmount =  DB::table('cashes')->first()->totalCreditAmount;
                //     DB::table('cashes')->update(['totalCreditAmount' => $previousTotalCreditAmount +  $request->sellingprice - $request->amountPaid]);
                // }
         
                // ////////////////////////////////////////// ends here  //////////////////////////////////
                return response()->json([
                    'status' => 200,
                    'message' => 'Creditor added successfully',
                ]);
            } else{
                return [ "amountpaid" => $request->amountPaid, "costprice" => $costprice];
            }



             ///   this shows you the category for which the product belongs to
             ///    reduce the stock quantity by one 
             
             $categoryId = Product::find($request->product_id)->category->id;
             $quantity =  DB::table('stocks')->where('category_id', '=', $categoryId)->first()->quantity;
             if ($quantity == 1) {
                DB::table('stocks')->where('category_id', '=', $categoryId)->update(['status' => 0]);
             } else{
                DB::table('stocks')->where('category_id', '=', $categoryId)->update(['status' => 1]);
             }
             DB::table('stocks')->where('category_id', '=', $categoryId)->update(['quantity' => $quantity - 1]);
            

            /// the total price will be calculated by the system, and the amount paid will be added by the user
    
            
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

    public function productInSale($saleId){
        /////////////////////////////////////// initial //////////////////////////////////
        return response()->json([
            'sales  ' => Sale::find($saleId)->with('products')->get(),
        ]);

        // return response()->json([
        //     'sales  ' => Sale::with('products')->get(),
        
        // ]);
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
