<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id'=>'required|max:190',
            'name' => 'required|max:191',
            'costprice' => 'required|max:191',
            'quantity' => 'required|max:190',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else{
            $stock = new Stock();
            $stock->name = $request->name;
            $stock->category_id = $request->category_id;
            $stock->costprice = $request->costprice;
            $stock->quantity = $request->quantity;
            $stock->save();
            return response()->json([
                'status' => 200,
                'message' => 'Student added successfully',
            ]);
        }
       
    }

    public function index(){
        $stock = Stock::all();
        return response()->json([
            'status' => 200,
            'result' => $stock,           
        ]);
    }

    public function edit($id){
        $stock = Stock::find($id);
        if ($stock) {
            return response()->json([
                'status' => 200,
                'stock' => $stock,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no stock found',
            ]);
        }
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'category_id'=>'required|max:190',
            'name' => 'required|max:191',
            'costprice' => 'required|max:191',
            'quantity' => 'required|max:190',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $stock = Stock::find($id);
            if ($stock) {
                $stock->name = $request->name;
                $stock->category_id = $request->category_id;
                $stock->costprice = $request->brand_id;
                $stock->quantity = $request->quantity;
                $stock->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Student added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "stock id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $stock = Stock::find($id);
        if ($stock) {
            $stock->delete();
            return response()->json([
                'status' => 200,
                'message' => 'stock deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no stock found',
            ]);
        }
    }

}
