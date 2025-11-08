<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PostResource;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;

class PostController extends Controller
{
    
    public function index(Request $request)
{
    try {
        $query = Post::with(['user', 'category', 'tags', 'comments'])->published();

        if ($request->filled('search')) {
            $query = $query->search($request->search);
        }

        if ($request->filled('author')) {
            $query = $query->byAuthor($request->author);
        }

        if ($request->filled('category')) {
            $query = $query->byCategory($request->category);
        }

        if ($request->filled('tag')) {
            $query = $query->byTag($request->tag);
        }

        $posts = $query->latest()
            ->paginate($request->get('per_page', 10));

        return PostResource::collection($posts)
            ->additional(['success' => true]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
        ], 500);
    }
}



    public function store(StorePostRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $post = Post::create($data);

            if (!empty($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            DB::commit();

            return new PostResource($post->load(['user', 'category', 'tags']));
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function show(Post $post)
    {
        try {
            return new PostResource($post->load(['user', 'category', 'tags', 'comments']));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function update(UpdatePostRequest $request, Post $post)
    {
        if (!$request->user()->isAdmin() && !$post->isOwnedBy($request->user())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $post->update($data);

            if (isset($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            DB::commit();
            return new PostResource($post->load(['user', 'category', 'tags']));
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(Request $request, Post $post)
    {
        if (!$request->user()->isAdmin() && !$post->isOwnedBy($request->user())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $post->tags()->detach();
            $post->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function myPosts(Request $request)
    {
        try {
            $posts = Post::with(['category', 'tags', 'comments'])
                ->where('user_id', $request->user()->id)
                ->latest()
                ->paginate($request->get('per_page', 10));

            return PostResource::collection($posts)
                ->additional(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function publish(Request $request, Post $post)
    {
        if (!$request->user()->isAdmin() && !$post->isOwnedBy($request->user())) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $post->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
            DB::commit();

            return new PostResource($post->load(['user', 'category', 'tags']));
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
}
