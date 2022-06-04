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
           ////////////////////////   this the logic to add the amount  to the total expenditures //////////////////
           $previousTotalExpense = DB::table('cashes')->first();
           if ($previousTotalExpense == null) {
               $cash = new Cash();
               $cash->totalExpense = $request->initialExpense;
               $cash->save();
           } else{
               $previousTotalExpense = DB::table('cashes')->first()->totalExpense;
               DB::table('cashes')->update(['totalExpense' => $previousTotalExpense + $request->initialExpense]);
           }
           /////////////////////  the logic for expenditures ends here  ////////////////////////

           ////////////////////////   this the logic to add the amount paid to the current balance //////////////////
           $previousCurrentBalance = DB::table('cashes')->first();
           if ($previousCurrentBalance == null) {
               $cash = new Cash();
               $cash->currentBalance = $request->initialExpense;
               $cash->save();
           } else{
               $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
               DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance - $request->initialExpense]);
           }
           /////////////////////  the logic ends here  ////////////////////////
         
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
