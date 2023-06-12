<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Error;
use App\Models\User;
use App\Helpers\ResponseFormatter;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            //validate request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8',
                'confirmation_password' => 'required|min:8|string'
            ]);

            if($request->password != $request->confirmation_password){
                return ResponseFormatter::error([
                    'message' => 'Password not match'
                ], 'Authentication failed', 500);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user = User::where('email', $request->email)->first();
           

            //jika berhasil maka login
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered Successfully');

        } catch (\Error $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }


    public function login(Request $request)
    {
        try {
            //request validasi
            $this->validate($request,[
                'email' => 'required|email',
                'password' => 'required'
            ]);

            //cek kredesnsial login
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized' 
                ], 'Authentication Failed', 500);
            };

            //jika hash tidak sesuai
            $user = User::where('email', $request->email)->first();
            if(!Hash::check($request->password, $user->password,[])){
                throw new \Exception('Invalid Credentials');
            };

            //jika berhasil maka login
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        } catch (\Error $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoked');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'current_password' => 'required',
            'password' => 'required|min:8|string',
            'confirmation_password' => 'required|min:8|string'
        ]);

        if($validator->fails()){
            return ResponseFormatter::error([
                'error' => $validator->errors()
            ], 'Update password failed', 401);
        }

        $user = Auth::user();

        if(!Hash::check($data['current_password'],$user->password)){
            return ResponseFormatter::error([
                'message' => 'Current password is not match'
            ], 'Update password failed', 401);
        }

        if($data['password'] != $data['confirmation_password']){
            return ResponseFormatter::error([
                'message' => 'Password is not match'
            ], 'Update password failed', 401);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return ResponseFormatter::success([
            'user' => $user
        ], 'Update password Success');
    }
}
