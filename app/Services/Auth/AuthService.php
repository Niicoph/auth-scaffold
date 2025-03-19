<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException as ExceptionsJWTException;
use Illuminate\Support\Str;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * log in a user
     * @param $credentials
     * @return array|bool
     */
    public function login($credentials)
    {
        try {
            $token = Auth::attempt($credentials);

            if (!$token) {
                return false;
            }

            $user = Auth::user();
            $refreshToken = Str::random(60);
            $userAuth = User::find($user->id);
            $userAuth->refresh_token = $refreshToken;
            $userAuth->save();

            return ['token' => $token, 'refreshToken' => $refreshToken, 'user' => $user];
        } catch (ExceptionsJWTException $e) {
            return false;
        }
    }

    /**
     * log out a user
     * @return bool
     */
    public function logout()
    {
        try {
            Auth::logout();
            return true;
        } catch (ExceptionsJWTException $e) {
            return false;
        }
    }

    /**
     * refresh a token
     * @param $refreshToken
     * @return array|bool
     */
    public function refreshToken($refreshToken)
    {
        try {
            $user = User::where('refresh_token', $refreshToken)->first();
            if (!$user) {
                return false;
            }
            $newToken = Auth::login($user);
            return ['token' => $newToken];
        } catch (ExceptionsJWTException $e) {
            return false;
        }
    }
    /**
     * validate a token
     * @param $token
     * @return bool
     */
    public function validateToken($token)
    {
        try {
            $user = JWTAuth::setToken($token)->authenticate();
            if (!$user) {
                return false;
            }
            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }
}
