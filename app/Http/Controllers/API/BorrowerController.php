<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BorrowerController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:191',
            'lastname' => 'required|max:191',
            'address' => 'required|max:191',
            'description' => 'required|max:190',
            'telephone' => 'required|max:190',
            'initialBorrow' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else{
            $borrower = new Borrower();
            $borrower->firstname = $request->firstname;
            $borrower->lastname = $request->lastname;
            $borrower->address = $request->address;
            $borrower->description = $request->description;
            $borrower->telephone = $request->telephone;
            $borrower->initialBorrow = $request->initialBorrow;
            $borrower->balance = $borrower->initialBorrow;
            $borrower->save();
            return response()->json([
                'status' => 200,
                'message' => 'Borrower added successfully',
            ]);
        }
       
    }

    public function payBorrowedAmount(Request $request, $id){
        $amountToPay = $request->amountToPay;
        $amountBorrowed =  DB::table('borrowers')->where('id', '=', $id)->first()->amountBorrowed;
        DB::table('borrowers')->update(['amountBorrowed' => $amountBorrowed  - $amountToPay]);
              //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
              $previousCashAthand = DB::table('cashes')->first()->cashAthand;
              DB::table('cashes')->update(['cashAthand' => $previousCashAthand + $request->amountBorrowed]);
              ///////////////////////////////////////////
             $amountBorrowed =  DB::table('borrowers')->where('id', '=', $id)->first()->amountBorrowed;
             if ($amountBorrowed == 0) {
                DB::table('borrowers')->where('id', '=', $id)->update(['paymentStatus' => 1]);
             } else{
                DB::table('borrowers')->where('id', '=', $id)->update(['paymentStatus' => 0]);
             }
    }

    public function index(Request $request){
        if ($request->sort == "-id") {
            $borrower = Borrower::with('borrowertransactions')->orderBy('id', 'desc')->paginate(20);
        } else {
            $borrower = Borrower::with('borrowertransactions')->paginate(20);
        }

        if ($request->firstname) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $borrower = Borrower::where('firstname', 'LIKE', '%' . $request->firstname . '%')
                ->with(
                    'borrowertransactions',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $borrower->total(),
                'per_page' => $borrower->perPage(),
                'current_page' => $borrower->currentPage(),
                'last_page' => $borrower->lastPage(),
                'from' => $borrower->firstItem(),
                'to' => $borrower->lastItem()
            ],
            'data' => $borrower
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function transactionsOfBorrower(Request $request, $id){
        // $borrower = Borrower::find($id);
        $borrower = Borrower::where('id', $id)->firstOrFail()->borrowertransactions()->paginate(5);
        
        if ($request->sort == "-id") {
            $borrower = Borrower::where('id', $id)->first()->borrowertransactions()->orderBy('id', 'desc')->paginate(20);
        } else {
            $borrower = Borrower::where('id', $id)->first()->borrowertransactions()->paginate(20);
        }

        if ($request->firstname) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $borrower = Borrower::where('firstname', 'LIKE', '%' . $request->firstname . '%')
                ->with(
                    'transactions',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $borrower->total(),
                'per_page' => $borrower->perPage(),
                'current_page' => $borrower->currentPage(),
                'last_page' => $borrower->lastPage(),
                'from' => $borrower->firstItem(),
                'to' => $borrower->lastItem()
            ],
            'data' =>  $borrower,
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function edit($id){
        $borrower = Borrower::find($id);
        if ($borrower) {
            return response()->json([
                'status' => 200,
                'borrower' => $borrower,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no borrower found',
            ]);
        }
    }

    public function borrowersCount(){
        $borrower = Borrower::all();
        return response()->json([
            'status' => 422,
            'borrowerCount' => Borrower::count(),
            'totalBorrowedAmount' => Borrower::sum('balance'),
        ]);
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
            $borrower = Borrower::find($id);
            if ($borrower) {
                $borrower->firstname = $request->firstname;
                $borrower->lastname = $request->lastname;
                $borrower->address = $request->address;
                $borrower->description = $request->description;
                $borrower->telephone = $request->telephone;
                $borrower->initialDeposit = $request->initialDeposit;
                $borrower->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Borrower added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "borrower id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $borrower = Borrower::find($id);
        if ($borrower) {
            $borrower->delete();
            return response()->json([
                'status' => 200,
                'message' => 'borrower deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no borrower found',
            ]);
        }
    }

}
