<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Models\Concerns\GeneratesSlug;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'category_id', 'title', 'slug', 'summary', 'content', 'image',
    'status', 'published_at', 'is_featured',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use GeneratesSlug, HasFactory;

    protected $attributes = [
        'status' => 'draft',
        'view_count' => 0,
        'is_featured' => false,
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $post): void {
            $post->user_id ??= auth()->id();
        });

        static::saving(function (Post $post): void {
            if ($post->status === PostStatus::Published && $post->published_at === null) {
                $post->published_at = now();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'view_count' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Posts that are published and whose publish date has been reached.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
