<?php

namespace App\Http\Controllers;

use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Throwable;

class PositionController extends Controller
{
    public function create(Request $request)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                'unique:positions'
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $position = Position::create($fields);
    
            $position->refresh();
    
            $position = new PositionResource($position);
    
            DB::commit();
    
            return response()->json(compact($position));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function index(Request $request)
    {
        $positions = PositionResource::collection(Position::all());

        return response()->json(compact($positions));
    }

    public function get(Request $request, Position $position)
    {
        $position = new PositionResource($position);

        return response()->json(compact($position));
    }

    public function update(Request $request, Position $position)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('positions')->ignore($position)
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $position->update($fields);
            
            $position = new PositionResource($position);
    
            DB::commit();
    
            return response()->json(compact($position));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function delete(Request $request, Position $position)
    {
        if($position->aspirants()->exists())
        {
            throw new NotFoundResourceException();
        }

        try
        {
            DB::beginTransaction();

            $position->delete();
    
            $position = new PositionResource($position);
    
            DB::commit();
    
            return response()->json(compact($position));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }
}
