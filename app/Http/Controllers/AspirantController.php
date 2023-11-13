<?php

namespace App\Http\Controllers;

use App\Http\Resources\AspirantCreationRequestResource;
use App\Http\Resources\AspirantResource;
use App\Http\Resources\AspirantUpdateRequestResource;
use App\Models\Aspirant;
use App\Models\AspirantCreationRequest;
use App\Models\AspirantUpdateRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AspirantController extends Controller
{
    public function create(Request $request)
    {
        $fields = $request->validate([
            'address' => [
                'required',
                'string'
            ],
            'flyer' => [
                'required',
                'image',
                'max:2048'
            ],
            'constituency' => [
                'required',
                'integer',
                'exists:constituencies,id'
            ],
            'party' => [
                'required',
                'integer',
                'exists:parties,id'
            ],
            'position' => [
                'required',
                'integer',
                'exists:positions,id'
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $aspirant_creation_request = AspirantCreationRequest::create([
                'address' => $fields['address'],
                'flyer' => 'aspirants/flyers/' . $fields['flyer']->hashName(),
                'constituency_id' => $fields['constituency'],
                'party_id' => $fields['party'],
                'position_id' => $fields['position'],
                'user_id' => $request->user()->id
            ]);
    
            Storage::put(
                'aspirants/flyers/' . $fields['flyer']->hashName(),
                $fields['flyer']
            );
            $aspirant_creation_request->refresh();
            $aspirant_creation_request->load([
                'constituency' => [
                    'region'
                ],
                'party',
                'position',
                'user'
            ]);
    
            $aspirant_creation_request = new AspirantCreationRequestResource($aspirant_creation_request);

            DB::commit();
    
            return response()->json(compact($aspirant_creation_request));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function index(Request $request)
    {
        $aspirants = AspirantResource::collection(Aspirant::with([
            'constituency' => [
                'region'
            ],
            'party',
            'position',
            'user'
        ])->get());

        return response()->json(compact($aspirants));
    }

    public function indexCreationRequest(Request $request) {
        $aspirant_creation_requests = AspirantCreationRequestResource::collection(AspirantCreationRequest::with([
            'constituency' => [
                'region'
            ],
            'party',
            'position',
            'user'
        ])->get());

        return response()->json(compact($aspirant_creation_requests));
    }

    public function indexUpdateRequest(Request $request) {
        $aspirant_update_requests = AspirantUpdateRequestResource::collection(AspirantUpdateRequest::with([
            'aspirant' => [
                'user'
            ],
            'constituency' => [
                'region'
            ],
            'party',
            'position'
        ])->get());

        return response()->json(compact($aspirant_update_requests));
    }

    public function get(Request $request, Aspirant $aspirant)
    {
        $aspirant->load([
            'constituency' => [
                'region'
            ],
            'party',
            'position',
            'user'
        ]);

        $aspirant = new AspirantResource($aspirant);

        return response()->json(compact($aspirant));
    }

    public function getCreationRequest(Request $request, AspirantCreationRequest $aspirant_creation_request)
    {
        $aspirant_creation_request->load([
            'constituency' => [
                'region'
            ],
            'party',
            'position',
            'user'
        ]);

        $aspirant_creation_request = new AspirantCreationRequestResource($aspirant_creation_request);

        return response()->json(compact($aspirant_creation_request));
    }

    public function getUpdateRequest(Request $request, AspirantUpdateRequest $aspirant_update_request)
    {
        $aspirant_update_request->load([
            'aspirant' => [
                'user'
            ],
            'constituency' => [
                'region'
            ],
            'party',
            'position'
        ]);

        $aspirant_update_request = new AspirantUpdateRequestResource($aspirant_update_request);

        return response()->json(compact($aspirant_update_request));
    }

    public function update(Request $request)
    {
        $fields = $request->validate([
            'address' => [
                'required',
                'string'
            ],
            'flyer' => [
                'sometimes',
                'image',
                'max:2048'
            ],
            'constituency' => [
                'required',
                'integer',
                'exists:constituencies,id'
            ],
            'party' => [
                'required',
                'integer',
                'exists:parties,id'
            ],
            'position' => [
                'required',
                'integer',
                'exists:positions,id'
            ]
        ]);
        $aspirant = $request->user()->aspirant;

        try
        {
            DB::beginTransaction();

            Storage::delete(
                $aspirant->aspirantUpdateRequests()->whereNull('status')->update([
                    'status' => 'Declined',
                    'status_applied_at' => now()
                ])->map(fn($model) => $model->flyer)->toArry()
            );
    
            $aspirant_update_request = AspirantUpdateRequest::create([
                'address' => $fields['address'],
                'flyer' => $fields['flyer'] != null ? 'aspirants/flyers/' . $fields['flyer']->hashName() : $aspirant->flyer,
                'aspirant_id' => $aspirant->id,
                'constituency_id' => $fields['constituency'],
                'party_id' => $fields['party'],
                'position_id' => $fields['position']
            ]);
    
            if($fields['flyer'] != null)
            {
                Storage::put(
                    'aspirants/flyers/' . $fields['flyer']->hashName(),
                    $fields['flyer']
                );
            }
    
            $aspirant_update_request->refresh();
            $aspirant_update_request->load([
                'aspirant' => [
                    'user'
                ],
                'constituency' => [
                    'region'
                ],
                'party',
                'position'
            ]);
    
            $aspirant_update_request = new AspirantCreationRequestResource($aspirant_update_request);

            DB::commit();
    
            return response()->json(compact($aspirant_update_request));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function delete(Request $request, Aspirant $aspirant)
    {
        $aspirant->load([
            'constituency' => [
                'region'
            ],
            'party',
            'position',
            'user'
        ]);

        try
        {
            DB::beginTransaction();

            $aspirant->user->update([
                'role_id' => Role::where('name', 'Follower')->first()->id
            ]);
            $aspirant->delete();
            Storage::delete($aspirant->flyer);

            $aspirant = new AspirantResource($aspirant);

            DB::commit();

            return response()->json(compact($aspirant));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function confirmCreation(Request $request, AspirantCreationRequest $aspirant_creation_request)
    {
        $fields = $request->validate([
            'status' => [
                'required',
                'string',
                'in:Accepted,Declined'
            ]
        ]);
        $fields['status_applied_at'] = now();

        try
        {
            DB::beginTransaction();
            
            $aspirant_creation_request->update($fields);
            $aspirant_creation_request->load([
                'constituency' => [
                    'region'
                ],
                'party',
                'position',
                'user'
            ]);

            $aspirant = null;

            if($aspirant_creation_request->status == 'Accepted')
            {
                $aspirant_creation_request->user->update([
                    'role_id' => Role::where('name', 'Aspirant')->first()->id
                ]);
                
                $aspirant = Aspirant::create([
                    'address' => $aspirant_creation_request->address,
                    'flyer' => $aspirant_creation_request->flyer,
                    'constituency_id' => $aspirant_creation_request->constituency_id,
                    'party_id' => $aspirant_creation_request->party_id,
                    'position_id' => $aspirant_creation_request->position_id,
                    'user_id' => $aspirant_creation_request->user_id,
                ]);

                $aspirant->refresh();
                $aspirant->load([
                    'constituency' => [
                        'region'
                    ],
                    'party',
                    'position',
                    'user'
                ]);

                $aspirant = new AspirantResource($aspirant);
            }
            else
            {
                Storage::delete($aspirant_creation_request->flyer);
            }

            $aspirant_creation_request = new AspirantUpdateRequestResource($aspirant_creation_request);
            
            DB::commit();

            return response()->json(compact($aspirant, $aspirant_creation_request));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function confirmUpdate(Request $request, AspirantUpdateRequest $aspirant_update_request)
    {
        $fields = $request->validate([
            'status' => [
                'required',
                'string',
                'in:Accepted,Declined'
            ]
        ]);
        $fields['status_applied_at'] = now();

        try
        {
            DB::beginTransaction();
            
            $aspirant_update_request->update($fields);
            $aspirant_update_request->load([
                'aspirant' => [
                    'user'
                ],
                'constituency' => [
                    'region'
                ],
                'party',
                'position'
            ]);

            $aspirant = null;

            if($aspirant_update_request->status == 'Accepted')
            {
                $aspirant = $aspirant_update_request->aspirant->update([
                    'address' => $aspirant_update_request->address,
                    'flyer' => $aspirant_update_request->flyer,
                    'constituency_id' => $aspirant_update_request->constituency_id,
                    'party_id' => $aspirant_update_request->party_id,
                    'position_id' => $aspirant_update_request->position_id,
                ]);

                $aspirant->refresh();
                $aspirant->load([
                    'constituency' => [
                        'region'
                    ],
                    'party',
                    'position',
                    'user'
                ]);

                $aspirant = new AspirantResource($aspirant);
            }
            else
            {
                Storage::delete($aspirant_update_request->flyer);
            }

            $aspirant_update_request = new AspirantUpdateRequestResource($aspirant_update_request);
            
            DB::commit();

            return response()->json(compact($aspirant, $aspirant_update_request));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }
}
