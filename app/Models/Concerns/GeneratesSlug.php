<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Automatically fills a unique `slug` column from a source column when the slug is empty.
 *
 * @mixin Model
 */
trait GeneratesSlug
{
    public static function bootGeneratesSlug(): void
    {
        static::saving(function (Model $model): void {
            /** @var Model&GeneratesSlug $model */
            $slug = Str::slug((string) ($model->slug ?: $model->getAttribute($model->slugSourceColumn())));

            $model->slug = $model->uniqueSlug($slug !== '' ? $slug : Str::lower(Str::random(8)));
        });
    }

    /**
     * The column used to build the slug.
     */
    public function slugSourceColumn(): string
    {
        return 'title';
    }

    protected function uniqueSlug(string $slug): string
    {
        $candidate = $slug;
        $suffix = 2;

        while (
            static::query()
                ->where('slug', $candidate)
                ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
                ->exists()
        ) {
            $candidate = "{$slug}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
