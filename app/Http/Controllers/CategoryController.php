<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class CategoryController extends Controller
{


public function index(Request $request)
{
    try {
        $categories = QueryBuilder::for(Category::class)
            ->withCount('posts')
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('description'),
            ])
            ->allowedSorts(['name', 'created_at', 'posts_count'])
            ->latest()
            ->paginate($request->get('per_page', 10))
            ->appends($request->query());

        return CategoryResource::collection($categories)
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



    public function show(Category $category)
    {
        try {
            $category->loadCount('posts');
            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category)
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => new CategoryResource($category)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        DB::beginTransaction();
        try {
            $category->update(array_filter([
                'name' => $request->name,
                'slug' => $request->name ? Str::slug($request->name) : null,
                'description' => $request->description,
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => new CategoryResource($category)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        DB::beginTransaction();
        try {
            $category->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}
