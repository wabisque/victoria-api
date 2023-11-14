<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartyResource;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Throwable;

class PartyController extends Controller
{
    public function create(Request $request)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                'unique:parties'
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $party = Party::create($fields);

            $party->refresh();

            $party = new PartyResource($party);

            DB::commit();

            return response()->json(compact('party'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function index(Request $request)
    {
        $parties = PartyResource::collection(Party::all());

        return response()->json(compact('parties'));
    }

    public function get(Request $request, Party $party)
    {
        $party = new PartyResource($party);

        return response()->json(compact('party'));
    }

    public function update(Request $request, Party $party)
    {
        $fields = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('parties')->ignore($party)
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $party->update($fields);

            $party = new PartyResource($party);

            DB::commit();

            return response()->json(compact('party'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function delete(Request $request, Party $party)
    {
        if($party->aspirants()->exists())
        {
            throw new NotFoundResourceException();
        }

        try
        {
            DB::beginTransaction();

            $party->delete();

            $party = new PartyResource($party);

            DB::commit();

            return response()->json(compact('party'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }
}
