<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Cash;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SalaryController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|max:191',
            'month' => 'required|max:191',
            'amount' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $salary = new Salary();
            $salary->staff_id = $request->staff_id;
            $salary->month = $request->month;
            $salary->amount = $request->amount;
            $salary->date = Carbon::now()->toDateString();
            $salary->save();

            //////////////////////////////   subtract the withdraw amount to the cashes.cashathand //////////////////
            $previousCashAthand = DB::table('cashes')->first();
            if ($previousCashAthand == null) {
                $cash = new Cash();
                $cash->cashAthand = 0 - $request->amount;
                $cash->currentBalance = 0 - $request->amount;
                $cash->save();
            } else {
                $previousCashAthand = DB::table('cashes')->first()->cashAthand;
                DB::table('cashes')->update(['cashAthand' => $previousCashAthand - $request->amount]);
                $previousCurrentBalance = DB::table('cashes')->first()->currentBalance;
                DB::table('cashes')->update(['currentBalance' => $previousCurrentBalance - $request->amount]);
            }
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
        $limit = $request->limit;
        if ($request->sort == "-id") {
            // $product = Product::with('category', 'brand')->orderBy('id', 'desc')->paginate(20);
            $product = Salary::orderBy('id', 'desc')->with('staff')->paginate($limit);
        } else {
            $product = Salary::with('staff')->paginate($limit);
        }

        if ($request->staffname) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $product = Salary::where('staffname', 'LIKE', '%' . $request->staffname . '%')->with('staff')->orderBy('id', $order)->paginate($limit);
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
