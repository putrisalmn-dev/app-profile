<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    /**
     * List published posts with optional category, featured, and search filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $posts = Post::query()
            ->published()
            ->with(['category', 'author'])
            ->when(
                $request->filled('category'),
                fn (Builder $query) => $query->whereHas(
                    'category',
                    fn (Builder $categoryQuery) => $categoryQuery->where('slug', $request->string('category')->toString()),
                ),
            )
            ->when($request->filled('featured'), fn (Builder $query) => $query->where('is_featured', $request->boolean('featured')))
            ->when(
                $request->filled('search'),
                fn (Builder $query) => $query->where(function (Builder $searchQuery) use ($request): void {
                    $term = '%'.$request->string('search')->toString().'%';
                    $searchQuery->where('title', 'like', $term)
                        ->orWhere('summary', 'like', $term);
                }),
            )
            ->orderByDesc('published_at')
            ->paginate($perPage);

        return PostResource::collection($posts);
    }

    /**
     * Display a single published post and increment its view counter.
     */
    public function show(string $slug): PostResource
    {
        $post = Post::query()
            ->published()
            ->with(['category', 'author'])
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('view_count');

        return PostResource::make($post);
    }
}
