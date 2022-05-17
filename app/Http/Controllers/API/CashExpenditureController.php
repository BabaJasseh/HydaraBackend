<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cashexpenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CashExpenditureController extends Controller
{
    public function store(Request $request){
        $cashExpenditure = new Cashexpenditure();
        $cashExpenditure->categoryName = $request->categoryName;
        $cashExpenditure->address = $request->address;
        $cashExpenditure->description = $request->description;
        $cashExpenditure->initialExpense = $request->initialExpense;
        $cashExpenditure->save();
         //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
         $previousCashAthand = DB::table('cashes')->first()->cashAthand;
         DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->sellingprice - $request->amount]);
         /////////////////////
         
        return response()->json([
            'status' => 200,
            'message' => 'Cashexpenditure added successfully',
        ]);
    }

    public function index(Request $request)
    {
        // $cashExpenditure = Product::with('category', 'brand', 'stock')->get();
        // return $request;
        if ($request->sort == "-id") {
            // $cashExpenditure = Product::with('category', 'brand')->orderBy('id', 'desc')->paginate(20);
            $cashExpenditure = Cashexpenditure::orderBy('id', 'desc')->paginate(20);
        } else {
            $cashExpenditure = Cashexpenditure::paginate(20);
        }

        if ($request->categoryName) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $cashExpenditure = Cashexpenditure::where('categoryName', 'LIKE', '%' . $request->categoryName . '%')->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $cashExpenditure->total(),
                'per_page' => $cashExpenditure->perPage(),
                'current_page' => $cashExpenditure->currentPage(),
                'last_page' => $cashExpenditure->lastPage(),
                'from' => $cashExpenditure->firstItem(),
                'to' => $cashExpenditure->lastItem()
            ],
            'data' => $cashExpenditure
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function destroy($id){
        $cashExpenditure = Cashexpenditure::find($id);
        if ($cashExpenditure) {
            $cashExpenditure->delete();
            return response()->json([
                'status' => 200,
                'message' => 'cas$cashExpenditure deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no cas$cashExpenditure found',
            ]);
        }
    }

  
}
