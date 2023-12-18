<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register
     */


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $newUser = new User();
        $newUser->name = $request->name;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);

        if ($newUser->save()) {
            return response()->json([
                'Message' => 'Register EndPoint',
                'Role Utilisateur' => $request->user()->role
            ], 200);
        }

        return response()->json([
            'Message' => 'Bad register'
        ], 500);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        $credentials = $request->only('email', 'password');

        if ($token = Auth::attempt($credentials)) {
            // $token = $request->user()->createToken('MyToken');

            // return response()->json([
            //     'Message' => 'User dans la base',
            //     // 'Token' => $token->plainTextToken,
            //     'Role Utilisateur' => $request->user()->role
            // ], 200);
            return $this->respondWithToken($token);
        }

        return response()->json(['Message' => 'User pas dans la base'], 401);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
