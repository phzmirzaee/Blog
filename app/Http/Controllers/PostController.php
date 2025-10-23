<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function store(StorePostRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $post = Post::create([
            'title' => $validatedData['title'],
            'author' => $validatedData['author'],
        ]);
        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
        ]);
    }


    public function index(): JsonResponse
    {
        $posts = Post::all();
        return response()->json($posts);
    }


    public function show(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        return response()->json([
            'title' => $post->title,
            'author' => $post->author,
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        return response()->json([
            'title' => $post->title,
            'author' => $post->author,
        ]);
    }

    public function update(StorePostRequest $request, int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $validatedData = $request->validated();
        $post->update([
            'title' => $validatedData['title'],
            'author' => $validatedData['author'],
        ]);
        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
        ]);
    }

}
