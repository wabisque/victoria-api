<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Throwable;

class PostController extends Controller
{
    public function create(Request $request)
    {
        $fields = $request->validate([
            'title' => [
                'required',
                'string'
            ],
            'body' => [
                'required',
                'string'
            ]
        ]);
        $fields['aspirant_id'] = $request->user()->aspirant->id;

        try
        {
            DB::beginTransaction();

            $post = Post::create($fields);

            $post->refresh();
            $post->load([
                'aspirant' => [
                    'constituency' => [
                        'region'
                    ],
                    'party',
                    'position',
                    'user'
                ]
            ]);

            $post = new PostResource($post);

            DB::commit();

            return response()->json(compact('post'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function index(Request $request)
    {
        if($request->user()->role()->where('name', 'Aspirant')->exists())
        {
            $posts = $request->user()->aspirant->posts()->with([
                'aspirant' => [
                    'constituency' => [
                        'region'
                    ],
                    'party',
                    'position',
                    'user'
                ]
            ])->latest()->get();
        }
        else if($request->user()->role()->where('name', 'Follower')->exists())
        {
            $posts = Post::with([
                'aspirant' => [
                    'constituency' => [
                        'region'
                    ],
                    'party',
                    'position',
                    'user'
                ]
            ])->whereIn(
                'aspirant_id',
                $request->user()->followedAspirants->map(fn($model) => $model->id)
            )->latest()->get();
        }
        else
        {
            $posts = Post::with([
                'aspirant' => [
                    'constituency' => [
                        'region'
                    ],
                    'party',
                    'position',
                    'user'
                ]
            ])->get();
        }

        $posts = PostResource::collection($posts);

        return response()->json(compact('posts'));
    }

    public function get(Request $request, Post $post)
    {
        if($request->user()->role()->where('name', 'Aspirant')->exists() && $post->aspirant_id != $request->user()->aspirant->id)
        {
            throw new NotFoundResourceException();
        }
        
        if($request->user()->role()->where('name', 'Follower')->exists() && $request->user()->followedAspirants()->where('id', $post->aspirant_id)->doesntExist())
        {
            throw new NotFoundResourceException();
        }

        $post->load([
            'aspirant' => [
                'constituency' => [
                    'region'
                ],
                'party',
                'position',
                'user'
            ]
        ]);

        $post = new PostResource($post);

        return response()->json(compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $fields = $request->validate([
            'title' => [
                'required',
                'string'
            ],
            'body' => [
                'required',
                'string'
            ]
        ]);

        try
        {
            DB::beginTransaction();

            $post->update($fields);
            $post->load([
                'aspirant' => [
                    'constituency' => [
                        'region'
                    ],
                    'party',
                    'position',
                    'user'
                ]
            ]);

            $post = new PostResource($post);

            DB::commit();

            return response()->json(compact('post'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }

    public function delete(Request $request, Post $post)
    {
        try
        {
            DB::beginTransaction();

            $post->load([
                'aspirant' => [
                    'constituency' => [
                        'region'
                    ],
                    'party',
                    'position',
                    'user'
                ]
            ]);
            $post->delete();

            $post = new PostResource($post);

            DB::commit();

            return response()->json(compact('post'));
        }
        catch(Throwable $th)
        {
            DB::rollBack();

            throw $th;
        }
    }
}
