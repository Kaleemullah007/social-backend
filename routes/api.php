<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('varification', [AuthController::class, 'varification'])->name('varification');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('posts/search', [PostController::class, 'searchPost'])->name('posts.search');     
    Route::post('posts/like', [PostController::class, 'likes'])->name('posts.like');      
    Route::get('posts/dbfeedposts', [PostController::class, 'showAll'])->name('posts.dbfeedposts');      
    Route::apiResource('posts',PostController::class);    
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});