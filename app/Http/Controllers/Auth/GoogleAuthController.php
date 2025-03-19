<?php

namespace App\Http\Controllers\Auth;

use App\Services\Auth\GoogleAuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    protected $googleAuthService;
    public function __construct(GoogleAuthService $googleAuthService)
    {
        $this->googleAuthService = $googleAuthService;
    }


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $response = $this->googleAuthService->handleGoogleCallback();
            if (!$response) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $token = $response['token'];
            // $user = $response['user'];
            return Redirect::to('https://www.example.com/dashboard')
                ->withCookie(cookie('sessionID', $token, 60, '/', null, true, 'Lax'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
