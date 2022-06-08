<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staffname' => 'required|max:191',
            'month' => 'required|max:191',
            'date' => 'required|max:190',
            'amount' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $salary = new Salary();
            $salary->staffname = $request->staffname;
            $salary->month = $request->month;
            $salary->amount = $request->amount;
            $salary->date = $request->date;
            $salary->save();

            //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
            $previousCashAthand = DB::table('cashes')->first()->cashAthand;
            DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->sellingprice - $request->amount]);
            /////////////////////


            return response()->json([
                'status' => 200,
                'message' => 'Salary added successfully',
            ]);
        }
    }

    public function index(Request $request)
    {
        // $product = Product::with('category', 'brand', 'stock')->get();
        // return $request;
        if ($request->sort == "-id") {
            // $product = Product::with('category', 'brand')->orderBy('id', 'desc')->paginate(20);
            $product = Salary::orderBy('id', 'desc')->paginate(20);
        } else {
            $product = Salary::paginate(20);
        }

        if ($request->staffname) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $product = Salary::where('staffname', 'LIKE', '%' . $request->staffname . '%')->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $product->total(),
                'per_page' => $product->perPage(),
                'current_page' => $product->currentPage(),
                'last_page' => $product->lastPage(),
                'from' => $product->firstItem(),
                'to' => $product->lastItem()
            ],
            'data' => $product
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }

    public function edit($id)
    {
        $salary = Salary::find($id);
        if ($salary) {
            return response()->json([
                'status' => 200,
                'salary' => $salary,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no salary found',
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
            $salary = Salary::find($id);
            if ($salary) {
                $salary->staffname = $request->staffname;
                $salary->month = $request->month;
                $salary->amount = $request->amount;
                $salary->date = $request->date;
                $salary->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Salary added successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'messages' => "salary id not found",
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $salary = Salary::find($id);
        if ($salary) {
            $salary->delete();
            return response()->json([
                'status' => 200,
                'message' => 'salary deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no salary found',
            ]);
        }
    }
}
