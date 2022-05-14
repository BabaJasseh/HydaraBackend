<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    // public function store(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|max:191',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 422,
    //             'errors' => $validator->errors(),
    //         ]);
    //     } else{
    //         $payment = new Payment();
    //         $payment->name = $request->name;
    
    //         $payment->save();
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Payment added successfully',
    //         ]);
    //     }
       
    // }

    public function index(Request $request, $saleId)
    {
        // $product = Product::with('category', 'payment', 'stock')->get();
        // return $request;
        if ($request->sort == "-id") {
            $payment = Payment::where('sale_id','=', $saleId)->orderBy('id', 'desc')->paginate(20);
        } else {
            $payment = Payment::where('sale_id', '=' ,$saleId)->paginate(20);
        }

        if ($request->name) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $payment = Payment::where('name', 'LIKE', '%' . $request->name . '%')
                ->with(
                    'products',
                )->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $payment->total(),
                'per_page' => $payment->perPage(),
                'current_page' => $payment->currentPage(),
                'last_page' => $payment->lastPage(),
                'from' => $payment->firstItem(),
                'to' => $payment->lastItem()
            ],
            'data' => $payment
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }


    public function edit($id){
        $payment = Payment::find($id);
        if ($payment) {
            return response()->json([
                'status' => 200,
                'payment' => $payment,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no payment found',
            ]);
        }
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $payment = Payment::find($id);
            if ($payment) {
                $payment->name = $request->name;
                $payment->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Payment
                     added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "payment id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $payment = Payment::find($id);
        if ($payment) {
            $payment->delete();
            return response()->json([
                'status' => 200,
                'message' => 'payment deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no payment found',
            ]);
        }
    }

}
