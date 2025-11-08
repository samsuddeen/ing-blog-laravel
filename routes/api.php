<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;


// The prefix /api/v1/ is defined in bootstrap/app.php

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post:slug}', [PostController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{tag:slug}', [TagController::class, 'show']);


Route::middleware(['auth:sanctum'])->group(function () {


    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::apiResource('users', UserController::class);

    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
    Route::get('/my-posts', [PostController::class, 'myPosts']);
    Route::post('/posts/{post}/publish', [PostController::class, 'publish']);

    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

    Route::apiResource('tags', TagController::class)->except(['index', 'show']);

    Route::apiResource('comments', CommentController::class);
    Route::get('/posts/{post}/comments', [CommentController::class, 'postComments']);

});


