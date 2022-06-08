<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:191',
            'lastname' => 'required|max:191',
            'address' => 'required|max:190',
            'telephone' => 'required|max:200',
            'salary' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $staff = new Staff();
            $staff->firstname = $request->firstname;
            $staff->lastname = $request->lastname;
            $staff->address = $request->address;
            $staff->telephone = $request->telephone;
            $staff->salary = $request->salary;
            $staff->save();
            return response()->json([
                'status' => 200,
                'message' => 'staff added successfully',
            ]);
        }
    }

    public function index(Request $request)
    {
        // $staff = Product::with('category', 'brand', 'stock')->get();
        // return $request;
        if ($request->sort == "-id") {
            $staff = Staff::orderBy('id', 'desc')->paginate(20);
        } else {
            $staff = Staff::paginate(20);
        }

        if ($request->firstname) {
            $order = $request->sort == '-id' ? 'DESC' : 'ASC';
            $staff = Staff::where('firstname', 'LIKE', '%' . $request->firstname . '%')->orderBy('id', $order)->paginate(20);
        }
        $response = [
            'pagination' => [
                'total' => $staff->total(),
                'per_page' => $staff->perPage(),
                'current_page' => $staff->currentPage(),
                'last_page' => $staff->lastPage(),
                'from' => $staff->firstItem(),
                'to' => $staff->lastItem()
            ],
            'data' => $staff
        ];

        return response()->json([
            'status' => 200,
            'result' => $response,
        ]);
    }


    public function edit($id)
    {
        $staff = Staff::find($id);
        if ($staff) {
            return response()->json([
                'status' => 200,
                'staff' => $staff,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no staff found',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:191',
            'lastname' => 'required|max:191',
            'address' => 'required|max:190',
            'telephone' => 'required|max:200',
            'salary' => 'required|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else {
            $staff = Staff::find($id);
            if ($staff) {
                $staff->firstname = $request->firstname;
                $staff->lastname = $request->lastname;
                $staff->address = $request->address;
                $staff->telephone = $request->telephone;
                $staff->salary = $request->salary;
                $staff->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Staff added successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'messages' => "staff id not found",
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $staff = Staff::find($id);
        if ($staff) {
            $staff->delete();
            return response()->json([
                'status' => 200,
                'message' => 'staff deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no staff found',
            ]);
        }
    }
}
