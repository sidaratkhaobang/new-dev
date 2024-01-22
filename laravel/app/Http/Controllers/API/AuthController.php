<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['username', 'password']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = Auth::user();
            $tokenData = [];
            foreach (config('services.account_api') as $account) {
                if ($account['username'] == $user->username) {
                    $tokenData = [
                        'abilities' => $account['abilities'],
                        'token_name' => $account['token_name'],
                    ];
                    break;
                }
            }

            if ($tokenData) {
                $user->tokens()->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'User Logged In Successfully',
                    'token' => $user->createToken($tokenData['token_name'], $tokenData['abilities'])->plainTextToken
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User Logged In Failed',
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
