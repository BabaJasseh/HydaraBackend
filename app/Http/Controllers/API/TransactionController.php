<?php

namespace App\Http\Controllers\API;

use App\Models\Cash;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
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
        } else {
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

                $previousCashAthand = DB::table('cashes')->first();
                if ($previousCashAthand == null) {
                    $cash = new Cash();
                    $cash->cashAthand = $request->amount;
                    $cash->currentBalance = $request->amount;
                    $cash->save();
                } else {
                    $previousCashAthand = DB::table('cashes')->first()->cashAthand;
                    DB::table('cashes')->update(['cashAthand' => $previousCashAthand + $request->amount]);
                    $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
                    DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance + $request->amount]);
                }


                // ////////////////////////   this the logic to add the amount paid to the current balance //////////////////
                // $previousCurrentBalance = DB::table('cashes')->first();
                // if ($previousCurrentBalance == null) {
                //     $cash = new Cash();
                //     $cash->currentBalance = $request->amount;
                //     $cash->save();
                // } else {
                //     $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
                //     DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance + $request->amount]);
                // }
                // /////////////////////  the logic ends here  ////////////////////////

            } elseif ($request->action == "withdraw") {
                DB::table('depositors')->where('id', '=', $request->depositor_id)->update(['balance' => $previousBalance - $request->amount]);
                //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
                $previousCashAthand = DB::table('cashes')->first()->cashAthand;
                DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->amount]);

                ////////////////////////   this the logic to add the amount paid to the current balance //////////////////
                $previousCurrentBalance = DB::table('cashes')->first();
                if ($previousCurrentBalance == null) {
                    $cash = new Cash();
                    $cash->currentBalance = $request->amount;
                    $cash->save();
                } else {
                    $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
                    DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance - $request->amount]);
                }
                /////////////////////  the logic ends here  ////////////////////////
            }

            return response()->json([
                'status' => 200,
                'message' => 'Transaction added successfully',
            ]);
        }
    }

    public function index(Request $request)
    {
        // $transaction = Product::with('category', 'brand', 'stock')->get();
        // return $request;
        if ($request->sort == "-id") {
            $transaction = Transaction::with('depositor')->orderBy('id', 'desc')->paginate(20);
        } else {
            $transaction = Transaction::with('depositor')->paginate(20);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $transaction = Transaction::where('name', 'LIKE', '%' . $request->name . '%')
                ->with(
                    'depositor',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $transaction->total(),
                'per_page' => $transaction->perPage(),
                'current_page' => $transaction->currentPage(),
                'last_page' => $transaction->lastPage(),
                'from' => $transaction->firstItem(),
                'to' => $transaction->lastItem()
            ],
            'data' => $transaction
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function edit($id)
    {
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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|max:190',
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
            } else {
                return response()->json([
                    'status' => 404,
                    'messages' => "transaction id not found",
                ]);
            }
        }
    }

    public function destroy($id)
    {
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
