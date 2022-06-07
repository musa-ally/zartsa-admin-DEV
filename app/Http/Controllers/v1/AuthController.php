<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Validator;
use JWTFactory;
use Illuminate\Contracts\Auth\Guard;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
	
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $auth)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->auth = $auth;
    }
	
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
	    $this->validate( request(), [
			'username'  => ['required'],
			'password'  => ['required'],
		], [
			'username.required'  => 'Username Required',
			'password.required'  => 'Password Required',
		] );
		
       $credentials = request(['username', 'password']);

        if (! $jwt = $this->auth->attempt($credentials)) {
          return response()->json(['error' => 'Unauthorized'], 401);
        }

       return $this->respondWithToken($jwt);
    }
	
	 
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getmydata()
    {
        return response()->json(auth()->user());
		$this->authorize('getmydata');
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
    protected function respondWithToken($jwt)
    {
		
        return response()->json([
            'access_token' => $jwt,
            'token_type' => 'bearer',
			'expires_in' => auth('api')->factory()->getTTL()
        ]);
    }
}