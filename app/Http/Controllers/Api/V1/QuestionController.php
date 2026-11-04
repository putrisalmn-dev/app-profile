<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\QuestionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreQuestionRequest;
use App\Http\Resources\Api\V1\QuestionResource;
use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController extends Controller
{
    /**
     * List answered public questions with their published answers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $questions = Question::query()
            ->publiclyVisible()
            ->with(['topic', 'publishedAnswers.author'])
            ->when(
                $request->filled('topic'),
                fn (Builder $query) => $query->whereHas(
                    'topic',
                    fn (Builder $topicQuery) => $topicQuery->where('slug', $request->string('topic')->toString())
                        ->orWhereKey($request->integer('topic')),
                ),
            )
            ->when(
                $request->filled('search'),
                fn (Builder $query) => $query->where(function (Builder $searchQuery) use ($request): void {
                    $term = '%'.$request->string('search')->toString().'%';
                    $searchQuery->where('subject', 'like', $term)
                        ->orWhere('body', 'like', $term);
                }),
            )
            ->latest()
            ->paginate($perPage);

        return QuestionResource::collection($questions);
    }

    /**
     * Submit a new question from a member of the public.
     */
    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $question = Question::query()->create([
            ...$request->safe()->only(['topic_id', 'name', 'email', 'phone', 'subject', 'body']),
            'status' => QuestionStatus::Pending,
            'ip_address' => $request->ip(),
        ]);

        $question->load('topic');

        return QuestionResource::make($question)
            ->additional(['message' => 'Pertanyaan Anda telah diterima dan sedang menunggu tanggapan petugas.'])
            ->response()
            ->setStatusCode(201);
    }
}
