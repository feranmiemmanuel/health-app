<?php

namespace App\Http\Controllers\Auth;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string',
                    'last_name' => 'required|string',
                    'email' => 'required|string|email|max:255|unique:users,email',
                    'phone' => 'required|unique:users,phone',
                    'password' => 'required|string|confirmed|min:6'
                ]
            );
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
            }
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->user_type = 'PATIENT';
            $user->password = Hash::make($request->password);
            $user->save();

            $credentials = $request->only('email', 'password');

            $bearertoken = auth('api')->attempt($credentials);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registration is Successful',
                'access_token' => $bearertoken,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_type' => $user->user_type,
                'user_phone' => $user->phone,
                'user_email' => $user->email,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email',
                'password' => 'required|string|min:6'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }
        $credentials = $request->only('email', 'password');

        $bearertoken = auth('api')->attempt($credentials);
        if (!$bearertoken) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Details'
            ], 400);
        }
        return response()->json([
            'success' => true,
            'message' => 'Login Successful',
            'token_type' => 'bearer',
            'access_token' => $bearertoken,
            'first_name' => auth()->user()->first_name,
            'last_name' => auth()->user()->last_name,
            'user_type' => auth()->user()->user_type,
            'user_phone' => auth()->user()->phone,
            'user_email' => auth()->user()->email,
        ], 200);
    }
}
