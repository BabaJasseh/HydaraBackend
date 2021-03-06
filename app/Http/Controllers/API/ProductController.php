<?php

namespace App\Http\Controllers\API;

use Tymon\JWTAuth\Facades\JWTAuth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:api', 'seller'], ['except' => ['login', 'register']]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:190',
            'name' => 'required|max:191',
            'brand_id' => 'required|max:191',
            'description' => 'required|max:190',
            'totalQuantity' => 'required|max:10000',
            'costprice' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $product = new Product();
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->description = $request->description;
            $product->costprice = $request->costprice;
            $product->totalQuantity = $request->totalQuantity;
            $product->totalPrice = $request->costprice * $request->totalQuantity;
            $product->save();
            return response()->json([
                'status' => 200,
                'message' => 'Product added successfully',
            ]);
        }
    }

    public function stockCount()
    {
        $products = Product::all();
        return response()->json([
            'status' => 422,
            'stockCount' => Product::count(),
            'totalProductsAmount' => Product::sum('totalPrice'),
        ]);
    }

    public function index(Request $request)
    {
        // $product = Product::with('category', 'brand', 'stock')->get();
        // return $request;
        $limit = $request->limit;
        if ($request->sort == "-id") {
            $product = Product::with('category', 'brand', 'stock')->orderBy('id', 'desc')->paginate($limit);
        } else {
            $product = Product::with('category', 'brand', 'stock')->paginate($limit);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $product = Product::where('name', 'LIKE', '%' . $request->name . '%')
                ->with(
                    'category',
                    'brand',
                    'stock'
                )->orderBy('id', $order)->paginate($limit);
        }
        $response = [
            'pagination' => [
                'total' => $product->total(),
                'per_page' => $product->perPage(),
                'current_page' => $product->currentPage(),
                'last_page' => $product->lastPage(),
                'from' => $product->firstItem(),
                'to' => $product->lastItem()
            ],
            'data' => $product
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function assignedProducts()
    {
        $id = auth()->user()->id;
        $user = User::find($id);
        // return $user;
        $products = $user->products;
        // return $products;
        return response()->json([
            'status' => 200,
            'result' => $products,
        ]);
    }

    public function appendStockToProduct(Request $request, $productId)
    {

        $productQuantity =  DB::table('products')->where('id', '=', $productId)->first()->totalQuantity;
        $newQuantity = $productQuantity + $request->quantityToAppend;
        DB::table('products')->where('id', '=', $productId)->update(['totalQuantity' => $newQuantity]);

        /// get the the stock quantity and price and update the total price
        $productQuantityAfterAppending =  DB::table('products')->where('id', '=', $productId)->first()->totalQuantity;
        $costprice =  DB::table('products')->where('id', '=', $productId)->first()->costprice;
        DB::table('products')->where('id', '=', $productId)->update(['totalPrice' => $productQuantityAfterAppending * $costprice]);

        return response()->json([
            'status' => 200,
            'result' => $newQuantity,
        ]);
    }

    public function edit($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no product found',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        // return $request;
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:190',
            'name' => 'required|max:191',
            'brand_id' => 'required|max:191',
            'description' => 'required|max:190',
            'costprice' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $product = Product::find($id);
            if ($product) {
                $product->name = $request->name;
                $product->category_id = $request->category_id;
                $product->brand_id = $request->brand_id;
                $product->description = $request->description;
                $product->costprice = $request->costprice;
                $product->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Product added successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'messages' => "product id not found",
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json([
                'status' => 200,
                'message' => 'product deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no product found',
            ]);
        }
    }
}
