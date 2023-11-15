<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConstituencyResource;
use App\Models\Constituency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Throwable;

class ConstituencyController extends Controller
{
    public function create(Request $request)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                'unique:constituencies'
            ],
            'region' => [
                'required',
                'integer',
                'exists:regions,id'
            ],
        ]);
        $fields['region_id'] = $fields['region'];

        unset($fields['region']);

        try
        {
            DB::beginTransaction();

            $constituency = Constituency::create($fields);

            $constituency->refresh();
            $constituency->load('region');

            $constituency = new ConstituencyResource($constituency);

            DB::commit();

            return response()->json(compact('constituency'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function index(Request $request)
    {
        $constituencies = ConstituencyResource::collection(Constituency::with('region')->latest()->get());

        return response()->json(compact('constituencies'));
    }

    public function get(Request $request, Constituency $constituency)
    {
        $constituency->load('region');

        $constituency = new ConstituencyResource($constituency);

        return response()->json(compact('constituency'));
    }

    public function update(Request $request, Constituency $constituency)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('constituencies')->ignore($constituency)
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $constituency->update($fields);
            $constituency->load('region');

            $constituency = new ConstituencyResource($constituency);

            DB::commit();

            return response()->json(compact('constituency'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function delete(Request $request, Constituency $constituency)
    {
        if(Constituency::where('id', $constituency->id)->where(fn($q) => $q->has('aspirants')->orWhereHas('aspirantCreationRequests', fn($q) => $q->whereNull('status'))->orWhereHas('aspirantUpdateRequests', fn($q) => $q->whereNull('status')))->exists())
        {
            throw new NotFoundResourceException();
        }

        try
        {
            DB::beginTransaction();

            $constituency->load('region');
            $constituency->delete();

            $constituency = new ConstituencyResource($constituency);

            DB::commit();

            return response()->json(compact('constituency'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }
}
