<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {

        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        // Check email
        $user = User::where('email', $fields['email'])->first();
        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {

            return $this->error('', 'Credentials do not match', 401);
        }

        $token = $user->createToken('API Token '.$user->name)->plainTextToken;


        return $this->success([
            'user' => $user,
            'token' => $token,
        ]);

    }
    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('API Token '.$user->name)->plainTextToken;
        return $this->success([
            'user' => $user,
            'token' => $token,
        ]);
    }
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
//        $request->user()->currentAccessToken()->delete();
        return $this->success('','logged out with success', 200);
    }
}
