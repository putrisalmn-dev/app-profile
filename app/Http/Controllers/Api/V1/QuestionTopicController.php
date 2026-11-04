<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\QuestionTopicResource;
use App\Models\QuestionTopic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionTopicController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $topics = QuestionTopic::query()
            ->active()
            ->withCount([
                'faqs' => fn (Builder $query) => $query->active(),
                'questions' => fn (Builder $query) => $query->publiclyVisible(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return QuestionTopicResource::collection($topics);
    }
}
