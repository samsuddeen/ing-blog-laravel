<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Models\Post;
use App\Http\Resources\CommentResource;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
 use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class CommentController extends Controller
{



public function index(Request $request)
{
    try {
        $comments = QueryBuilder::for(Comment::class)
            ->with(['user', 'commentable'])
            ->allowedFilters([
                AllowedFilter::partial('content'), 
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('commentable_id'),
                AllowedFilter::exact('commentable_type'),
            ])
            ->allowedSorts(['created_at', 'updated_at'])
            ->latest()
            ->paginate($request->get('per_page', 10))
            ->appends($request->query());

        return CommentResource::collection($comments)
            ->additional(['success' => true]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
        ], 500);
    }
}


    public function postComments(Post $post, Request $request)
    {
        try {
            $comments = $post->comments()
                ->with('user')
                ->latest()
                ->paginate($request->get('per_page', 10));

            return CommentResource::collection($comments)
                ->additional(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function store(StoreCommentRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $comment = Comment::create($data);
            DB::commit();

            return new CommentResource($comment->load(['user', 'commentable']));
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $user = $request->user();
        if (!$user->isAdmin() && !$user->ownsComment($comment) && !$user->ownsPost($comment->commentable)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $comment->update($request->validated());
            DB::commit();

            return new CommentResource($comment->load(['user', 'commentable']));
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(Request $request, Comment $comment)
    {
        $user = $request->user();
        if (!$user->isAdmin() && !$user->ownsComment($comment) && !$user->ownsPost($comment->commentable)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            $comment->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function show(Comment $comment)
    {
        try {
            return new CommentResource($comment->load(['user', 'commentable']));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
}
