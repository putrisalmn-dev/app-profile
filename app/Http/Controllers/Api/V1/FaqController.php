<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FaqResource;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FaqController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $faqs = Faq::query()
            ->active()
            ->with('topic')
            ->whereHas('topic', fn (Builder $query) => $query->active())
            ->when(
                $request->filled('topic'),
                fn (Builder $query) => $query->whereHas(
                    'topic',
                    fn (Builder $topicQuery) => $topicQuery->where('slug', $request->string('topic')->toString())
                        ->orWhereKey($request->integer('topic')),
                ),
            )
            ->orderBy('sort_order')
            ->get();

        return FaqResource::collection($faqs);
    }
}
