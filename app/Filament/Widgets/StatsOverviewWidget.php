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
            Stat::make('Artikel Terbit', Post::query()->where('status', PostStatus::Published)->count())
                ->description('Total artikel: '.Post::query()->count())
                ->color('success'),
            Stat::make('Halaman Statis', Page::query()->count())
                ->description('Halaman profil instansi')
                ->color('primary'),
            Stat::make('Pertanyaan Menunggu', Question::query()->where('status', QuestionStatus::Pending)->count())
                ->description('Dijawab: '.Question::query()->where('status', QuestionStatus::Answered)->count())
                ->color('warning'),
            Stat::make('FAQ Aktif', Faq::query()->active()->count())
                ->description('Pertanyaan sering diajukan')
                ->color('info'),
        ];
    }
}
