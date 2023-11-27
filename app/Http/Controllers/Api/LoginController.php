<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="User's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Login successful",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Invalid login credentials",
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal Server Error",
     *
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                "username" => ['required', 'string', 'max:25'],
                'password' => ['required']
            ]);

            if (!Auth::attempt($validatedData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid login credential!',
                    'data' => []
                ], 401);
            }

            $user = Auth::user();

            $token = $user->createToken('user_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successfully',
                'data' => [
                    'user' => $user,
                    'access_token' => $token
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e,
                'data' => []
            ], 500);
        }

    }
}
