<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashController extends Controller
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

            $price = Product::where('id', '=', $request->product_id )->first()['price'];
     
            $sale->seller = $request->seller;  // it should be the authenticated user Auth::user
            $sale->customerName = $request->customerName;
            $sale->product_id = $request->product_id;
            $sale->sellingprice = $request->sellingprice;
            $sale->amountPaid = $request->amountPaid;
            $sale->profit = $request->sellingprice - $price;
            $sale->date = Carbon::now()->toDateString();
            
            

            /// the total price will be calculated by the system, and the amount paid will be added by the user
    
            $sale->save();
            return response()->json([
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
        return response()->json([
            'sales  ' => Sale::find($saleId)->with('products')->get(),
        ]);
    }

    public function edit($id){
        $sale = Sale::find($id);
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
                    'message' => 'Student added successfully',
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
