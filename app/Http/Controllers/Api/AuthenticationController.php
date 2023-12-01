<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiHttpResponses;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreRegistryRequest;

class AuthenticationController extends Controller
{
    use ApiHttpResponses;
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
    public function login(LoginRequest $request)
    {
        $validatedData = $request->all();
        try {

            if (!Auth::attempt($validatedData)) {
                return $this->sendErrors([], "Invalid login credentials", 401);
            }

            $user = Auth::user();

            $token = $user->createToken('user_token')->plainTextToken;

            $results = [
                'user' => new UserResource($user),
                'token' => $token
            ];

            return $this->sendResponse($results, "Logged in", 200);

        } catch (\Exception $e) {
            return $this->sendErrors([], $e->getMessage(), 500);
        }

    }

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

    public function register(StoreRegistryRequest $request)
    {
        //Field validation
        try {
            $validatedData = $request->all();

            $user = User::create([
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'country_code' => $validatedData['country_code'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password'])
            ]);

            //Generating the user token
            $token = $user->createToken('user_token')->plainTextToken;

            $results = [
                "user" => new UserResource($user),
                "token" => $token
            ];

            return $this->sendResponse($results, "Created", 201);


        } catch (\Exception $e) {
            return $this->sendErrors([], $e->getMessage(), 500);
        }
    }
}
