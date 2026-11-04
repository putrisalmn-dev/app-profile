<?php

namespace App\Models;

use App\Enums\MenuItemType;
use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['menu_id', 'parent_id', 'label', 'url', 'type', 'linkable_id', 'linkable_type', 'sort_order', 'is_active'])]
class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory;

    protected $attributes = [
        'type' => 'custom',
    ];

    protected static function booted(): void
    {
        static::saving(function (MenuItem $item): void {
            if ($item->parent_id !== null && $item->menu_id === null) {
                $item->menu_id = static::query()->whereKey($item->parent_id)->value('menu_id');
            }

            if ($item->type === MenuItemType::Custom) {
                $item->linkable_type = null;
                $item->linkable_id = null;

                return;
            }

            $item->linkable_type = $item->type->value;
            $item->url = null;
        });
    }

    protected function casts(): array
    {
        return [
            'type' => MenuItemType::class,
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Menu, $this>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * @return BelongsTo<MenuItem, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The path or URL the item points to. Internal links are expressed as app paths
     * (e.g. `/pages/tentang-kami`) so the Flutter client can route them itself.
     */
    public function resolvedUrl(): ?string
    {
        if ($this->type === MenuItemType::Custom) {
            return $this->url;
        }

        $slug = $this->linkable?->getAttribute('slug');

        if ($slug === null) {
            return null;
        }

        return match ($this->type) {
            MenuItemType::Page => "/pages/{$slug}",
            MenuItemType::Post => "/posts/{$slug}",
            MenuItemType::Category => "/categories/{$slug}",
        };
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
