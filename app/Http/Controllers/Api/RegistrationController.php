<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="User's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="Password confirmation",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */

    public function register(Request $request)
    {
        //Field validation
        try {
            $validatedData = $request->validate([
                'username' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:users,email', 'max:255'],
                'password' => ['required', 'min:8', 'confirmed']
            ]);

            //User creation with user model
            $user = User::create([
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password'])
            ]);

            //Generating the user token
            $token = $user->createToken('user_token')->plainTextToken;

            //Return json response for registration
            return response()->json([
                "success" => true,
                "message" => "Registration completed",
                "data" => [
                    "user" => $user,
                    "token" => $token
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                "success" => false,
                "message" => "Validation errors",
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "An error occurred during registration"
            ], 500);
        }
    }
}
