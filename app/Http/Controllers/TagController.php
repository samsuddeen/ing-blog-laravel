<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TagResource;
use Spatie\QueryBuilder\AllowedFilter;
 use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;

class TagController extends Controller
{


public function index(Request $request)
{
    try {
        $tags = QueryBuilder::for(Tag::class)
            ->withCount('posts')
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['name', 'created_at', 'posts_count'])
            ->latest()
            ->paginate($request->get('per_page', 10))
            ->appends($request->query());

        return TagResource::collection($tags)
            ->additional([
                'success' => true,
            ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage()
        ], 500);
    }
}


    public function show(Tag $tag)
    {
        try {
            $tag->loadCount('posts');
            return response()->json([
                'success' => true,
                'data' => new TagResource($tag)
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage()
            ], 500);
        }
    }

    public function store(StoreTagRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $tag = Tag::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tag created successfully',
                'data' => new TagResource($tag)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        DB::beginTransaction();
        try {
            $tag->update(array_filter([
                'name' => $request->name,
                'slug' => $request->name ? Str::slug($request->name) : null,
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tag updated successfully',
                'data' => new TagResource($tag)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage()
            ], 500);
        }
    }

    public function destroy(Tag $tag)
    {
        DB::beginTransaction();
        try {
            $tag->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: '.$e->getMessage()
            ], 500);
        }
    }
}
