<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function store(StorePostRequest $request): PostResource
    {
        $user=auth()->user();
        if ($user->role === 'user') {
            $user->role = 'author';
            $user->save();
        }
        $validatedData = $request->validated();
        $post = Post::create([
            'title' => $validatedData['title'],
            'author' => $validatedData['author'],
            'user_id'=>auth()->id(),
        ]);
        return (new PostResource($post))->additional([
            'message'=>'پست با موفقیت ساخته شد'
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


    public function update(StorePostRequest $request, int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $user=auth()->user();
        if ($user->id !== $post->user_id && $user->role !== 'admin') {
            return response()->json(['error' => 'شما مجاز به ویرایش این پست نیستید.'], 403);
        }
        $validatedData = $request->validated();
        $post->update([
            'title' => $validatedData['title'],
            'author' => $validatedData['author'],
        ]);
        return response()->json([
            'message' => 'پست با موفقیت ویرایش شد',
            'post' => $post,
        ]);
    }

}
