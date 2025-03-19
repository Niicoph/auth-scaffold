<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException as ExceptionsJWTException;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(AuthService $authService)
    {
        $this->userService = $authService;
    }

    /**
     * logs a user
     * @param LoginRequest $loginRequest
     * @return \Illuminate\Http\JsonResponse // cookies with sessionID and refreshToken
     * @throws ExceptionsJWTException
     */
    public function login(LoginRequest $loginRequest)
    {
        try {
            $credentials = $loginRequest->only(['email', 'password', 'remember_me']);
            $response = $this->userService->login($credentials);
            if (!$response) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $token = $response['token'];
            $refreshToken = $response['refreshToken'];
            $user = $response['user'];
            return response()->json(['message' => 'Login Sucessful', 'user' => $user], 200)
                ->cookie('refreshToken', $refreshToken, 20160, null, null, false, true)
                ->cookie('sessionID', $token, 60, null, null, false, true);
        } catch (ExceptionsJWTException $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
    /**
     * log out a user
     * @return \Illuminate\Http\JsonResponse
     * @throws ExceptionsJWTException
     */
    public function logout(Request $request)
    {
        try {
            // 1. verificamos que la peticion tenga una cookie con el token
            if (!$request->hasCookie('sessionID')) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $response = $this->userService->logout();
            if (!$response) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return response()->json(['message' => 'Logout Sucessful'], 200)
                ->cookie('refreshToken', '', 0, null, null, false, true)
                ->cookie('sessionID', '', 0, null, null, false, true);
        } catch (ExceptionsJWTException $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    /**
     * refresh token
     * @return \Illuminate\Http\JsonResponse
     * @throws ExceptionsJWTException
     */
    public function refreshToken(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refreshToken');
            if (!$refreshToken) {
                return response()->json(['message' => 'No refresh token provided'], 401);
            }
            $response = $this->userService->refreshToken($refreshToken);
            if (!$response) {
                return response()->json(['message' => 'Invalid refresh token'], 401);
            }
            $newToken = $response['token'];
            return response()->json(['message' => 'Token refreshed'])->cookie('sessionID', $newToken, 60, '/', null, true, true);
        } catch (ExceptionsJWTException $e) {
            return response()->json(['message' => 'Could not refresh token'], 500);
        }
    }
}
