<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], 401);
        }

        $input = $request->only(['fullname', 'email', 'username', 'password']);
        User::create($input);

        return response()->json([
            'success' => true,
            'message' => 'Register complete, please confirm your email'
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => true,
                'message' => $validator->errors()
            ], 401);
        }

        $input = $request->only(['email', 'password']);

        if(!User::where('email', $input['email'])->first()){
            return response()->json([
                'success' => false,
                'message' => 'Email not registered'
            ], 401);
        }

        if(!$token = auth()->attempt($input)){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expire_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }

    public function me(Request $request)
    {
        
    }
}
