<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ExpenditureController extends Controller
{
    public function store(Request $request){
        $expenditure = new Expenditure();
        $expenditure->name = $request->name;
        $expenditure->description = $request->description;
        $expenditure->amount = $request->amount;
        $expenditure->save();
         //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
         $previousCashAthand = DB::table('cashes')->first()->cashAthand;
         DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->sellingprice - $request->amount]);
         /////////////////////
         
        return response()->json([
            'status' => 200,
            'message' => 'Expenditure added successfully',
        ]);
    }

    public function index(){
        $expenditure = Expenditure::with('classe')->get();
        return response()->json([
            'status' => 200,
            'result' => $expenditure,           
        ]);
    }

    public function destroy($id){
        $expenditure = Expenditure::find($id);
        if ($expenditure) {
            $expenditure->delete();
            return response()->json([
                'status' => 200,
                'message' => 'expenditure deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no expenditure found',
            ]);
        }
    }

  
}
