<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PublicPostController extends Controller
{
  public function index(Request $request)
{
    $search = $request->query('search');
    $posts = Post::with('author')
        ->when($search, function ($query, $search) {
            return $query->search($search);
        })
        ->latest()
        ->paginate(10);

    return response()->json([
        'success' => true,
        'data' => PostResource::collection($posts)->items(),
        'meta' => [
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total' => $posts->total(),
        ]
    ]);
}

    public function show($id)
    {
        $post = Post::with('author')->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return new PostResource($post);
    }
}
