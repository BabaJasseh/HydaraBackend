<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Depositor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepositorController extends Controller
{
    public function store(Request $request){
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
        } else{
            $depositor = new Depositor();
            $depositor->firstname = $request->firstname;
            $depositor->lastname = $request->lastname;
            $depositor->address = $request->address;
            $depositor->description = $request->description;
            $depositor->telephone = $request->telephone;
            $depositor->initialDeposit = $request->initialDeposit;
            $depositor->balance = $depositor->initialDeposit;
            $depositor->save();
            return response()->json([
                'status' => 200,
                'message' => 'Depositor added successfully',
            ]);
        }
       
    }

    public function index(Request $request){
        // $depositor = Depositor::all();
        // return response()->json([
        //     'status' => 200,
        //     'result' => $depositor,           
        // ]);
        if ($request->sort == "-id") {
            $depositor = Depositor::with('transactions')->orderBy('id', 'desc')->paginate(20);
        } else {
            $depositor = Depositor::with('transactions')->paginate(20);
        }

        if ($request->firstname) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $depositor = Depositor::where('firstname', 'LIKE', '%' . $request->firstname . '%')
                ->with(
                    'transactions',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $depositor->total(),
                'per_page' => $depositor->perPage(),
                'current_page' => $depositor->currentPage(),
                'last_page' => $depositor->lastPage(),
                'from' => $depositor->firstItem(),
                'to' => $depositor->lastItem()
            ],
            'data' => $depositor
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function transactionsOfdepositor(Request $request, $id){
        // $depositor = Depositor::find($id);
        $depositor = Depositor::where('id', $id)->firstOrFail()->transactions()->paginate(5);
        
        if ($request->sort == "-id") {
            $depositor = Depositor::where('id', $id)->first()->transactions()->orderBy('id', 'desc')->paginate(20);
        } else {
            $depositor = Depositor::where('id', $id)->first()->transactions()->paginate(20);
        }

        if ($request->firstname) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $depositor = Depositor::where('firstname', 'LIKE', '%' . $request->firstname . '%')
                ->with(
                    'transactions',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $depositor->total(),
                'per_page' => $depositor->perPage(),
                'current_page' => $depositor->currentPage(),
                'last_page' => $depositor->lastPage(),
                'from' => $depositor->firstItem(),
                'to' => $depositor->lastItem()
            ],
            'data' =>  $depositor,
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function edit($id){
        $depositor = Depositor::find($id);
        if ($depositor) {
            return response()->json([
                'status' => 200,
                'depositor' => $depositor,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no depositor found',
            ]);
        }
    }

    public function depositorCount(){

        // $simple_collection = collect([2,5,7,35,25,10]);
        // $simple_collection->min()
        $depositor = Depositor::all();
        return response()->json([
            'status' => 422,
            'depositorCount' => Depositor::count(),
            'totalDeposits' => $depositor->sum('balance'),
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
            $depositor = Depositor::find($id);
            if ($depositor) {
                $depositor->firstname = $request->firstname;
                $depositor->lastname = $request->lastname;
                $depositor->address = $request->address;
                $depositor->description = $request->description;
                $depositor->telephone = $request->telephone;
                $depositor->initialDeposit = $request->initialDeposit;
                $depositor->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Depositor added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "depositor id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $depositor = Depositor::find($id);
        if ($depositor) {
            $depositor->delete();
            return response()->json([
                'status' => 200,
                'message' => 'depositor deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no depositor found',
            ]);
        }
    }

}
