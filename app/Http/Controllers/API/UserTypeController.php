<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Usertype;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;


class UserTypeController extends Controller
{
    // public function store(Request $request){
    //     $user = new User();
    //     $user->classe_id = $request->classe_id;
    //     $user->name = $request->name;
    //     $user->password = $request->password;
    //     $user->gender = $request->gender;
    //     $user->save();
    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Student added successfully',
    //     ]);
    // }

    public function index(){
        // $user = Student::with('classe')->get();
        $user = Usertype::all();
        return response()->json([
            'status' => 200,
            'result' => $user,           
        ]);
    }

    public function destroy($id){
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => 'user deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no user found',
            ]);
        }
    }


}
