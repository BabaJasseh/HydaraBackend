<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $req)
    {
        $limit = $req->limit;
        $user = User::paginate($limit);
        $response = [
            'pagination' => [
                'total' => $user->total(),
                'per_page' => $user->perPage(),
                'current_page' => $user->currentPage(),
                'last_page' => $user->lastPage(),
                'from' => $user->firstItem(),
                'to' => $user->lastItem()
            ],
            'data' => $user
        ];
        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function usersBasedOnCategory($productId)
    {
        $category = Product::find($productId)->category()->first()->name;
        if ($category == 'Electronic Devices') {
            $user =  DB::table('users')->where('userType', '=', 'electronicDeviceSeller')->get();
        } else if ($category == 'Accessories') {
            $user =  DB::table('users')->where('userType', '=', 'accessoriesSeller')->get();
        } else if ($category == 'Mobiles') {
            $user =  DB::table('users')->where('userType', '=', 'mobileSeller')->get();
        }
        return response()->json([
            'status' => 200,
            'user' => $user,
        ]);
    }

    public function usersCount()
    {

        // $simple_collection = collect([2,5,7,35,25,10]);
        // $simple_collection->min()
        return response()->json([
            'status' => 422,
            'userCount' => User::count(),
        ]);
    }

    public function edit($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'status' => 200,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no user found',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:190',
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
            $user = User::find($id);
            if ($user) {
                $user->name = $request->name;
                $user->category_id = $request->category_id;
                $user->costprice = $request->brand_id;
                $user->quantity = $request->quantity;
                $user->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Student added successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'messages' => "user id not found",
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => 'user deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no user found',
            ]);
        }
    }
}
