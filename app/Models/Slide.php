<?php

namespace App\Models;

use Database\Factories\SlideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'image', 'link_url', 'sort_order', 'is_active', 'start_date', 'end_date'])]
class Slide extends Model
{
    /** @use HasFactory<SlideFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Active slides whose display window includes today.
     */
    #[Scope]
    protected function showing(Builder $query): void
    {
        $today = today()->toDateString();

        $query->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today))
            ->where(fn (Builder $query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today));
    }
}
