<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
            $category = new Category();
            $category->name = $request->name;
            $category->date = Carbon::now()->toDateString();
    
            $category->save();
            return response()->json([
                'status' => 200,
                'message' => 'Category added successfully',
            ]);
        }
       
    }

    public function index(Request $request){
        $category = Category::paginate();
        if ($request->keyword) {
            $category = Category::where('name', 'LIKE', '%' .$request->keyword. '%')->get();
        }
        return response()->json([
            'status' => 200,
            'result' => $category,   
        ]);
    }

    public function productInCategory($categoryId){
        $sale = Category::find($categoryId)->with('products')->get();
        if ($sale) {
            return response()->json([
                'status' => 200,
                'categories' => $sale,
            ]);
        }
    }
    public function edit($id){
        $category = Category::find($id);
        if ($category) {
            return response()->json([
                'status' => 200,
                'category' => $category,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no category found',
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
            $category = Category::find($id);
            if ($category) {
                $category->name = $request->name;
                $category->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Student added successfully',
                ]);
            } else{ 
                return response()->json([
                    'status' => 404,
                    'messages' => "category id not found",
                ]);
            }
           
        }
       
    }

    public function destroy($id){
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json([
                'status' => 200,
                'message' => 'category deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no category found',
            ]);
        }
    }

}
