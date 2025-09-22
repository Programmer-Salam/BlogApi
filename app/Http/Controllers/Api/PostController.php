<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    use ApiResponses;

 public function index()
    {
        $posts = Post::with('author')
            ->byAuthor(auth()->id())
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }
    public function store(PostRequest $request): JsonResponse
    {
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'author_id' => auth()->id(),
        ]);

        return $this->resourceResponse(
            new PostResource($post->load('author')),
            'Post created successfully',
            201
        );
    }

    public function show($id): JsonResponse
    {
    $post = Post::with('author')->find($id);

    if (!$post) {
        return response()->json([
            'message' => 'Post not found'
        ], 404);
    }
        return $this->resourceResponse(
            new PostResource($post->load('author'))
        );
    }

    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $post->update($request->validated());

        return $this->resourceResponse(
            new PostResource($post->load('author')),
            'Post updated successfully'
        );
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return $this->successResponse(
            null,
            'Post deleted successfully'
        );
    }
}
