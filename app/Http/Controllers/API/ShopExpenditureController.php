<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shopexpenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ShopExpenditureController extends Controller
{
    public function store(Request $request){
        $shopExpenditure = new Shopexpenditure();
        $shopExpenditure->categoryName = $request->categoryName;
        $shopExpenditure->address = $request->address;
        $shopExpenditure->description = $request->description;
        $shopExpenditure->initialExpense = $request->initialExpense;
        $shopExpenditure->save();
         //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
         $previousCashAthand = DB::table('cashes')->first()->cashAthand;
         DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->sellingprice - $request->amount]);
         /////////////////////
         
        return response()->json([
            'status' => 200,
            'message' => 'Shopexpenditure added successfully',
        ]);
    }

    public function index(Request $request)
    {
        
        if ($request->sort == "-id") {
            $shopExpenditure = Shopexpenditure::orderBy('id', 'desc')->paginate(20);
        } else {
            $shopExpenditure = Shopexpenditure::paginate(20);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $shopExpenditure = Shopexpenditure::where('name', 'LIKE', '%' . $request->name . '%')
                ->with(
                    'category',
                    'brand',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $shopExpenditure->total(),
                'per_page' => $shopExpenditure->perPage(),
                'current_page' => $shopExpenditure->currentPage(),
                'last_page' => $shopExpenditure->lastPage(),
                'from' => $shopExpenditure->firstItem(),
                'to' => $shopExpenditure->lastItem()
            ],
            'data' => $shopExpenditure
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function destroy($id){
        $shopExpenditure = Shopexpenditure::find($id);
        if ($shopExpenditure) {
            $shopExpenditure->delete();
            return response()->json([
                'status' => 200,
                'message' => 'shopExpenditure deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no shopExpenditure found',
            ]);
        }
    }

  
}
