<?php

namespace App\Models;

use App\Enums\PageStatus;
use App\Models\Concerns\GeneratesSlug;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'slug', 'content', 'image', 'status', 'sort_order', 'show_in_menu'])]
class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use GeneratesSlug, HasFactory;

    protected $attributes = [
        'status' => 'draft',
    ];

    protected static function booted(): void
    {
        static::creating(function (Page $page): void {
            $page->user_id ??= auth()->id();
        });
    }

    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
            'sort_order' => 'integer',
            'show_in_menu' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', PageStatus::Published);
    }
}
