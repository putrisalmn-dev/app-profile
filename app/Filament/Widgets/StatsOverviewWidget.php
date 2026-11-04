<?php

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Enums\QuestionStatus;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\Question;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(__('Published Posts'), Post::query()->where('status', PostStatus::Published)->count())
                ->description(__('Total posts: :count', ['count' => Post::query()->count()]))
                ->color('success'),
            Stat::make(__('Static Pages'), Page::query()->count())
                ->description(__('Organization profile pages'))
                ->color('primary'),
            Stat::make(__('Pending Questions'), Question::query()->where('status', QuestionStatus::Pending)->count())
                ->description(__('Answered: :count', ['count' => Question::query()->where('status', QuestionStatus::Answered)->count()]))
                ->color('warning'),
            Stat::make(__('Active FAQs'), Faq::query()->active()->count())
                ->description(__('Frequently asked questions'))
                ->color('info'),
        ];
    }
}
