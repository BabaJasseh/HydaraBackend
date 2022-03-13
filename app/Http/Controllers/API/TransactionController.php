<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            // 'category_id'=>'required|max:190',
            // 'name' => 'required|max:191',
            // 'brand_id' => 'required|max:191',
            // 'description' => 'required|max:190',
            // 'price' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else{
            $transaction = new Transaction();
            $transaction->depositor_id = $request->depositor_id;
            $transaction->description = $request->description;
            $transaction->amount = $request->amount;
            $transaction->action = $request->action;  ///////   action should either be a deposit or a withdraw
            $transaction->save();
            $previousBalance = DB::table('depositors')->where('id', '=', $request->depositor_id)->first()->balance;
            if ($request->action == "deposit") {
                DB::table('depositors')->where('id', '=', $request->depositor_id)->update(['balance' => $previousBalance + $request->amount]);
                 //////////////////////////////   add the deposit amount to the cashes.cashathand //////////////////
                $previousCashAthand = DB::table('cashes')->first()->cashAthand;
                DB::table('cashes')->update(['cashAthand' => $previousCashAthand + $request->amount]);
            } elseif($request->action == "withdraw"){
                DB::table('depositors')->where('id', '=', $request->depositor_id)->update(['balance' => $previousBalance - $request->amount]);
                //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
                $previousCashAthand = DB::table('cashes')->first()->cashAthand;
                DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->amount]);
            }
           
           


            return response()->json([
                'status' => 200,
                'message' => 'Transaction added successfully',
            ]);
        }
       
    }

    public function index(){
        $transaction = Transaction::all();
        return response()->json([
            'status' => 200,
            'result' => $transaction,           
        ]);
    }

    public function edit($id){
        $transaction = Transaction::find($id);
        if ($transaction) {
            return response()->json([
                'status' => 200,
                'transaction' => $transaction,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no transaction found',
            ]);
        }
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'category_id'=>'required|max:190',
            'name' => 'required|max:191',
            'brand_id' => 'required|max:191',
            'description' => 'required|max:190',
            'price' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $transaction = Transaction::find($id);
            if ($transaction) {
                $transaction->name = $request->name;
                $transaction->category_id = $request->category_id;
                $transaction->brand_id = $request->brand_id;
                $transaction->description = $request->description;
                $transaction->price = $request->price;
                $transaction->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Student added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "transaction id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
            return response()->json([
                'status' => 200,
                'message' => 'transaction deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no transaction found',
            ]);
        }
    }

}
