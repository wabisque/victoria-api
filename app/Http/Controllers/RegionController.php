<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class RegionController extends Controller
{
    public function create(Request $request)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                'unique:regions'
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $region = Region::create($fields);
    
            $region->refresh();
            $region->load('constituencies');
    
            $region = new RegionResource($region);
    
            DB::commit();
    
            return response()->json(compact('region'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function index(Request $request)
    {
        $regions = RegionResource::collection(Region::with('constituencies')->latest()->get());

        return response()->json(compact('regions'));
    }

    public function get(Request $request, Region $region)
    {
        $region->load('constituencies');
        
        $region = new RegionResource($region);

        return response()->json(compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('regions')->ignore($region)
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $region->update($fields);
            $region->load('constituencies');
    
            $region = new RegionResource($region);
    
            DB::commit();
    
            return response()->json(compact('region'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function delete(Request $request, Region $region)
    {
        try
        {
            DB::beginTransaction();

            $region->load('constituencies');
            $region->constituencies->each(fn($constituency) => $constituency->delete());
            $region->delete();
    
            $region = new RegionResource($region);
    
            DB::commit();
    
            return response()->json(compact('region'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }
}
