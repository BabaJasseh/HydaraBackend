<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sellerinventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class SellerInventoryController extends Controller
{
    
    public function store(Request $request, $productId){
        /// note that if the seller is not in the inventory create a new seller inventory
        /// otherwise update the seller inventory
        /// make the update based on the user_id and that is the seller
        //// username should be user_id
            $sellerInventory = new Sellerinventory();
            $sellerInventory->user_id = $request->user_id;
            $sellerInventory->product_id = $productId;
            $sellerInventory->sellerStockQuantity = $request->quantityToAdd;
            $sellerName =  DB::table('users')->where('id', '=', $sellerInventory->user_id)->first()->firstname;
            $sellerInventory->sellername = $sellerName;
            $mainStockQuantity =  DB::table('products')->where('id', '=', $productId)->first()->totalQuantity;
            DB::table('products')->where('id', '=', $productId)->update(['totalQuantity' => $mainStockQuantity -  $request->quantityToAdd]);
            $sellerInventory->save();
            return response()->json([
                'status' => 200,
                'result' => "product Seller successfuly",
            ]);   
    }

    public function updateSellerStockQuantity(Request $request, $productId){
        $sellerStockQuantity =  DB::table('sellerinventories')->where('user_id', '=', $request->user_id)->first()->sellerStockQuantity;
        $mainStockQuantity =  DB::table('products')->where('id', '=', $productId)->first()->totalQuantity;
        if ($request->quantityToAddOrRemove > $mainStockQuantity) {
            return response()->json([
                'status' => 204,
                'message' => 'insufficient products in seller stock',
            ]);
        }
        DB::table('sellerinventories')->where('user_id', '=', $request->user_id)->update(['sellerStockQuantity' => $sellerStockQuantity + $request->quantityToAddOrRemove]);
        DB::table('products')->where('id', '=', $productId)->update(['totalQuantity' => $mainStockQuantity - $request->quantityToAddOrRemove]);
        return response()->json([
            'status' => 200,
            'result' => "updated successfuly",
        ]);
    }

    public function index(Request $request, $productId)
    {
        // $product = Product::with('category', 'Sellerinventory', 'stock')->get();
        // return $request;
        if ($request->sort == "-id") {
            $Sellerinventory = Sellerinventory::where('product_id','=', $productId)->orderBy('id', 'desc')->paginate(20);
        } else {
            $Sellerinventory = Sellerinventory::where('product_id', '=' ,$productId)->paginate(20);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $Sellerinventory = Sellerinventory::where('name', 'LIKE', '%' . $request->name . '%')
                ->with(
                    'products',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $Sellerinventory->total(),
                'per_page' => $Sellerinventory->perPage(),
                'current_page' => $Sellerinventory->currentPage(),
                'last_page' => $Sellerinventory->lastPage(),
                'from' => $Sellerinventory->firstItem(),
                'to' => $Sellerinventory->lastItem()
            ],
            'data' => $Sellerinventory
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }


    public function edit($id){
        $Sellerinventory = Sellerinventory::find($id);
        if ($Sellerinventory) {
            return response()->json([
                'status' => 200,
                'Sellerinventory' => $Sellerinventory,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no Sellerinventory found',
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
            $Sellerinventory = Sellerinventory::find($id);
            if ($Sellerinventory) {
                $Sellerinventory->name = $request->name;
                $Sellerinventory->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Sellerinventory
                     added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "Sellerinventory id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $Sellerinventory = Sellerinventory::find($id);
        if ($Sellerinventory) {
            $Sellerinventory->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Sellerinventory deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no Sellerinventory found',
            ]);
        }
    }

}
