<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = RoleResource::collection(Role::all());

        return response()->json(compact($roles));
    }

    public function get(Request $request, Role $role)
    {
        $role = new RoleResource($role);

        return response()->json(compact($role));
    }
}
