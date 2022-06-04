<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrowertransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BorrowerTransactionController extends Controller
{
    public function storeBorrowerTransaction(Request $request){
        $validator = Validator::make($request->all(), [
   
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else{
            $borrowertransaction = new Borrowertransaction();
            $borrowertransaction->borrower_id = $request->borrower_id;
            $borrowertransaction->description = $request->description;
            $borrowertransaction->amount = $request->amount;
            $borrowertransaction->action = $request->action;  ///////   action should either be a deposit or a withdraw
            $borrowertransaction->save();
            $previousBalance = DB::table('borrowers')->where('id', '=', $request->borrower_id)->first()->balance;
            if ($request->action == "repay") {
                DB::table('borrowers')->where('id', '=', $request->borrower_id)->update(['balance' => $previousBalance - $request->amount]);

                   ////////////////////////   this the logic to add the amount paid to the current balance //////////////////
                    $previousCurrentBalance = DB::table('cashes')->first();
                    if ($previousCurrentBalance == null) {
                        $cash = new Cash();
                        $cash->currentBalance = $request->amount;
                        $cash->save();
                    } else{
                        $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
                        DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance + $request->amount]);
                    }
                    /////////////////////  the logic ends here  ////////////////////////

            } elseif($request->action == "borrow"){
                DB::table('borrowers')->where('id', '=', $request->borrower_id)->update(['balance' => $previousBalance + $request->amount]);
                //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
                $previousCashAthand = DB::table('cashes')->first()->cashAthand;
                DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->amount]);

                   ////////////////////////   this the logic to add the amount paid to the current balance //////////////////
                    $previousCurrentBalance = DB::table('cashes')->first();
                    if ($previousCurrentBalance == null) {
                        $cash = new Cash();
                        $cash->currentBalance = $request->amount;
                        $cash->save();
                    } else{
                        $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
                        DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance - $request->amount]);
                    }
                    /////////////////////  the logic ends here  ////////////////////////
            }

            // if you borrow your balance increases and decreases when you repay
           
           


            return response()->json([
                'status' => 200,
                'message' => 'Borrowertransaction added successfully',
            ]);
        }
       
    }

    public function index(Request $request)
    {
        // $borrowertransaction = Product::with('category', 'brand', 'stock')->get();
        // return $request;
        if ($request->sort == "-id") {
            $borrowertransaction = Borrowertransaction::with('borrower')->orderBy('id', 'desc')->paginate(20);
        } else {
            $borrowertransaction = Borrowertransaction::with('borrower')->paginate(20);
        }

        if ($request->firstname) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $borrowertransaction = Borrowertransaction::where('firstname', 'LIKE', '%' . $request->firstname . '%')
                ->with(
                    'depositor',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $borrowertransaction->total(),
                'per_page' => $borrowertransaction->perPage(),
                'current_page' => $borrowertransaction->currentPage(),
                'last_page' => $borrowertransaction->lastPage(),
                'from' => $borrowertransaction->firstItem(),
                'to' => $borrowertransaction->lastItem()
            ],
            'data' => $borrowertransaction
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function edit($id){
        $borrowertransaction = Borrowertransaction::find($id);
        if ($borrowertransaction) {
            return response()->json([
                'status' => 200,
                'borrowertransaction' => $borrowertransaction,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no borrowertransaction found',
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
            $borrowertransaction = Borrowertransaction::find($id);
            if ($borrowertransaction) {
                $borrowertransaction->name = $request->name;
                $borrowertransaction->category_id = $request->category_id;
                $borrowertransaction->brand_id = $request->brand_id;
                $borrowertransaction->description = $request->description;
                $borrowertransaction->price = $request->price;
                $borrowertransaction->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Student added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "borrowertransaction id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $borrowertransaction = Borrowertransaction::find($id);
        if ($borrowertransaction) {
            $borrowertransaction->delete();
            return response()->json([
                'status' => 200,
                'message' => 'borrowertransaction deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no borrowertransaction found',
            ]);
        }
    }

}
