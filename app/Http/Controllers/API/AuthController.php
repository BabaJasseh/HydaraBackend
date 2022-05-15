<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:191',
            'lastname' => 'required|max:191',
            'email' => 'required|max:191',
            'password' => 'required|max:191',
            'userType' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'registration failed',

            ]);
        }

        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->userType = $request->userType;
        $user->save();
        return response([
            'message' => "user created successfully",
            'users' => $user,
        ]);
    }
    public function login()
    {
        $credentials = request(['email', 'password']);
        // return $credentials['email'];

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    // public function info()
    // {
    //     $data = [
    //         "code" => 200,
    //         "roles" => ["admin"],
    //         "introduction" => "I am a super administrator",
    //         "avatar" => ('/hydara_logo.png'),
    //         "name" => "Super Admin"
    //     ];
    //     return response()->json($data, 200);
    // }

    // public function login(Request $request)
    // {
    //     // return $password = bcrypt('yourPa$$w0rd');;
    //     if (!JWTAuth::attempt($request->only('email', 'password')))
    //     {
    //         return response()
    //             ->json(['message' => 'Unauthorized']);
    //     }

    //     $user = User::where('email', $request['email'])->firstOrFail();

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()
    //         ->json(['message' => $token]);
    // }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'code' => 200,
            'data' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'code' => 200,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl'),
            'firstname' => JWTAuth::user()->firstname,
            'user_type' => JWTAuth::user()->userType,
        ]);
    }

    public function info()
    {
        $roles = array(JWTAuth::user()->userType);
        $data = [
            "code" => 200,
            "roles" => $roles,
            "introduction" => "I am a super administrator",
            "avatar" => ('/hydara_logo.png'),
            "name" => JWTAuth::user()->firstname,
        ];
        return response()->json($data, 200);
    }
}
