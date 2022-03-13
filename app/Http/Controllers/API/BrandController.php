<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        } else{
            $brand = new Brand();
            $brand->name = $request->name;
    
            $brand->save();
            return response()->json([
                'status' => 200,
                'message' => 'Brand added successfully',
            ]);
        }
       
    }

    public function index(){
        $brand = Brand::all();
        return response()->json([
            'status' => 200,
            'result' => $brand,   
        ]);
    }

    public function edit($id){
        $brand = Brand::find($id);
        if ($brand) {
            return response()->json([
                'status' => 200,
                'brand' => $brand,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no brand found',
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
            $brand = Brand::find($id);
            if ($brand) {
                $brand->name = $request->name;
                $brand->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Brand
                     added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "brand id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $brand = Brand::find($id);
        if ($brand) {
            $brand->delete();
            return response()->json([
                'status' => 200,
                'message' => 'brand deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no brand found',
            ]);
        }
    }

}
