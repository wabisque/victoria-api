<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Throwable;

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

        $user = User::where('email', $fields['id'])->orWhere('phone_number', $fields['id'])->with('aspirant', 'role')->first();

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
                'unique:users'
            ],
            'phone_number' => [
                'required',
                'string',
                'numeric',
                'min:10',
                'max:10',
                'unique:users'
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
        $user->load('aspirant', 'role');

        $token = $user->createToken('api')->plainTextToken;
        $user = new UserResource($user);

        return response()->json(compact($user, $token));
    }

    public function user(Request $request)
    {
        $user = $request->user();

        $user->load('aspirant', 'role');

        $user = new UserResource($request->user());
        
        return response()->json(compact($user));
    }

    public function updateDetails(Request $request)
    {
        $user = $request->user();
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3'
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user)
            ],
            'phone_number' => [
                'required',
                'string',
                'numeric',
                'min:10',
                'max:10',
                Rule::unique('users')->ignore($user)
            ],
        ]);

        try
        {
            DB::beginTransaction();

            $user->update($fields);
            $user->load('aspirant', 'role');

            $user = new UserResource($request->user());
            
            DB::commit();
            
            return response()->json(compact($user));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $fields = $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::defaults()
            ],
            'current_password' => [
                'required',
                'current_password'
            ]
        ]);
        $fields['password'] = Hash::make($fields['password']);

        unset($fields['current_password']);

        try
        {
            DB::beginTransaction();

            $user->update($fields);
            $user->load('aspirant', 'role');

            $user = new UserResource($request->user());
            
            DB::commit();
            
            return response()->json(compact($user));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }
}
