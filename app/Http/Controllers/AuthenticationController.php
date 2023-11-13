<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
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
            'id' => [
                'required',
                'string'
            ],
            'password' => [
                'required',
                'string'
            ]
        ]);

        $user = User::where('email', $fields['id'])->orWhere('phone_number', $fields['id'])->with('roles')->first();

        if($user != null && Hash::check($fields['password'], $user->password)) {
            $token = $user->createToken('api')->plainTextToken;
            $user = new UserResource($user);

            return response()->json(compact($user, $token));
        }

        throw ValidationException::withMessages([
            'id' => 'The provided credentials do not match our records.'
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
            'phone_number' => [
                'required',
                'string',
                'numeric',
                'min:10',
                'max:10',
                'distinct:users'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ]
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'phone_number' => $fields['phone_number'],
            'password' => Hash::make($fields['password'])
        ]);
        
        $user->refresh();
        $user->load('roles');

        $token = $user->createToken('api')->plainTextToken;
        $user = new UserResource($user);

        return response()->json(compact($user, $token));
    }

    public function user(Request $request)
    {
        $user = $request->user();

        $user->load('roles');

        $user = new UserResource($request->user());
        
        return response()->json(compact($user));
    }
}
