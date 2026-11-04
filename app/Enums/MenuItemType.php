<?php

namespace App\Enums;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use Filament\Support\Contracts\HasLabel;

enum MenuItemType: string implements HasLabel
{
    case Page = 'page';
    case Post = 'post';
    case Category = 'category';
    case Custom = 'custom';

    public function getLabel(): string
    {
        return match ($this) {
            self::Page => __('Page'),
            self::Post => __('Post'),
            self::Category => __('Category'),
            self::Custom => __('Custom URL'),
        };
    }

    /**
     * The Eloquent model class the menu item links to, if any.
     *
     * @return class-string<Page|Post|Category>|null
     */
    public function modelClass(): ?string
    {
        return match ($this) {
            self::Page => Page::class,
            self::Post => Post::class,
            self::Category => Category::class,
            self::Custom => null,
        };
    }

    /**
     * The column used as a human readable title for the linked model.
     */
    public function titleColumn(): ?string
    {
        return match ($this) {
            self::Page, self::Post => 'title',
            self::Category => 'name',
            self::Custom => null,
        };
    }
}
