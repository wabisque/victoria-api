<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users',
            ],
            'password' => [
                'required',
                'string'
            ]
        ]);

        if(auth()->attempt($fields)) {
            $user = User::where('email', $fields['email'])->first();
            $token = $user->createToken('api')->plainTextToken;

            return response()->json(compact($user, $token));
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response();
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3'
            ],
            'email' => [
                'required',
                'email',
                'distinct:users'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password'])
        ]);
        $token = $user->createToken('api')->plainTextToken;

        return response()->json(compact($user, $token));
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}
