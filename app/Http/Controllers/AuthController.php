<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\isAuth;
class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        // $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->middleware(isAuth::class)->except(['login', 'register']);

    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => ['required', 'exists:users,email', 'email:rfc,dns'],
            'password' => ['required', 'min:8'],
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ],422);
        }

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request){
        
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'unique:users,email', 'email:rfc,dns'],
            'password' => ['required', 'confirmed', 'min:8'],
            'avatar' => ['nullable', 'file', 'mimes:jpeg,bmp,png','between:100,250000'],
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ],422);
        }

        $path = null;

        if(request()->avatar){
            $path = request()->avatar->store('/public/users/avatar');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => $path,
        ]);
            

        return response()->json([
            'status' => 'success',
            'message' => 'User registred succesfully!'
        ]);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        
        $user = User::where('id', auth()->user()->id)->first();
        $user->load('client');
        return response()->json(['user' => $user]);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
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
        $user = User::where('id', auth()->user()->id)->first();
        $user->load('client');
        return response()->json([
            'access_token' => $token,
            'user' =>  $user,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
