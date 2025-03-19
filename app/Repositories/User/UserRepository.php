<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository
{
    /**
     * Create a new user
     * @param array $data
     * @return User
     */
    public function create($data)
    {
        return User::create($data);
    }

    /**
     * Update or create a user
     * @param array $data // google user data
     */
    public function updateOrCreate($googleUser)
    {
        return User::updateOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'provider_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ]);
    }
}
