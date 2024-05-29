<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Support\Exceptions\OAuthException;
use App\Support\Traits\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * @OA\Info(
 *    title="CyberElysium",
 *    version="1.0.0",
 *    description="API endpoints Students and Authentication"
 * ),
 *
 * @OA\SecurityScheme(
 *       securityScheme="bearerAuth",
 *       in="header",
 *       name="bearerAuth",
 *       type="http",
 *       scheme="bearer",
 *       bearerFormat="JWT"
 *    )
 */
class AuthController extends Controller
{
    use Authenticatable;

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     description="Create a new user account",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Validate the request

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Log the user in and generate token
        $token = Auth::login($user);

        // Return the token in the response
        return $this->responseWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     description="Login and get a JWT token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!$token = Auth::attempt($request->only('email', 'password'))) {
            throw new OAuthException('invalid_credentials_provided');
        }

        return $this->responseWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     summary="Refresh token",
     *     description="Refresh the JWT token",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function refresh(): JsonResponse
    {
        return $this->responseWithToken(auth()->refresh());
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="User logout",
     *     description="Logout the user and invalidate the token",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return new JsonResponse(['success' => true]);
    }

    /**
     * Return a response with a token.
     *
     * @param string $token
     * @return JsonResponse
     */
    protected function responseWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
