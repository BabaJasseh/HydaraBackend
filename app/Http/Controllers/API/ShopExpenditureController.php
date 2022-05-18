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

        if ($request->categoryName) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $shopExpenditure = Shopexpenditure::where('categoryName', 'LIKE', '%' . $request->categoryName . '%')->orderBy('id', $order)->paginate(20);
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
