<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Creditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CreditorController extends Controller
{
    // public function store(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'firstname' => 'required|max:191',
    //         'lastname' => 'required|max:191',
    //         'address' => 'required|max:191',
    //         'description' => 'required|max:190',
    //         'telephone' => 'required|max:190',
    //         'initialDeposit' => 'required|max:200',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 422,
    //             'errors' => $validator->errors(),
    //         ]);
    //     } else{
    //         $creditor = new Creditor();
    //         $creditor->firstname = $request->firstname;
    //         $creditor->lastname = $request->lastname;
    //         $creditor->address = $request->address;
    //         $creditor->description = $request->description;
    //         $creditor->telephone = $request->telephone;
    //         $creditor->initialDeposit = $request->initialDeposit;
    //         $creditor->save();
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Creditor added successfully',
    //         ]);
    //     }
       
    // }

    public function store(Request $request, $id){
        $addAmount = $request->addAmount;
        // $sale->date = Carbon::now()->toDateString();
        $previousAmount =  DB::table('creditors')->where('id', '=', $id)->first()->amountpaid;
        $balance =  DB::table('creditors')->where('id', '=', $id)->first()->balance;
        DB::table('creditors')->where('id', '=', $id)->update(['amountpaid' => $previousAmount + $addAmount, 'balance' => $balance - $addAmount]);
        if ($balance == 0) {
            DB::table('creditors')->where('id', '=', $id)->update(['paymentstatus' => 1]);

        } else{
            DB::table('creditors')->where('id', '=', $id)->update(['paymentstatus' => 0]);

        }

          //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
          $previousCashAthand = DB::table('cashes')->first()->cashAthand;
          DB::table('cashes')->update(['cashAthand' => $previousCashAthand + $request->amountBorrowed]);
          ///////////////////
    }

    public function index(){
        $totalCreditAmount = Creditor::sum('balance');
        $totalCreditors = Creditor::where('balance', '>', 0)->count();
        return response()->json([
            'status' => 200,
            'totalCreditAmount' => $totalCreditAmount,  
            'totalCreditors' => $totalCreditors,         
        ]);
    }

    public function edit($id){
        $creditor = Creditor::find($id);
        if ($creditor) {
            return response()->json([
                'status' => 200,
                'creditor' => $creditor,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no creditor found',
            ]);
        }
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:191',
            'lastname' => 'required|max:191',
            'address' => 'required|max:191',
            'description' => 'required|max:190',
            'telephone' => 'required|max:190',
            'initialDeposit' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $creditor = Creditor::find($id);
            if ($creditor) {
                $creditor->firstname = $request->firstname;
                $creditor->lastname = $request->lastname;
                $creditor->address = $request->address;
                $creditor->description = $request->description;
                $creditor->telephone = $request->telephone;
                $creditor->initialDeposit = $request->initialDeposit;
                $creditor->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Creditor added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "creditor id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $creditor = Creditor::find($id);
        if ($creditor) {
            $creditor->delete();
            return response()->json([
                'status' => 200,
                'message' => 'creditor deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no creditor found',
            ]);
        }
    }

}
