<?php

namespace App\Services\Auth;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthService
{
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle Google callback
     *
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = $this->userRepository->updateOrCreate($googleUser);
            $token = Auth::guard('api')->login($user);
            return ['token' => $token, 'user' => $user];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error', 'error' => $e->getMessage()]);
        }
    }
}
