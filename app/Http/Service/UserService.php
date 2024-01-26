<?php

namespace App\Http\Service;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;

class UserService
{

    public function createFromRequest(RegistrationRequest $request): User
    {
        $user = new User();
        $user->email = $request->get('email');
        $user->password = $request->get('password');
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->save();

        return $user;
    }
}
